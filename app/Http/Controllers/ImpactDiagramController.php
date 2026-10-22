<?php

namespace App\Http\Controllers;

use App\Services\ImpactDiagram\ImpactDiagramBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Illuminate\View\View;

class ImpactDiagramController extends Controller
{
    /**
     * Display the impact diagram (relations graph) starting from an entity.
     */
    public function __invoke(string $entityType, int $entity, ImpactDiagramBuilder $builder): View
    {
        /** @var Model $root */
        $root = $builder->resolveEntity($entityType, $entity);

        return view('pages.impact-diagram.index', [
            'graph' => Js::from($builder->build($root)),
            'rootLabel' => (string) $root->getAttribute('name'),
        ]);
    }
}
