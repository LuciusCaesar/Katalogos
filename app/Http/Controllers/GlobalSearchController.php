<?php

namespace App\Http\Controllers;

use App\Models\BusinessAsset;
use App\Models\BusinessEvent;
use App\Models\BusinessObjective;
use App\Models\BusinessProcess;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use App\Models\Domain;
use App\Models\KPI;
use App\Models\Regulation;
use App\Models\RootCause;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    /**
     * Entity types offered as browse shortcuts when no query is entered yet.
     * Ordered like the sidebar navigation.
     *
     * @var array<int, array{label: string, route: string}>
     */
    private const BROWSE_TYPES = [
        ['label' => 'Domains', 'route' => 'web.domains.index'],
        ['label' => 'Data Initiatives', 'route' => 'web.data-initiatives.index'],
        ['label' => 'Business Objectives', 'route' => 'web.business-objectives.index'],
        ['label' => 'KPIs', 'route' => 'web.kpis.index'],
        ['label' => 'Business Assets', 'route' => 'web.business-assets.index'],
        ['label' => 'Business Rules', 'route' => 'web.business-rules.index'],
        ['label' => 'Regulations', 'route' => 'web.regulations.index'],
        ['label' => 'Data Quality Checks', 'route' => 'web.data-quality-checks.index'],
        ['label' => 'Data Issues', 'route' => 'web.data-issues.index'],
        ['label' => 'Root Causes', 'route' => 'web.root-causes.index'],
        ['label' => 'Solutions', 'route' => 'web.solutions.index'],
        ['label' => 'Data Sources', 'route' => 'web.data-sources.index'],
        ['label' => 'Business Processes', 'route' => 'web.business-processes.index'],
        ['label' => 'Business Events', 'route' => 'web.business-events.index'],
    ];

    /**
     * Display the global search results grouped by entity type.
     */
    public function __invoke(Request $request): View
    {
        $query = Str::of((string) $request->query('q', ''))
            ->trim()
            ->replaceMatches('/[%_\\\\]/', '')
            ->limit(255, '')
            ->toString();

        $groups = $query === '' ? [] : $this->search($query);

        return view('pages.search.index', [
            'query' => $query,
            'groups' => $groups,
            'browseTypes' => self::BROWSE_TYPES,
        ]);
    }

    /**
     * Run the search across every searchable entity and build the result groups.
     * Groups are ordered like the sidebar navigation.
     *
     * @return array<int, array{title: string, routeName: string, indexRoute: string, items: array<int, Model>}>
     */
    private function search(string $query): array
    {
        $groups = [
            [
                'title' => 'Domains',
                'routeName' => 'web.domains.show',
                'indexRoute' => 'web.domains.index',
                'items' => Domain::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Data Initiatives',
                'routeName' => 'web.data-initiatives.show',
                'indexRoute' => 'web.data-initiatives.index',
                'items' => DataInitiative::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Objectives',
                'routeName' => 'web.business-objectives.show',
                'indexRoute' => 'web.business-objectives.index',
                'items' => BusinessObjective::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'KPIs',
                'routeName' => 'web.kpis.show',
                'indexRoute' => 'web.kpis.index',
                'items' => KPI::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Assets',
                'routeName' => 'web.business-assets.show',
                'indexRoute' => 'web.business-assets.index',
                'items' => $this->searchBusinessAssets($query),
            ],
            [
                'title' => 'Business Rules',
                'routeName' => 'web.business-rules.show',
                'indexRoute' => 'web.business-rules.index',
                'items' => BusinessRule::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Regulations',
                'routeName' => 'web.regulations.show',
                'indexRoute' => 'web.regulations.index',
                'items' => Regulation::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Data Quality Checks',
                'routeName' => 'web.data-quality-checks.show',
                'indexRoute' => 'web.data-quality-checks.index',
                'items' => DataQualityCheck::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Data Issues',
                'routeName' => 'web.data-issues.show',
                'indexRoute' => 'web.data-issues.index',
                'items' => DataIssue::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Root Causes',
                'routeName' => 'web.root-causes.show',
                'indexRoute' => 'web.root-causes.index',
                'items' => RootCause::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Solutions',
                'routeName' => 'web.solutions.show',
                'indexRoute' => 'web.solutions.index',
                'items' => Solution::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Data Sources',
                'routeName' => 'web.data-sources.show',
                'indexRoute' => 'web.data-sources.index',
                'items' => DataSource::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Processes',
                'routeName' => 'web.business-processes.show',
                'indexRoute' => 'web.business-processes.index',
                'items' => BusinessProcess::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Events',
                'routeName' => 'web.business-events.show',
                'indexRoute' => 'web.business-events.index',
                'items' => BusinessEvent::search($query)->take(5)->get()->all(),
            ],
        ];

        return array_values(
            array_filter(
                $groups,
                fn (array $group): bool => $group['items'] !== []
            )
        );
    }

    /**
     * Search business assets by their own attributes, additionally matching
     * assets whose assigned Data Steward or Data Owner has the given name.
     *
     * @return array<int, BusinessAsset>
     */
    private function searchBusinessAssets(string $query): array
    {
        return BusinessAsset::search($query)
            ->query(function (Builder $eloquent) use ($query): void {
                $eloquent
                    ->orWhereHas('dataSteward', fn (Builder $sub): Builder => $sub->where('name', 'like', '%'.$query.'%'))
                    ->orWhereHas('dataOwner', fn (Builder $sub): Builder => $sub->where('name', 'like', '%'.$query.'%'));
            })
            ->take(5)
            ->get()
            ->all();
    }
}
