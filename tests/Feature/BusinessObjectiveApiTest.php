<?php

use App\Models\BusinessObjective;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all business objectives', function () {
    Sanctum::actingAs($this->user);

    BusinessObjective::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/business-objectives');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a business objective', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Increase Revenue',
        'description' => 'Increase company revenue by 20%',
    ];

    $response = $this->postJson('/api/v1/business-objectives', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_objectives', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/business-objectives', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a business objective', function () {
    Sanctum::actingAs($this->user);

    $businessObjective = BusinessObjective::factory()->create();

    $response = $this->getJson('/api/v1/business-objectives/'.$businessObjective->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $businessObjective->toArray()]);
});

it('updates a business objective', function () {
    Sanctum::actingAs($this->user);

    $businessObjective = BusinessObjective::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/business-objectives/'.$businessObjective->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('business_objectives', $updatedData);
});

it('deletes a business objective', function () {
    Sanctum::actingAs($this->user);
    $businessObjective = BusinessObjective::factory()->create();

    $response = $this->deleteJson('/api/v1/business-objectives/'.$businessObjective->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('business_objectives', ['id' => $businessObjective->id]);
});

it('requires authentication', function () {
    // Don't call Sanctum::actingAs() - test unauthenticated access
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/business-objectives');

    $response->assertStatus(401);
});
