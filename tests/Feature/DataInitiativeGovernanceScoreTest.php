<?php

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\User;
use App\Services\DataInitiativeGovernanceProjection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->projection = app(DataInitiativeGovernanceProjection::class);
});

// A factory business asset has name + definition + domain + data initiative,
// so its baseline score is 6.0 achieved points out of 14.5 possible (ratio 6/14.5).

it('computes the average ratio and total points of member assets', function () {
    $initiative = DataInitiative::factory()->create();
    $asset1 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule = BusinessRule::factory()->create();
    $asset1->businessRules()->attach($rule->id);
    $asset1->calculateGovernanceScore(); // 8.0 points
    $asset2 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]); // 6.0 points

    $metrics = $this->projection->metrics($initiative);

    expect($metrics['scored_assets'])->toBe(2);
    expect($metrics['total_assets'])->toBe(2);
    expect($metrics['total_points'])->toEqualWithDelta(14.0, 1e-9);
    expect($metrics['max_possible_points'])->toEqualWithDelta(29.0, 1e-9);
    expect($metrics['average'])->toEqualWithDelta(((8.0 / 14.5) + (6.0 / 14.5)) / 2, 1e-9);
});

it('returns null average and zero points for an empty initiative', function () {
    $initiative = DataInitiative::factory()->create();

    $metrics = $this->projection->metrics($initiative);

    expect($metrics['average'])->toBeNull();
    expect($metrics['total_points'])->toBe(0.0);
    expect($metrics['scored_assets'])->toBe(0);
    expect($metrics['total_assets'])->toBe(0);
});

it('ignores assets that do not belong to the initiative', function () {
    $initiative = DataInitiative::factory()->create();
    BusinessAsset::factory()->create(); // belongs to another initiative

    $metrics = $this->projection->metrics($initiative);

    expect($metrics['total_assets'])->toBe(0);
    expect($metrics['average'])->toBeNull();
});

it('rebuilds the initiative timeline from the asset score log', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $history = $this->projection->history($initiative);

    expect($history)->toHaveCount(1);
    expect($history[0]['scored_assets'])->toBe(1);
    expect($history[0]['total_points'])->toEqualWithDelta(6.0, 1e-9);
    expect($history[0]['average'])->toEqualWithDelta(6.0 / 14.5, 1e-9);
    expect($history[0]['business_asset_id'])->toBe($asset->id);
    expect($history[0]['event'])->toContain('Business Asset #'.$asset->id);
    expect($history[0]['event'])->toContain('created');
});

it('emits a new timeline point only when the aggregate changes', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // Recalculation without any change → no new score row → no new point
    $asset->calculateGovernanceScore();
    expect($this->projection->history($initiative))->toHaveCount(1);

    // Actual change → new point, most recent first
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    $history = $this->projection->history($initiative);

    expect($history)->toHaveCount(2);
    expect($history[0]['total_points'])->toEqualWithDelta(8.0, 1e-9);
    expect($history[0]['event'])->toContain('business_rule_attached');
    expect($history[1]['total_points'])->toEqualWithDelta(6.0, 1e-9);
});

it('tracks membership changes across initiatives in the timeline', function () {
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative1->id]);

    // Reassigning the asset fires a new score row snapshotting the new initiative
    $asset->update(['data_initiative_id' => $initiative2->id]);

    $history1 = $this->projection->history($initiative1);
    $history2 = $this->projection->history($initiative2);

    // initiative1: the asset joined, then left (aggregate back to empty)
    expect($history1)->toHaveCount(2);
    expect($history1[0]['scored_assets'])->toBe(0);
    expect($history1[0]['average'])->toBeNull();
    expect($history1[1]['scored_assets'])->toBe(1);

    // initiative2: a single point with the asset
    expect($history2)->toHaveCount(1);
    expect($history2[0]['scored_assets'])->toBe(1);
    expect($history2[0]['total_points'])->toEqualWithDelta(6.0, 1e-9);
});

it('averages across multiple assets and emits a point per membership change', function () {
    $initiative = DataInitiative::factory()->create();
    BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $history = $this->projection->history($initiative);

    expect($history)->toHaveCount(2);
    expect($history[0]['scored_assets'])->toBe(2);
    expect($history[1]['scored_assets'])->toBe(1);
    expect($history[0]['average'])->toEqualWithDelta(6.0 / 14.5, 1e-9);
});

it('is resilient to replayed requests duplicating a score calculation', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    // A real score change, then the same calculation replayed (double submission):
    // the second run sees the same score as the latest row and appends nothing.
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);
    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    expect($asset->governanceScores()->count())->toBe(2);
    expect($this->projection->history($initiative))->toHaveCount(2);
});

it('api returns computed governance metrics', function () {
    Sanctum::actingAs($this->user);

    $initiative = DataInitiative::factory()->create();
    BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $response = $this->getJson("/api/v1/data-initiatives/{$initiative->id}");

    $response->assertStatus(200);

    $data = $response->json('data');
    expect($data['average_governance_score'])->toEqualWithDelta(6.0 / 14.5, 1e-9);
    expect($data['total_governance_score'])->toEqualWithDelta(6.0, 1e-9);
    expect($data['max_governance_score'])->toEqualWithDelta(14.5, 1e-9);
});

it('api returns the projected governance score history', function () {
    Sanctum::actingAs($this->user);

    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $response = $this->getJson("/api/v1/data-initiatives/{$initiative->id}/governance-score-history");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'calculated_at',
                    'average',
                    'total_points',
                    'max_possible_points',
                    'scored_assets',
                    'business_asset_id',
                    'business_asset',
                    'event',
                ],
            ],
        ]);

    $point = $response->json('data.0');
    expect($point['event'])->toContain('Business Asset #'.$asset->id);
    expect((float) $point['total_points'])->toEqualWithDelta(6.0, 1e-9);
});
