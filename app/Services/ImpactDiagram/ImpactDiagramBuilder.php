<?php

namespace App\Services\ImpactDiagram;

use App\Models\BusinessAsset;
use App\Models\BusinessObjective;
use App\Models\BusinessProcess;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use App\Models\Domain;
use App\Models\RootCause;
use App\Models\Solution;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Builds the impact diagram graph (nodes + typed edges) for a root entity.
 *
 * The registries below drive the whole feature: adding a new diagrammable
 * entity type later only requires a new TYPES entry, the relations to
 * traverse in RELATIONS, a SHOW_ROUTES entry, a TYPE_ORDER position, and
 * the node attributes in ATTRIBUTES.
 *
 * Every type can be used as the entry point of the diagram. The payload
 * carries the BFS depth of each node from the root; the frontend uses it
 * to show only first-degree relations initially and reveal the rest
 * progressively on demand.
 */
final class ImpactDiagramBuilder
{
    /**
     * Registry of diagrammable entity types: route slug => diagram data.
     *
     * @var array<string, array{key: string, model: class-string<Model>}>
     */
    private const TYPES = [
        'business-asset' => ['key' => 'asset', 'model' => BusinessAsset::class],
        'business-rule' => ['key' => 'rule', 'model' => BusinessRule::class],
        'data-source' => ['key' => 'source', 'model' => DataSource::class],
        'data-quality-check' => ['key' => 'dq', 'model' => DataQualityCheck::class],
        'data-initiative' => ['key' => 'initiative', 'model' => DataInitiative::class],
        'business-objective' => ['key' => 'objective', 'model' => BusinessObjective::class],
        'domain' => ['key' => 'domain', 'model' => Domain::class],
        'data-issue' => ['key' => 'issue', 'model' => DataIssue::class],
        'root-cause' => ['key' => 'rootcause', 'model' => RootCause::class],
        'solution' => ['key' => 'solution', 'model' => Solution::class],
        'business-process' => ['key' => 'process', 'model' => BusinessProcess::class],
    ];

