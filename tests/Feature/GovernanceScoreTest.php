<?php

use App\Events\BusinessAssetChanged;
use App\Listeners\CalculateGovernanceScore;
use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataSource;
use App\Models\GovernanceScore;
use App\Models\User;
use App\Services\GovernanceCriteria;
use App\Services\GovernanceScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// A factory business asset has name + definition + domain + data initiative,
// so its baseline score is 6.0 achieved points out of 14.5 possible.

test('governance criteria are defined in code', function () {
    $criteria = GovernanceCriteria::all();

    expect($criteria)->toHaveCount(8);
    expect(GovernanceCriteria::DEFINITION_VERSION)->toBeInt();
    expect(GovernanceCriteria::maxPossibleScore())->toBe(14.5);

    expect(array_column($criteria, 'key'))->toContain(
        'has_name', 'has_definition', 'has_domain', 'has_data_initiative',
        'has_business_rule', 'has_data_source', 'has_data_steward', 'has_data_owner'
    );
});

test('it calculates governance score on business asset creation', function () {
    $asset = BusinessAsset::factory()->create();

    expect($asset->governanceScores()->count())->toBe(1);
    expect($asset->governanceScore)->not->toBeNull();
    expect((float) $asset->governanceScore->score)->toBeGreaterThanOrEqual(0);
    expect((float) $asset->governanceScore->score)->toBeLessThanOrEqual(1);
});

test('it stores the ratio, the absolute points and the definition snapshot', function () {
    $initiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);

    $score = $asset->governanceScore;

    expect((float) $score->achieved_score)->toBe(6.0);
    expect((float) $score->max_possible_score)->toBe(14.5);
    expect((float) $score->score)->toEqualWithDelta(6.0 / 14.5, 1e-9);
    expect($score->definition_version)->toBe(GovernanceCriteria::DEFINITION_VERSION);
    expect($score->data_initiative_id)->toBe($initiative->id);
});

test('it stores criteria results and weights', function () {
    $asset = BusinessAsset::factory()->create();

    expect($asset->governanceScore->criteria_results)->not->toBeEmpty();
    expect($asset->governanceScore->criteria_weights)->not->toBeEmpty();

    expect($asset->governanceScore->criteria_results['has_name'])->toBeTrue();
    expect($asset->governanceScore->criteria_results['has_definition'])->toBeTrue();
    expect($asset->governanceScore->criteria_results['has_data_steward'])->toBeFalse();
});

test('it does not append a score row when a relevant field changes but the score is unchanged', function () {
    $asset = BusinessAsset::factory()->create();
    expect($asset->governanceScores()->count())->toBe(1);

    // name is a criterion field, but has_name stays true
    $asset->update(['name' => 'A completely different name']);

    expect($asset->governanceScores()->count())->toBe(1);
});

test('it does not even trigger a recalculation for fields that do not affect governance', function () {
    $asset = BusinessAsset::factory()->create();
    expect($asset->governanceScores()->count())->toBe(1);

    // Categorization fields are not governance criteria
    $asset->update(['consistency' => 'high', 'integrity' => 'middle', 'availability' => 'low']);

    expect($asset->governanceScores()->count())->toBe(1);
});

test('duplicate identical updates do not duplicate the history', function () {
    // This is the original bug scenario: the same update executed twice
    // (double form submission) used to create two identical history entries.
    $asset = BusinessAsset::factory()->create();

    $asset->update(['definition' => 'First update']);
    $asset->update(['definition' => 'First update']);

    expect($asset->governanceScores()->count())->toBe(1);
});

test('it appends one row per actual score change', function () {
    $asset = BusinessAsset::factory()->create();
    expect($asset->governanceScores()->count())->toBe(1);

    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    $source = DataSource::factory()->create();
    $asset->dataSources()->attach($source->id);
    $asset->calculateGovernanceScore(['action' => 'data_source_attached']);

    expect($asset->governanceScores()->count())->toBe(3);
    expect((float) $asset->fresh()->governanceScore->achieved_score)->toBe(9.5);
});

test('it increases score when business rule is attached', function () {
    $asset = BusinessAsset::factory()->create();
    $originalScore = (float) $asset->governanceScore->score;

    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);

    $asset->calculateGovernanceScore(['action' => 'business_rule_attached']);

    $asset->refresh();
    expect((float) $asset->governanceScore->score)->toBeGreaterThan($originalScore);
    expect($asset->governanceScore->criteria_results['has_business_rule'])->toBeTrue();
});

test('reassigning the data initiative appends a row even when the score is unchanged', function () {
    // data_initiative_id is NOT NULL on business_assets: an asset can only be
    // reassigned to another initiative, never unlinked.
    $initiativeA = DataInitiative::factory()->create();
    $initiativeB = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiativeA->id]);
    expect((float) $asset->governanceScore->achieved_score)->toBe(6.0);
    expect($asset->governanceScore->data_initiative_id)->toBe($initiativeA->id);

    // has_data_initiative stays true, so the achieved score does not change...
    $asset->update(['data_initiative_id' => $initiativeB->id]);

    // ...but the membership snapshot changed, so a new row is appended
    expect($asset->governanceScores()->count())->toBe(2);
    $latest = $asset->governanceScores()->latest('calculated_at')->latest('id')->first();
    expect((float) $latest->achieved_score)->toBe(6.0);
    expect($latest->data_initiative_id)->toBe($initiativeB->id);
});

test('score history rows are immutable facts', function () {
    $asset = BusinessAsset::factory()->create();
    $first = $asset->governanceScore;
    $firstCalculatedAt = $first->calculated_at;

    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $asset->calculateGovernanceScore();

    $first->refresh();
    expect((float) $first->achieved_score)->toBe(6.0);
    expect($first->calculated_at->equalTo($firstCalculatedAt))->toBeTrue();
});

test('listener appends a score row when the asset state changed', function () {
    $asset = BusinessAsset::factory()->create();
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);

    $listener = new CalculateGovernanceScore(new GovernanceScoreService);
    $listener->handle(new BusinessAssetChanged($asset, ['action' => 'test']));

    expect($asset->governanceScores()->count())->toBe(2);
});

test('listener is idempotent when the asset state did not change', function () {
    $asset = BusinessAsset::factory()->create();

    $listener = new CalculateGovernanceScore(new GovernanceScoreService);
    $listener->handle(new BusinessAssetChanged($asset, ['action' => 'test']));
    $listener->handle(new BusinessAssetChanged($asset, ['action' => 'test']));

    expect($asset->governanceScores()->count())->toBe(1);
});

test('bulk recalculation does not duplicate unchanged scores', function () {
    BusinessAsset::factory()->count(3)->create();
    $countBefore = GovernanceScore::query()->count();

    app(GovernanceScoreService::class)->recalculateAll();

    expect(GovernanceScore::query()->count())->toBe($countBefore);
});

test('service returns the current score and the history of an asset', function () {
    $asset = BusinessAsset::factory()->create();
    $service = app(GovernanceScoreService::class);

    expect($service->getScore($asset))->not->toBeNull();
    expect($service->getHistory($asset))->toHaveCount(1);
});
