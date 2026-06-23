<?php

namespace App\Livewire;

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\RootCause;
use App\Models\Solution;
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
        return view('livewire.dashboard-component');
    }
}
