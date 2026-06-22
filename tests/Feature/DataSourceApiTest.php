<?php

use App\Models\DataSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all data sources', function () {
    Sanctum::actingAs($this->user);

    DataSource::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/data-sources');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a data source', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Data Source',
        'description' => 'Test Description',
    ];

    $response = $this->postJson('/api/v1/data-sources', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_sources', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/data-sources', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a data source', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();

    $response = $this->getJson('/api/v1/data-sources/'.$dataSource->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $dataSource->toArray()]);
});

it('updates a data source', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/data-sources/'.$dataSource->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_sources', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();

    $response = $this->putJson('/api/v1/data-sources/'.$dataSource->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a data source', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();

    $response = $this->deleteJson('/api/v1/data-sources/'.$dataSource->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('data_sources', ['id' => $dataSource->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/data-sources');

    $response->assertStatus(401);
});