    /**
     * Relations to traverse per diagram type key. Each entry names the Eloquent
     * relation method called on the visited model and the edge orientation.
     * Pivot relations must be declared on both sides; the fixed from/to
     * orientation of each relation type deduplicates the edges.
     *
     * @var array<string, array<int, array{method: string, rel: string, label: string, from: string, to: string}>>
     */
    private const RELATIONS = [
        'asset' => [
            ['method' => 'businessRules', 'rel' => 'conform', 'label' => 'must conform to', 'from' => 'asset', 'to' => 'rule'],
            ['method' => 'dataSources', 'rel' => 'contained', 'label' => 'is contained', 'from' => 'asset', 'to' => 'source'],
            ['method' => 'businessProcesses', 'rel' => 'used-by', 'label' => 'uses', 'from' => 'process', 'to' => 'asset'],
            ['method' => 'dataIssues', 'rel' => 'affects', 'label' => 'affects', 'from' => 'issue', 'to' => 'asset'],
            ['method' => 'domain', 'rel' => 'in-domain', 'label' => 'belongs to', 'from' => 'asset', 'to' => 'domain'],
            ['method' => 'dataInitiative', 'rel' => 'in-initiative', 'label' => 'is part of', 'from' => 'asset', 'to' => 'initiative'],
        ],
        'rule' => [
            ['method' => 'businessAssets', 'rel' => 'conform', 'label' => 'must conform to', 'from' => 'asset', 'to' => 'rule'],
            ['method' => 'dataQualityChecks', 'rel' => 'checked', 'label' => 'is checked by', 'from' => 'rule', 'to' => 'dq'],
            ['method' => 'dataIssues', 'rel' => 'violates', 'label' => 'violates', 'from' => 'issue', 'to' => 'rule'],
        ],
        'source' => [
            ['method' => 'businessAssets', 'rel' => 'contained', 'label' => 'is contained', 'from' => 'asset', 'to' => 'source'],
            ['method' => 'dataQualityChecks', 'rel' => 'applies', 'label' => 'applies to', 'from' => 'dq', 'to' => 'source'],
        ],
        'dq' => [
            ['method' => 'businessRule', 'rel' => 'checked', 'label' => 'is checked by', 'from' => 'rule', 'to' => 'dq'],
            ['method' => 'dataSources', 'rel' => 'applies', 'label' => 'applies to', 'from' => 'dq', 'to' => 'source'],
        ],
        'initiative' => [
            ['method' => 'businessAssets', 'rel' => 'in-initiative', 'label' => 'is part of', 'from' => 'asset', 'to' => 'initiative'],
            ['method' => 'businessObjectives', 'rel' => 'contributes-to', 'label' => 'contributes to', 'from' => 'initiative', 'to' => 'objective'],
        ],
        'objective' => [
            ['method' => 'dataInitiatives', 'rel' => 'contributes-to', 'label' => 'contributes to', 'from' => 'initiative', 'to' => 'objective'],
        ],
        'domain' => [
            ['method' => 'businessAssets', 'rel' => 'in-domain', 'label' => 'belongs to', 'from' => 'asset', 'to' => 'domain'],
        ],
        'issue' => [
            ['method' => 'businessAssets', 'rel' => 'affects', 'label' => 'affects', 'from' => 'issue', 'to' => 'asset'],
            ['method' => 'businessRules', 'rel' => 'violates', 'label' => 'violates', 'from' => 'issue', 'to' => 'rule'],
            ['method' => 'rootCauses', 'rel' => 'caused-by', 'label' => 'is caused by', 'from' => 'issue', 'to' => 'rootcause'],
        ],
        'rootcause' => [
            ['method' => 'dataIssues', 'rel' => 'caused-by', 'label' => 'is caused by', 'from' => 'issue', 'to' => 'rootcause'],
            ['method' => 'solutions', 'rel' => 'solved-by', 'label' => 'is solved by', 'from' => 'rootcause', 'to' => 'solution'],
        ],
        'solution' => [
            ['method' => 'rootCauses', 'rel' => 'solved-by', 'label' => 'is solved by', 'from' => 'rootcause', 'to' => 'solution'],
        ],
        'process' => [
            ['method' => 'businessAssets', 'rel' => 'used-by', 'label' => 'uses', 'from' => 'process', 'to' => 'asset'],
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
        'initiative' => 'web.data-initiatives.show',
        'objective' => 'web.business-objectives.show',
        'domain' => 'web.domains.show',
        'issue' => 'web.data-issues.show',
        'rootcause' => 'web.root-causes.show',
        'solution' => 'web.solutions.show',
        'process' => 'web.business-processes.show',
    ];

    /**
     * Type display order, matching the frontend block order (computed to
     * keep related types adjacent and minimize edge crossings).
     *
     * @var array<int, string>
     */
    private const TYPE_ORDER = ['process', 'objective', 'initiative', 'domain', 'asset', 'source', 'dq', 'rule', 'issue', 'rootcause', 'solution'];

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
     * Each node carries its BFS depth from the root (0 for the root).
     *
     * @return array{rootId: string, nodes: array<int, array<string, mixed>>, edges: array<int, array<string, mixed>>}
     */
    public function build(Model $root): array
    {
        $nodes = [];
        $edges = [];
        $depths = [];
        $queue = [[$root, 0]];

        while ($queue !== []) {
            [$model, $depth] = array_shift($queue);
            $modelKey = $this->keyOf($model);
            $nodeId = $this->nodeId($modelKey, $model->getKey());

            if (array_key_exists($nodeId, $depths)) {
                continue;
            }

            $depths[$nodeId] = $depth;
            $nodes[$nodeId] = $this->makeNode($modelKey, $model, $depth);

            foreach (self::RELATIONS[$modelKey] as $relation) {
                $model->loadMissing($relation['method']);

                // Normalize the relation result: belongsTo returns a single
                // model (or null), all other relation types return a collection.
                $result = $model->{$relation['method']};
                if ($result === null) {
                    continue;
                }
                $relatedItems = $result instanceof Model ? [$result] : $result;

                foreach ($relatedItems as $related) {
                    $relatedKey = $this->keyOf($related);
                    $relatedId = $this->nodeId($relatedKey, $related->getKey());

                    if (! array_key_exists($relatedId, $depths)) {
                        $queue[] = [$related, $depth + 1];
                    }

                    $edge = $this->makeEdge($relation, $model, $related);
                    $edges[$edge['from'].'|'.$edge['to']] ??= $edge;
                }
            }
        }

        return [
            'rootId' => $this->nodeId($this->keyOf($root), $root->getKey()),
            'nodes' => $this->sortNodes($nodes, $depths),
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
     * @param  array{method: string, rel: string, label: string, from: string, to: string}  $relation
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
        ];
    }

    /**
     * Node payload for a model at a given BFS depth.
     *
     * @return array<string, mixed>
     */
    private function makeNode(string $key, Model $model, int $depth): array
    {
        return [
            'id' => $this->nodeId($key, $model->getKey()),
            'type' => $key,
            'label' => $this->entityLabel($model),
            'url' => route(self::SHOW_ROUTES[$key], $model),
            'depth' => $depth,
            'attributes' => $this->attributes($key, $model),
        ];
    }

    /**
     * Display label of a diagrammable entity.
     */
    public function entityLabel(Model $model): string
    {
        // Data initiatives are labelled by their "label" column, every other
        // entity type by its "name" column.
        $labelAttribute = $this->keyOf($model) === 'initiative' ? 'label' : 'name';

        return (string) ($model->getAttribute($labelAttribute) ?? '');
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
                'Domain' => (string) ($model->domain?->name),
                'Data Initiative' => (string) ($model->dataInitiative?->label),
            ];
        }

