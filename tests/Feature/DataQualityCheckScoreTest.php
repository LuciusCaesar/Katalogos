<?php

use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Score creation tests - all 3 field combinations

it('can create a score with rows_passed and rows_failed', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 80,
            'rows_failed' => 20,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 80,
        'rows_failed' => 20,
        'total_rows' => 100,
        'score' => 0.8,
        'origin_type' => 'user',
        'origin_id' => $this->user->id,
    ]);
});

it('can create a score with rows_passed and total_rows', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 75,
            'total_rows' => 100,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 75,
        'rows_failed' => 25,
        'total_rows' => 100,
        'score' => 0.75,
    ]);
});

it('can create a score with rows_failed and total_rows', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_failed' => 30,
            'total_rows' => 100,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 70,
        'rows_failed' => 30,
        'total_rows' => 100,
        'score' => 0.7,
    ]);
});

// Validation tests

it('cannot create a score with only one field', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 80,
        ]);

    $response->assertSessionHasErrors(['rows']);
});

it('cannot create a score with no fields', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), []);

    $response->assertSessionHasErrors(['rows']);
});

it('validates that all three fields are consistent when provided', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 80,
            'rows_failed' => 20,
            'total_rows' => 110,
        ]);

    $response->assertSessionHasErrors(['total_rows']);
});

it('can create a score with all three fields when they are consistent', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 80,
            'rows_failed' => 20,
            'total_rows' => 100,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 80,
        'rows_failed' => 20,
        'total_rows' => 100,
        'score' => 0.8,
    ]);
});

it('validates that total_rows cannot be less than rows_passed', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 80,
            'total_rows' => 50,
        ]);

    $response->assertSessionHasErrors(['rows_passed']);
});

it('validates that total_rows cannot be less than rows_failed', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_failed' => 80,
            'total_rows' => 50,
        ]);

    $response->assertSessionHasErrors(['rows_failed']);
});

it('validates that all fields must be positive integers', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => -10,
            'rows_failed' => 20,
        ]);

    $response->assertSessionHasErrors(['rows_passed']);
});

it('validates that rows_failed cannot be negative when calculated', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 100,
            'total_rows' => 50,
        ]);

    $response->assertSessionHasErrors(['rows_passed']);
});

it('validates that rows_passed cannot be negative when calculated', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_failed' => 100,
            'total_rows' => 50,
        ]);

    $response->assertSessionHasErrors(['rows_failed']);
});

// Relationship tests

it('data quality check has many scores', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->count(3)->create([
        'data_quality_check_id' => $dqc->id,
    ]);

    expect($dqc->scores()->count())->toBe(3);
});

it('can get latest score', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 50,
        'rows_failed' => 50,
        'total_rows' => 100,
        'score' => 0.5,
        'created_at' => now()->subDay(),
    ]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 900,
        'rows_failed' => 100,
        'total_rows' => 1000,
        'score' => 0.9,
        'created_at' => now(),
    ]);

    expect(abs((float) $dqc->latestScore->score - 0.9) < 0.0001)->toBeTrue();
});

// Accessor tests

it('formats score percentage correctly', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    $score = DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'score' => 0.85,
    ]);

    expect($score->score_percentage)->toBe('85.00%');
});

it('score percentage accessor on score model works', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    $score = DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'score' => 0.65,
    ]);

    expect($score->score_percentage)->toBe('65.00%');
    expect($dqc->latestScore->score_percentage)->toBe('65.00%');
});

it('rows_failed is accessible from latest score', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => 70,
        'rows_failed' => 30,
        'total_rows' => 100,
    ]);

    expect($dqc->latestScore->rows_failed)->toBe(30);
});

it('latest score is null when no scores exist', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    expect($dqc->latestScore)->toBeNull();
});

// Edge cases

it('handles zero total rows', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 0,
            'rows_failed' => 0,
        ]);

    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'score' => 0,
    ]);
});

it('handles perfect score', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 100,
            'rows_failed' => 0,
        ]);

    $this->assertDatabaseHas('data_quality_check_scores', [
        'data_quality_check_id' => $dqc->id,
        'score' => 1.0,
    ]);
});

// Score history view tests

it('displays score history page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->count(3)->create([
        'data_quality_check_id' => $dqc->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.scores.index', $dqc))
        ->assertStatus(200)
        ->assertViewIs('pages.data-quality-checks.scores.index')
        ->assertSee(__('Score History'));
});

it('displays empty state when no scores exist', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.scores.index', $dqc))
        ->assertStatus(200)
        ->assertSee(__('No scores recorded yet.'));
});

// API tests

it('can store score via api', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->actingAs($this->user)
        ->postJson(route('api.data-quality-checks.scores.store', $dqc), [
            'rows_passed' => 90,
            'rows_failed' => 10,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.score', '0.9000')
        ->assertJsonPath('data.score_percentage', '90.00%');
});

it('can fetch score history via api', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->count(3)->create([
        'data_quality_check_id' => $dqc->id,
    ]);

    $this->actingAs($this->user)
        ->getJson(route('api.data-quality-checks.scores.index', $dqc))
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

// Factory tests

it('factory creates valid scores', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    $rowsPassed = fake()->numberBetween(1, 1000);
    $rowsFailed = fake()->numberBetween(1, 1000);
    $totalRows = $rowsPassed + $rowsFailed;
    $expectedScore = $rowsPassed / $totalRows;

    $score = DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $dqc->id,
        'rows_passed' => $rowsPassed,
        'rows_failed' => $rowsFailed,
        'total_rows' => $totalRows,
        'score' => $expectedScore,
    ]);

    expect($score->rows_passed + $score->rows_failed)->toBe($score->total_rows);
    expect(abs((float) $score->score - $expectedScore) < 0.0001)->toBeTrue();
});

it('system origin factory state works', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    $score = DataQualityCheckScore::factory()->for($dqc)->system()->create();

    expect($score->origin_type)->toBe('system');
    expect($score->origin_id)->toBeNull();
    expect($score->origin_name)->toBe('System');
});

it('scheduled origin factory state works', function () {
    $businessRule = BusinessRule::factory()->create();
    $dqc = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    $score = DataQualityCheckScore::factory()->for($dqc)->scheduled()->create();

    expect($score->origin_type)->toBe('scheduled');
    expect($score->origin_id)->toBeNull();
    expect($score->origin_name)->toBe('Scheduled Job');
});
