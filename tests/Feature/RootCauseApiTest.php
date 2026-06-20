<?php

use App\Models\RootCause;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all root causes', function () {
    Sanctum::actingAs($this->user);

    RootCause::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/root-causes');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a root cause', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Root Cause',
        'description' => 'Test Description',
        'dimension' => 'Process',
    ];

    $response = $this->postJson('/api/v1/root-causes', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('root_causes', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/root-causes', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'dimension']);
});

it('validates dimension is one of allowed values', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/root-causes', [
        'name' => 'Test',
        'dimension' => 'Invalid',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['dimension']);
});

it('shows a root cause', function () {
    Sanctum::actingAs($this->user);

    $rootCause = RootCause::factory()->create();

    $response = $this->getJson('/api/v1/root-causes/'.$rootCause->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $rootCause->toArray()]);
});

it('updates a root cause', function () {
    Sanctum::actingAs($this->user);

    $rootCause = RootCause::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'dimension' => 'People',
    ];

    $response = $this->putJson('/api/v1/root-causes/'.$rootCause->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('root_causes', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $rootCause = RootCause::factory()->create();

    $response = $this->putJson('/api/v1/root-causes/'.$rootCause->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'dimension']);
});

it('deletes a root cause', function () {
    Sanctum::actingAs($this->user);

    $rootCause = RootCause::factory()->create();

    $response = $this->deleteJson('/api/v1/root-causes/'.$rootCause->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('root_causes', ['id' => $rootCause->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/root-causes');

    $response->assertStatus(401);
});
