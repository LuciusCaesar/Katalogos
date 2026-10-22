<?php

namespace App\Services\ImpactDiagram;

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Builds the impact diagram graph (nodes + typed edges) for a root entity.
 *
 * The registry below drives the whole feature: adding a new diagrammable
 * entity type later only requires a new TYPES entry, the relations to
 * traverse in RELATIONS, and the node attributes closure in ATTRIBUTES.
 */
final class ImpactDiagramBuilder
{
    /**
     * Registry of diagrammable entity types: route slug => diagram data.
     *
     * @var array<string, array{key: string, model: class-string<Model>, label: string}>
     */
    private const TYPES = [
        'business-asset' => ['key' => 'asset', 'model' => BusinessAsset::class, 'label' => 'Business Asset'],
        'business-rule' => ['key' => 'rule', 'model' => BusinessRule::class, 'label' => 'Business Rule'],
        'data-source' => ['key' => 'source', 'model' => DataSource::class, 'label' => 'Data Source'],
        'data-quality-check' => ['key' => 'dq', 'model' => DataQualityCheck::class, 'label' => 'Data Quality Check'],
    ];

    /**
     * Relations to traverse per diagram type key. Each entry names the Eloquent
     * relation method called on the visited model and the edge orientation.
     *
     * @var array<string, array<int, array{method: string, rel: string, label: string, from: string, to: string, hidden: bool}>>
     */
    private const RELATIONS = [
        'asset' => [
            ['method' => 'businessRules', 'rel' => 'conform', 'label' => 'must conform to', 'from' => 'asset', 'to' => 'rule', 'hidden' => false],
            ['method' => 'dataSources', 'rel' => 'contained', 'label' => 'is contained', 'from' => 'asset', 'to' => 'source', 'hidden' => false],
        ],
        'rule' => [
            ['method' => 'businessAssets', 'rel' => 'conform', 'label' => 'must conform to', 'from' => 'asset', 'to' => 'rule', 'hidden' => false],
            ['method' => 'dataQualityChecks', 'rel' => 'checked', 'label' => 'is checked by', 'from' => 'rule', 'to' => 'dq', 'hidden' => true],
        ],
        'source' => [
            ['method' => 'businessAssets', 'rel' => 'contained', 'label' => 'is contained', 'from' => 'asset', 'to' => 'source', 'hidden' => false],
            ['method' => 'dataQualityChecks', 'rel' => 'applies', 'label' => 'applies to', 'from' => 'dq', 'to' => 'source', 'hidden' => true],
        ],
        'dq' => [
            ['method' => 'businessRule', 'rel' => 'checked', 'label' => 'is checked by', 'from' => 'rule', 'to' => 'dq', 'hidden' => true],
            ['method' => 'dataSources', 'rel' => 'applies', 'label' => 'applies to', 'from' => 'dq', 'to' => 'source', 'hidden' => true],
        ],
    ];

    /**
     * Show route name per diagram type key.
     *
     * @var array<string, string>
     */
    private const SHOW_ROUTES = [
        'asset' => 'web.business-assets.show',
        'rule' => 'web.business-rules.show',
        'source' => 'web.data-sources.show',
        'dq' => 'web.data-quality-checks.show',
    ];

    /**
     * Display order of type keys in the payload (columns in blocks view).
     *
     * @var array<int, string>
     */
    private const TYPE_ORDER = ['source', 'asset', 'rule', 'dq'];

    /**
     * Resolve an entity type slug and id to the root model.
     *
     * @throws ModelNotFoundException
     */
    public function resolveEntity(string $entityTypeSlug, int $entityId): Model
    {
        abort_unless(array_key_exists($entityTypeSlug, self::TYPES), 404);

        $type = self::TYPES[$entityTypeSlug];

        return $type['model']::findOrFail($entityId);
    }

