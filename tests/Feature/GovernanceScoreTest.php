<?php

use App\Events\BusinessAssetChanged;
use App\Listeners\CalculateGovernanceScore;
use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\Domain;
use App\Models\GovernanceCriterion;
use App\Models\User;
use App\Services\GovernanceScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->seed(GovernanceCriterionSeeder::class);
});

test('it creates governance criteria from seeder', function () {
    expect(GovernanceCriterion::count())->toBeGreaterThan(0);
    expect(GovernanceCriterion::where('key', 'has_name')->exists())->toBeTrue();
    expect(GovernanceCriterion::where('key', 'has_definition')->exists())->toBeTrue();
});

test('it calculates governance score on business asset creation', function () {
    $asset = BusinessAsset::factory()->create();

    expect($asset->governanceScores->count())->toBeGreaterThanOrEqual(1);
    expect($asset->governanceScore)->not->toBeNull();
    expect($asset->governanceScore->score)->toBeGreaterThanOrEqual(0);
    expect($asset->governanceScore->score)->toBeLessThanOrEqual(1);
});

test('it calculates score based on filled criteria', function () {
    $domain = Domain::factory()->create();
    $initiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $initiative->id,
    ]);

    expect($asset->governanceScore->score)->toBeGreaterThan(0.4);
});

test('it recalculates score when business asset is updated', function () {
    $asset = BusinessAsset::factory()->create(['definition' => 'Original']);
    $originalScore = $asset->governanceScore->score;
    $initialCount = $asset->governanceScores->count();

    $asset->update(['definition' => 'Updated Definition']);

    $asset->refresh();
    expect($asset->governanceScore->score)->toEqual($originalScore);
    expect($asset->governanceScores->count())->toBeGreaterThan($initialCount);
});

test('it stores criteria results and weights', function () {
    $asset = BusinessAsset::factory()->create();

    expect($asset->governanceScore->criteria_results)->not->toBeEmpty();
    expect($asset->governanceScore->criteria_weights)->not->toBeEmpty();

    expect($asset->governanceScore->criteria_results['has_name'])->toBeTrue();
    expect($asset->governanceScore->criteria_results['has_definition'])->toBeTrue();
});

test('it increases score when business rule is attached', function () {
    $asset = BusinessAsset::factory()->create();
    $originalScore = $asset->governanceScore->score;

    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);

    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    $asset->refresh();
    expect($asset->governanceScore->score)->toBeGreaterThan($originalScore);
    expect($asset->governanceScore->criteria_results['has_business_rule'])->toBeTrue();
});

test('listener handles business asset changed event', function () {
    $asset = BusinessAsset::factory()->create();
    $listener = new CalculateGovernanceScore(new GovernanceScoreService);

    $event = new BusinessAssetChanged($asset, ['action' => 'test']);
    $listener->handle($event);

    $asset->refresh();
    expect($asset->governanceScores->count())->toBeGreaterThan(1);
});

test('score is zero when no criteria exist', function () {
    GovernanceCriterion::query()->delete();

    $asset = BusinessAsset::factory()->create();

    expect($asset->governanceScore)->not->toBeNull();
    expect((float) $asset->governanceScore->score)->toBe(0.0);
});
