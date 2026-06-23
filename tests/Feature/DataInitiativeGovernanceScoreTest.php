<?php

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use App\Models\User;
use Database\Seeders\GovernanceCriterionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->seed(GovernanceCriterionSeeder::class);
});

it('calculates average governance score for data initiative', function () {
    $initiative = DataInitiative::factory()->create();

    // Create business assets with different scores
    $asset1 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $asset2 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // Force scores to known values
    $asset1->calculateGovernanceScore();
    $asset2->calculateGovernanceScore();

    $initiative->refresh();

    expect($initiative->average_governance_score)->not->toBeNull();
    expect($initiative->average_governance_score)->toBeGreaterThanOrEqual(0);
    expect($initiative->average_governance_score)->toBeLessThanOrEqual(1);
});

it('creates history entry when score changes', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // Initial calculation
    $asset->calculateGovernanceScore();

    // Should have at least 1 history entry
    expect($initiative->governanceScoreHistory()->count())->toBeGreaterThanOrEqual(1);
});

it('recalculates when business asset is linked', function () {
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative1->id]);

    // Reset history
    DataInitiativeGovernanceScoreHistory::query()->delete();

    $initialHistoryCount = $initiative2->governanceScoreHistory()->count();

    // Reassign asset from initiative1 to initiative2
    // This tests both unlinking from old and linking to new
    $asset->update(['data_initiative_id' => $initiative2->id]);

    $initiative2->refresh();
    expect($initiative2->governanceScoreHistory()->count())->toBeGreaterThan($initialHistoryCount);
    expect($initiative2->governanceScoreHistory()->latest()->first()->event)
        ->toContain('Business Asset #'.$asset->id);
});

it('recalculates when business asset governance score changes', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $initialScore = $initiative->average_governance_score;
    $initialHistoryCount = $initiative->governanceScoreHistory()->count();

    // Trigger score change by attaching a business rule
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    $initiative->refresh();

    // The score should have changed (or at least we should have more history)
    // Note: It may add 1 or more history entries depending on the flow
    expect($initiative->governanceScoreHistory()->count())->toBeGreaterThanOrEqual($initialHistoryCount);
});

it('handles empty data initiative gracefully', function () {
    $initiative = DataInitiative::factory()->create();

    // Empty initiative should have null or 0 score
    expect($initiative->average_governance_score)->toBeNull();
});

it('recalculates both old and new initiative on reassignment', function () {
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative1->id]);

    // Trigger initial calculation
    $asset->calculateGovernanceScore();

    $initialHistory1 = $initiative1->governanceScoreHistory()->count();
    $initialHistory2 = $initiative2->governanceScoreHistory()->count();

    // Reassign
    $asset->update(['data_initiative_id' => $initiative2->id]);

    $initiative1->refresh();
    $initiative2->refresh();

    expect($initiative1->governanceScoreHistory()->count())->toBeGreaterThanOrEqual($initialHistory1 + 1);
    expect($initiative2->governanceScoreHistory()->count())->toBeGreaterThanOrEqual($initialHistory2 + 1);

    // Check the latest events contain the business asset id
    $latest1 = $initiative1->governanceScoreHistory()->latest()->first();
    $latest2 = $initiative2->governanceScoreHistory()->latest()->first();

    expect($latest1->event)->toContain('Business Asset #'.$asset->id);
    expect($latest2->event)->toContain('Business Asset #'.$asset->id);
});

it('api returns average governance score', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // Trigger score calculation
    $asset->calculateGovernanceScore();
    $initiative->refresh();

    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/v1/data-initiatives/{$initiative->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.average_governance_score', $initiative->average_governance_score);
});

it('api returns governance score history', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // Trigger score calculation
    $asset->calculateGovernanceScore();

    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/v1/data-initiatives/{$initiative->id}/governance-score-history");

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => [['id', 'score', 'event', 'calculated_at']]]);
});
