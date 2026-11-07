<?php

namespace App\Livewire;

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
use App\Services\DataInitiativeGovernanceProjection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class DashboardComponent extends Component
{
    public int $dataInitiativesCount;

    public int $businessAssetsCount;

    public int $businessRulesCount;

    public int $dataIssuesCount;

    public int $rootCausesCount;

    public int $solutionsCount;

    public function mount(): void
    {
        $this->dataInitiativesCount = DataInitiative::count();
        $this->businessAssetsCount = BusinessAsset::count();
        $this->businessRulesCount = BusinessRule::count();
        $this->dataIssuesCount = DataIssue::count();
        $this->rootCausesCount = RootCause::count();
        $this->solutionsCount = Solution::count();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.dashboard-component', [
            'myResponsibilities' => $this->myResponsibilities(),
            'needsAttention' => $this->needsAttention(),
            'recentlyUpdated' => $this->recentlyUpdated(),
        ]);
    }

    /**
     * Entities the authenticated user holds a role on, with that role's label
     * and the governance score where one exists.
     *
     * @return Collection<int, array{entity: Model, type: string, routeName: string, role: string, score: float|null}>
     */
    private function myResponsibilities(): Collection
    {
        $userId = auth()->id();

        if ($userId === null) {
            return collect();
        }

        $assignedTo = fn (Builder $query): Builder => $query->whereHas(
            'roleAssignments',
            fn (Builder $sub): Builder => $sub->where('user_id', $userId)
        );

        $roleOf = fn (Model $entity): string => $entity->roleAssignments
            ->where('user_id', $userId)
            ->map(fn ($assignment) => $assignment->role?->name)
            ->filter()
            ->unique()
            ->implode(', ');

        $projection = app(DataInitiativeGovernanceProjection::class);

        $assets = BusinessAsset::query()
            ->where($assignedTo)
            ->with(['governanceScore', 'roleAssignments.role'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (BusinessAsset $asset): array => [
                'entity' => $asset,
                'type' => __('Business Asset'),
                'routeName' => 'web.business-assets.show',
                'role' => $roleOf($asset),
                'score' => $asset->governanceScore?->score,
            ]);

        $initiatives = DataInitiative::query()
            ->where($assignedTo)
            ->with(['roleAssignments.role'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function (DataInitiative $initiative) use ($roleOf, $projection): array {
                return [
                    'entity' => $initiative,
                    'type' => __('Data Initiative'),
                    'routeName' => 'web.data-initiatives.show',
                    'role' => $roleOf($initiative),
                    'score' => $projection->metrics($initiative)['average'],
                ];
            });

        $domains = Domain::query()
            ->where($assignedTo)
            ->with(['roleAssignments.role'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Domain $domain): array => [
                'entity' => $domain,
                'type' => __('Domain'),
                'routeName' => 'web.domains.show',
                'role' => $roleOf($domain),
                'score' => null,
            ]);

        $sources = DataSource::query()
            ->where($assignedTo)
            ->with(['roleAssignments.role'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (DataSource $source): array => [
                'entity' => $source,
                'type' => __('Data Source'),
                'routeName' => 'web.data-sources.show',
                'role' => $roleOf($source),
                'score' => null,
            ]);

        return $assets
            ->merge($initiatives)
            ->merge($domains)
            ->merge($sources)
            ->sortByDesc(fn (array $row): string => $row['entity']->updated_at->toDateTimeString())
            ->take(10)
            ->values();
    }

    /**
     * Data issues affecting the user's assets, and the user's assets whose
     * governance score fell below the healthy threshold.
     *
     * @return Collection<int, array{entity: Model, type: string, routeName: string, reason: string}>
     */
    private function needsAttention(): Collection
    {
        $userId = auth()->id();

        if ($userId === null) {
            return collect();
        }

        $myAssets = fn (Builder $query): Builder => $query->whereHas(
            'roleAssignments',
            fn (Builder $sub): Builder => $sub->where('user_id', $userId)
        );

        $issues = DataIssue::query()
            ->whereHas('businessAssets', $myAssets)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (DataIssue $issue): array => [
                'entity' => $issue,
                'type' => __('Data Issue'),
                'routeName' => 'web.data-issues.show',
                'reason' => __('Affects your assets'),
            ]);

        $lowScoreAssets = BusinessAsset::query()
            ->where($myAssets)
            ->whereHas('governanceScore', fn (Builder $query): Builder => $query->where('score', '<', 0.5))
            ->with('governanceScore')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (BusinessAsset $asset): array => [
                'entity' => $asset,
                'type' => __('Business Asset'),
                'routeName' => 'web.business-assets.show',
                'reason' => __('Governance score below 50%'),
            ]);

        return $issues
            ->merge($lowScoreAssets)
            ->sortByDesc(fn (array $row): string => $row['entity']->updated_at->toDateTimeString())
            ->take(10)
            ->values();
    }

    /**
     * Most recently updated entities across every catalog type.
     *
     * @return Collection<int, array{entity: Model, type: string, routeName: string}>
     */
    private function recentlyUpdated(): Collection
    {
        $types = [
            ['model' => BusinessAsset::class, 'type' => __('Business Asset'), 'routeName' => 'web.business-assets.show'],
            ['model' => DataInitiative::class, 'type' => __('Data Initiative'), 'routeName' => 'web.data-initiatives.show'],
            ['model' => Domain::class, 'type' => __('Domain'), 'routeName' => 'web.domains.show'],
            ['model' => BusinessObjective::class, 'type' => __('Business Objective'), 'routeName' => 'web.business-objectives.show'],
            ['model' => KPI::class, 'type' => __('KPI'), 'routeName' => 'web.kpis.show'],
            ['model' => BusinessRule::class, 'type' => __('Business Rule'), 'routeName' => 'web.business-rules.show'],
            ['model' => Regulation::class, 'type' => __('Regulation'), 'routeName' => 'web.regulations.show'],
            ['model' => DataQualityCheck::class, 'type' => __('Data Quality Check'), 'routeName' => 'web.data-quality-checks.show'],
            ['model' => DataIssue::class, 'type' => __('Data Issue'), 'routeName' => 'web.data-issues.show'],
            ['model' => RootCause::class, 'type' => __('Root Cause'), 'routeName' => 'web.root-causes.show'],
            ['model' => Solution::class, 'type' => __('Solution'), 'routeName' => 'web.solutions.show'],
            ['model' => DataSource::class, 'type' => __('Data Source'), 'routeName' => 'web.data-sources.show'],
            ['model' => BusinessProcess::class, 'type' => __('Business Process'), 'routeName' => 'web.business-processes.show'],
            ['model' => BusinessEvent::class, 'type' => __('Business Event'), 'routeName' => 'web.business-events.show'],
        ];

        return collect($types)
            ->flatMap(fn (array $type): Collection => $type['model']::query()
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(fn (Model $entity): array => [
                    'entity' => $entity,
                    'type' => $type['type'],
                    'routeName' => $type['routeName'],
                ]))
            ->sortByDesc(fn (array $row): string => $row['entity']->updated_at->toDateTimeString())
            ->take(5)
            ->values();
    }
}
