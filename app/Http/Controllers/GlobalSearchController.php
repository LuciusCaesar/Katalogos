<?php

namespace App\Http\Controllers;

use App\Models\BusinessAsset;
use App\Models\BusinessEvent;
use App\Models\BusinessProcess;
use App\Models\DataIssue;
use App\Models\DataSource;
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

        return view('pages.search.index', ['query' => $query, 'groups' => $groups]);
    }

    /**
     * Run the search across every searchable entity and build the result groups.
     *
     * @return array<int, array{title: string, routeName: string, items: array<int, Model>}>
     */
    private function search(string $query): array
    {
        $groups = [
            [
                'title' => 'Business Assets',
                'routeName' => 'web.business-assets.show',
                'items' => $this->searchBusinessAssets($query),
            ],
            [
                'title' => 'Data Issues',
                'routeName' => 'web.data-issues.show',
                'items' => DataIssue::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Root Causes',
                'routeName' => 'web.root-causes.show',
                'items' => RootCause::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Solutions',
                'routeName' => 'web.solutions.show',
                'items' => Solution::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Data Sources',
                'routeName' => 'web.data-sources.show',
                'items' => DataSource::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Processes',
                'routeName' => 'web.business-processes.show',
                'items' => BusinessProcess::search($query)->take(5)->get()->all(),
            ],
            [
                'title' => 'Business Events',
                'routeName' => 'web.business-events.show',
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
