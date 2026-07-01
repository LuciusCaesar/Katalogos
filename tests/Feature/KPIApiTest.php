<?php

use App\Models\BusinessObjective;
use App\Models\KPI;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all kpis', function () {
    Sanctum::actingAs($this->user);

    KPI::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/kpis');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a kpi', function () {
    Sanctum::actingAs($this->user);

    $businessObjective = BusinessObjective::factory()->create();

    $data = [
        'name' => 'Customer Satisfaction Score',
        'description' => 'Measure customer satisfaction on a scale of 1-10',
        'business_objective_id' => $businessObjective->id,
    ];

    $response = $this->postJson('/api/v1/kpis', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('k_p_i_s', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/kpis', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'business_objective_id']);
});

it('validates business objective exists on create', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/kpis', [
        'name' => 'Test KPI',
        'business_objective_id' => 99999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['business_objective_id']);
});

it('shows a kpi', function () {
    Sanctum::actingAs($this->user);

    $kpi = KPI::factory()->create();

    $response = $this->getJson('/api/v1/kpis/'.$kpi->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $kpi->toArray()]);
});

it('updates a kpi', function () {
    Sanctum::actingAs($this->user);

    $kpi = KPI::factory()->create();
    $businessObjective = BusinessObjective::factory()->create();

    $updatedData = [
        'name' => 'Updated KPI Name',
        'description' => 'Updated Description',
        'business_objective_id' => $businessObjective->id,
    ];

    $response = $this->putJson('/api/v1/kpis/'.$kpi->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('k_p_i_s', $updatedData);
});

it('partially updates a kpi', function () {
    Sanctum::actingAs($this->user);

    $kpi = KPI::factory()->create();

    $partialData = [
        'name' => 'Partially Updated Name',
    ];

    $response = $this->patchJson('/api/v1/kpis/'.$kpi->id, $partialData);

    $response->assertStatus(200);

    $this->assertDatabaseHas('k_p_i_s', ['id' => $kpi->id, 'name' => 'Partially Updated Name']);
});

it('deletes a kpi', function () {
    Sanctum::actingAs($this->user);
    $kpi = KPI::factory()->create();

    $response = $this->deleteJson('/api/v1/kpis/'.$kpi->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('k_p_i_s', ['id' => $kpi->id]);
});

it('requires authentication', function () {
    // Don't call Sanctum::actingAs() - test unauthenticated access
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/kpis');

    $response->assertStatus(401);
});

it('includes business objective in kpi response', function () {
    Sanctum::actingAs($this->user);

    $kpi = KPI::factory()->create();

    $response = $this->getJson('/api/v1/kpis/'.$kpi->id);

    $response->assertStatus(200)
        ->assertJsonPath('data.business_objective_id', $kpi->business_objective_id);
});