    /**
     * Build the connected component graph around the given root entity.
     *
     * @return array{rootId: string, nodes: array<int, array<string, mixed>>, edges: array<int, array<string, mixed>>}
     */
    public function build(Model $root): array
    {
        $nodes = [];
        $edges = [];
        $visited = [];
        $queue = [$root];

        while ($queue !== []) {
            $model = array_shift($queue);
            $modelKey = $this->keyOf($model);
            $nodeId = $this->nodeId($modelKey, $model->getKey());

            if (isset($visited[$nodeId])) {
                continue;
            }

            $visited[$nodeId] = true;
            $nodes[$nodeId] = $this->makeNode($modelKey, $model);

            foreach (self::RELATIONS[$modelKey] as $relation) {
                $model->loadMissing($relation['method']);

                // Normalize the relation result: belongsTo returns a single
                // model, all other relation types return a collection.
                $result = $model->{$relation['method']};
                $relatedItems = $result instanceof Model ? [$result] : $result;

                foreach ($relatedItems as $related) {
                    $relatedKey = $this->keyOf($related);
                    $relatedId = $this->nodeId($relatedKey, $related->getKey());

                    if (! isset($visited[$relatedId])) {
                        $queue[] = $related;
                    }

                    $edge = $this->makeEdge($relation, $model, $related);
                    $edges[$edge['from'].'|'.$edge['to']] ??= $edge;
                }
            }
        }

        return [
            'rootId' => $this->nodeId($this->keyOf($root), $root->getKey()),
            'nodes' => $this->sortNodes($nodes),
            'edges' => array_values($edges),
        ];
    }

    /**
     * Diagram type key for a model instance.
     */
    private function keyOf(Model $model): string
    {
        foreach (self::TYPES as $type) {
            if ($model instanceof $type['model']) {
                return $type['key'];
            }
        }

        abort(500, 'Unsupported impact diagram entity: '.$model::class);
    }

    /**
     * Composite node id, e.g. "asset:12".
     */
    private function nodeId(string $key, int|string $modelId): string
    {
        return $key.':'.$modelId;
    }

    /**
     * Edge payload for a traversed relation.
     *
     * @param  array{method: string, rel: string, label: string, from: string, to: string, hidden: bool}  $relation
     * @return array<string, mixed>
     */
    private function makeEdge(array $relation, Model $model, Model $related): array
    {
        $fromModel = $this->keyOf($model) === $relation['from'] ? $model : $related;
        $toModel = $this->keyOf($model) === $relation['from'] ? $related : $model;

        return [
            'from' => $this->nodeId($relation['from'], $fromModel->getKey()),
            'to' => $this->nodeId($relation['to'], $toModel->getKey()),
            'rel' => $relation['rel'],
            'label' => $relation['label'],
            'hidden' => $relation['hidden'],
        ];
    }

    /**
     * Node payload for a model.
     *
     * @return array<string, mixed>
     */
    private function makeNode(string $key, Model $model): array
    {
        return [
            'id' => $this->nodeId($key, $model->getKey()),
            'type' => $key,
            'label' => (string) $model->getAttribute('name'),
            'url' => route(self::SHOW_ROUTES[$key], $model),
            'attributes' => $this->attributes($key, $model),
        ];
    }

    /**
     * A few main characteristics of the entity for the side panel.
     * Extend here when adding more characteristics per type.
     *
     * @return array<string, string>
     */
    private function attributes(string $key, Model $model): array
    {
        if ($key === 'asset') {
            /** @var BusinessAsset $model */
            return [
                'Definition' => (string) ($model->definition ?? ''),
                'Domain' => (string) $model->domain?->name,
                'Data Initiative' => (string) $model->dataInitiative?->label,
            ];
        }

        /** @var BusinessAsset|BusinessRule|DataSource|DataQualityCheck $model */
        $attributes = ['Description' => (string) ($model->description ?? '')];

        if ($key === 'rule') {
            /** @var BusinessRule $model */
            $attributes['Dimension'] = (string) $model->dimension->value;
        }

        return $attributes;
    }

    /**
     * Sort nodes by display type order then numeric id for a stable layout.
     *
     * @param  array<string, array<string, mixed>>  $nodes
     * @return array<int, array<string, mixed>>
     */
    private function sortNodes(array $nodes): array
    {
        return (new Collection($nodes))
            ->sortBy([
                fn (array $node) => array_search($node['type'], self::TYPE_ORDER, true),
                fn (array $node) => (int) explode(':', $node['id'])[1],
            ])
            ->values()
            ->all();
    }
}
