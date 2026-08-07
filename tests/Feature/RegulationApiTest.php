<?php

use App\Models\BusinessAsset;
use App\Models\Regulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all regulations', function () {
    Sanctum::actingAs($this->user);

    Regulation::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/regulations');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a regulation', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'GDPR Compliance',
        'description' => 'General Data Protection Regulation',
        'documentation_url' => 'https://gdpr-info.eu',
    ];

    $response = $this->postJson('/api/v1/regulations', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('regulations', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/regulations', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a regulation', function () {
    Sanctum::actingAs($this->user);

    $regulation = Regulation::factory()->create();

    $response = $this->getJson('/api/v1/regulations/'.$regulation->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $regulation->toArray()]);
});

it('updates a regulation', function () {
    Sanctum::actingAs($this->user);

    $regulation = Regulation::factory()->create();

    $updatedData = [
        'name' => 'Updated Regulation Name',
        'description' => 'Updated Description',
        'documentation_url' => 'https://updated-url.com',
    ];

    $response = $this->putJson('/api/v1/regulations/'.$regulation->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('regulations', $updatedData);
});

it('deletes a regulation', function () {
    Sanctum::actingAs($this->user);
    $regulation = Regulation::factory()->create();

    $response = $this->deleteJson('/api/v1/regulations/'.$regulation->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('regulations', ['id' => $regulation->id]);
});

it('requires authentication', function () {
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/regulations');

    $response->assertStatus(401);
});

it('shows business assets relationship', function () {
    Sanctum::actingAs($this->user);

    $regulation = Regulation::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $regulation->businessAssets()->attach([$asset1->id, $asset2->id]);

    $response = $this->getJson('/api/v1/regulations/'.$regulation->id);

    $response->assertStatus(200);
    $this->assertCount(2, $regulation->fresh()->businessAssets);
});

it('syncs business assets on create', function () {
    Sanctum::actingAs($this->user);

    $asset = BusinessAsset::factory()->create();

    $data = [
        'name' => 'Test Regulation',
        'description' => 'Test Description',
        'business_asset_ids' => [$asset->id],
    ];

    $response = $this->postJson('/api/v1/regulations', $data);

    $response->assertStatus(201);

    $regulation = Regulation::where('name', 'Test Regulation')->first();
    $this->assertCount(1, $regulation->businessAssets);
    $this->assertEquals($asset->id, $regulation->businessAssets->first()->id);
});

it('syncs business assets on update', function () {
    Sanctum::actingAs($this->user);

    $regulation = Regulation::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $regulation->businessAssets()->attach($asset1->id);

    $updatedData = [
        'name' => 'Updated Regulation',
        'business_asset_ids' => [$asset2->id],
    ];

    $response = $this->putJson('/api/v1/regulations/'.$regulation->id, $updatedData);

    $response->assertStatus(200);

    $regulation->refresh();
    $this->assertCount(1, $regulation->businessAssets);
    $this->assertEquals($asset2->id, $regulation->businessAssets->first()->id);
});
