<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all business assets', function () {
    Sanctum::actingAs($this->user);
    
    BusinessAsset::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/business-assets');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a business asset', function () {
    Sanctum::actingAs($this->user);
    
    $dataInitiative = DataInitiative::factory()->create();
    
    $data = [
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
    ];

    $response = $this->postJson('/api/v1/business-assets', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('business_assets', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);
    
    $response = $this->postJson('/api/v1/business-assets', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'definition', 'data_initiative_id']);
});

it('shows a business asset', function () {
    Sanctum::actingAs($this->user);

    $businessAsset = BusinessAsset::factory()->create();

    $response = $this->getJson('/api/v1/business-assets/'.$businessAsset->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $businessAsset->toArray()]);
});

it('updates a business asset', function () {
    Sanctum::actingAs($this->user);
    
    $businessAsset = BusinessAsset::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'definition' => 'Updated Definition',
    ];

    $response = $this->putJson('/api/v1/business-assets/'.$businessAsset->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('business_assets', $updatedData);
});

it('deletes a business asset', function () {
    Sanctum::actingAs($this->user);
    
    $businessAsset = BusinessAsset::factory()->create();

    $response = $this->deleteJson('/api/v1/business-assets/'.$businessAsset->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('business_assets', ['id' => $businessAsset->id]);
});

it('requires authentication', function () {
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/business-assets');

    $response->assertStatus(401);
});
