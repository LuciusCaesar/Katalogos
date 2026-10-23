<?php

use App\Models\BusinessAsset;
use App\Models\BusinessEvent;
use App\Models\BusinessProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all business events', function () {
    Sanctum::actingAs($this->user);

    BusinessEvent::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/business-events');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a business event', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Customer Registration',
        'description' => 'A customer registers on the website',
    ];

    $response = $this->postJson('/api/v1/business-events', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_events', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/business-events', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a business event', function () {
    Sanctum::actingAs($this->user);

    $businessEvent = BusinessEvent::factory()->create();

    $response = $this->getJson('/api/v1/business-events/'.$businessEvent->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $businessEvent->toArray()]);
});

it('updates a business event', function () {
    Sanctum::actingAs($this->user);

    $businessEvent = BusinessEvent::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/business-events/'.$businessEvent->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('business_events', $updatedData);
});

it('deletes a business event', function () {
    Sanctum::actingAs($this->user);
    $businessEvent = BusinessEvent::factory()->create();

    $response = $this->deleteJson('/api/v1/business-events/'.$businessEvent->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('business_events', ['id' => $businessEvent->id]);
});

it('requires authentication', function () {
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/business-events');

    $response->assertStatus(401);
});

it('shows business asset and business process relationships', function () {
    Sanctum::actingAs($this->user);

    $businessEvent = BusinessEvent::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();
    $process = BusinessProcess::factory()->create();

    $businessEvent->businessAssets()->attach([$asset1->id, $asset2->id]);
    $businessEvent->businessProcesses()->attach($process->id);

    $response = $this->getJson('/api/v1/business-events/'.$businessEvent->id);

    $response->assertStatus(200);
    $this->assertCount(2, $businessEvent->fresh()->businessAssets);
    $this->assertCount(1, $businessEvent->fresh()->businessProcesses);
});
