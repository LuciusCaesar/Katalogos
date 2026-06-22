<?php

use App\Models\BusinessRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all business rules', function () {
    Sanctum::actingAs($this->user);

    BusinessRule::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/business-rules');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a business rule', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Business Rule',
        'description' => 'Test Description',
    ];

    $response = $this->postJson('/api/v1/business-rules', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_rules', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/business-rules', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a business rule', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();

    $response = $this->getJson('/api/v1/business-rules/'.$businessRule->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $businessRule->toArray()]);
});

it('updates a business rule', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/business-rules/'.$businessRule->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_rules', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();

    $response = $this->putJson('/api/v1/business-rules/'.$businessRule->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a business rule', function () {
    Sanctum::actingAs($this->user);

    $businessRule = BusinessRule::factory()->create();

    $response = $this->deleteJson('/api/v1/business-rules/'.$businessRule->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('business_rules', ['id' => $businessRule->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/business-rules');

    $response->assertStatus(401);
});