        if ($key === 'initiative') {
            /** @var DataInitiative $model */
            return [
                'Code' => (string) ($model->code ?? ''),
                'Label' => (string) ($model->label ?? ''),
                'Description' => (string) ($model->description ?? ''),
            ];
        }

        if ($key === 'solution') {
            /** @var Solution $model */
            return [
                'Description' => (string) ($model->description ?? ''),
                'Dimension' => $this->attributeValue($model->dimension),
                'Implementation Status' => $this->attributeValue($model->implementation_status),
            ];
        }

        if ($key === 'rootcause') {
            /** @var RootCause $model */
            $attributes = ['Description' => (string) ($model->description ?? '')];
            $attributes['Dimension'] = $this->attributeValue($model->dimension);

            return $attributes;
        }

        if ($key === 'rule') {
            /** @var BusinessRule $model */
            return [
                'Description' => (string) ($model->description ?? ''),
                'Dimension' => $this->attributeValue($model->dimension),
            ];
        }

        /**
         * BusinessObjective|Domain|DataIssue|DataSource|DataQualityCheck|BusinessProcess
         *
         * @var BusinessObjective|Domain|DataIssue|DataSource|DataQualityCheck|BusinessProcess $model
         */
        return ['Description' => (string) ($model->description ?? '')];
    }

    /**
     * Convert an attribute value (enum-cast or plain) to its display string.
     */
    private function attributeValue(mixed $value): string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return (string) ($value ?? '');
    }

    /**
     * Sort nodes by BFS depth, then display type order, then numeric id,
     * for a stable layout.
     *
     * @param  array<string, array<string, mixed>>  $nodes
     * @param  array<string, int>  $depths
     * @return array<int, array<string, mixed>>
     */
    private function sortNodes(array $nodes, array $depths): array
    {
        $typeIndexOf = static function (array $node): int {
            $index = array_search($node['type'], self::TYPE_ORDER, true);

            return $index === false ? 99 : (int) $index;
        };

        usort($nodes, static function (array $a, array $b) use ($depths, $typeIndexOf): int {
            return [$depths[$a['id']], $typeIndexOf($a), (int) explode(':', $a['id'])[1]]
                <=> [$depths[$b['id']], $typeIndexOf($b), (int) explode(':', $b['id'])[1]];
        });

        return $nodes;
    }
}
