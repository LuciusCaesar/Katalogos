<?php

use App\Models\BusinessAsset;
use App\Models\BusinessProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all business processes', function () {
    Sanctum::actingAs($this->user);

    BusinessProcess::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/business-processes');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a business process', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Order Processing',
        'description' => 'Process customer orders',
    ];

    $response = $this->postJson('/api/v1/business-processes', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_processes', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/business-processes', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a business process', function () {
    Sanctum::actingAs($this->user);

    $businessProcess = BusinessProcess::factory()->create();

    $response = $this->getJson('/api/v1/business-processes/'.$businessProcess->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $businessProcess->toArray()]);
});

it('updates a business process', function () {
    Sanctum::actingAs($this->user);

    $businessProcess = BusinessProcess::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/business-processes/'.$businessProcess->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('business_processes', $updatedData);
});

it('deletes a business process', function () {
    Sanctum::actingAs($this->user);
    $businessProcess = BusinessProcess::factory()->create();

    $response = $this->deleteJson('/api/v1/business-processes/'.$businessProcess->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('business_processes', ['id' => $businessProcess->id]);
});

it('requires authentication', function () {
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/business-processes');

    $response->assertStatus(401);
});

it('shows business assets relationship', function () {
    Sanctum::actingAs($this->user);

    $businessProcess = BusinessProcess::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $businessProcess->businessAssets()->attach([$asset1->id, $asset2->id]);

    $response = $this->getJson('/api/v1/business-processes/'.$businessProcess->id);

    $response->assertStatus(200);
    $this->assertCount(2, $businessProcess->fresh()->businessAssets);
});
