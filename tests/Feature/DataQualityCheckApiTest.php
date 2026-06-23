<?php

use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all data quality checks', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    DataQualityCheck::factory()->count(3)->create(['business_rule_id' => $businessRule->id]);

    $response = $this->getJson('/api/v1/data-quality-checks');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a data quality check', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    $data = [
        'name' => 'Test Data Quality Check',
        'description' => 'Test Description',
        'business_rule_id' => $businessRule->id,
    ];

    $response = $this->postJson('/api/v1/data-quality-checks', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_quality_checks', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/data-quality-checks', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'business_rule_id']);
});

it('shows a data quality check', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->getJson('/api/v1/data-quality-checks/'.$dataQualityCheck->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $dataQualityCheck->toArray()]);
});

it('updates a data quality check', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'business_rule_id' => $businessRule->id,
    ];

    $response = $this->putJson('/api/v1/data-quality-checks/'.$dataQualityCheck->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_quality_checks', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->putJson('/api/v1/data-quality-checks/'.$dataQualityCheck->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a data quality check', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $response = $this->deleteJson('/api/v1/data-quality-checks/'.$dataQualityCheck->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('data_quality_checks', ['id' => $dataQualityCheck->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/data-quality-checks');

    $response->assertStatus(401);
});
