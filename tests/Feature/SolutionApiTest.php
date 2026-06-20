<?php

use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all solutions', function () {
    Sanctum::actingAs($this->user);

    Solution::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/solutions');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a solution', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Solution',
        'description' => 'Test Description',
        'dimension' => 'Process',
    ];

    $response = $this->postJson('/api/v1/solutions', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('solutions', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/solutions', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'dimension']);
});

it('validates dimension is one of allowed values', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/solutions', [
        'name' => 'Test',
        'dimension' => 'Invalid',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['dimension']);
});

it('shows a solution', function () {
    Sanctum::actingAs($this->user);

    $solution = Solution::factory()->create();

    $response = $this->getJson('/api/v1/solutions/'.$solution->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $solution->toArray()]);
});

it('updates a solution', function () {
    Sanctum::actingAs($this->user);

    $solution = Solution::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'dimension' => 'People',
    ];

    $response = $this->putJson('/api/v1/solutions/'.$solution->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('solutions', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $solution = Solution::factory()->create();

    $response = $this->putJson('/api/v1/solutions/'.$solution->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'dimension']);
});

it('deletes a solution', function () {
    Sanctum::actingAs($this->user);

    $solution = Solution::factory()->create();

    $response = $this->deleteJson('/api/v1/solutions/'.$solution->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('solutions', ['id' => $solution->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/solutions');

    $response->assertStatus(401);
});
