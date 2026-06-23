<?php

use App\Livewire\DashboardComponent;
use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\RootCause;
use App\Models\Solution;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('loads dashboard page successfully', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('renders the dashboard component', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire(DashboardComponent::class);
});

it('displays all KPI counts', function () {
    // Create test data with specific counts
    DataInitiative::factory()->count(3)->create();
    BusinessAsset::factory()->count(5)->create();
    BusinessRule::factory()->count(2)->create();
    DataIssue::factory()->count(4)->create();
    RootCause::factory()->count(6)->create();
    Solution::factory()->count(8)->create();

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('3') // Data Initiatives
        ->assertSee('5') // Business Assets
        ->assertSee('2') // Business Rules
        ->assertSee('4') // Data Quality Issues
        ->assertSee('6') // Root Causes
        ->assertSee('8'); // Solutions
});

it('displays section headings', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Governance')
        ->assertSee('Data Quality');
});

it('displays all KPI card labels', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Data Initiatives')
        ->assertSee('Business Assets')
        ->assertSee('Business Rules')
        ->assertSee('Data Quality Issues')
        ->assertSee('Root Causes')
        ->assertSee('Solutions');
});

it('displays zero counts when no records exist', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('0'); // All counts should be 0
});
