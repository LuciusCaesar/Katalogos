<?php

use App\Models\DataSource;
use App\Models\Role;
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

// Team Management Tests

it('returns data custodian information in data source response', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();
    $custodian = User::factory()->create();
    $dataCustodianRole = Role::where('name', 'Data Custodian')->firstOrFail();

    $dataSource->assignRoleToUser($custodian, $dataCustodianRole);

    $response = $this->getJson('/api/v1/data-sources/'.$dataSource->id);

    $response->assertStatus(200)
        ->assertJsonPath('data.data_custodian', $custodian->name)
        ->assertJsonPath('data.data_custodian_id', $custodian->id);
});

it('updates data custodian via api', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();
    $newCustodian = User::factory()->create();

    $response = $this->putJson(
        '/api/v1/data-sources/'.$dataSource->id.'/team',
        ['data_custodian_id' => $newCustodian->id]
    );

    $response->assertStatus(200)
        ->assertJsonPath('data.data_custodian', $newCustodian->name)
        ->assertJsonPath('data.data_custodian_id', $newCustodian->id);

    $this->assertDatabaseHas('role_assignments', [
        'roleable_id' => $dataSource->id,
        'roleable_type' => DataSource::class,
        'user_id' => $newCustodian->id,
    ]);
});

it('removes data custodian via api when null is provided', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();
    $oldCustodian = User::factory()->create();
    $dataCustodianRole = Role::where('name', 'Data Custodian')->firstOrFail();

    $dataSource->assignRoleToUser($oldCustodian, $dataCustodianRole);

    $response = $this->putJson(
        '/api/v1/data-sources/'.$dataSource->id.'/team',
        ['data_custodian_id' => null]
    );

    $response->assertStatus(200)
        ->assertJsonPath('data.data_custodian', null)
        ->assertJsonPath('data.data_custodian_id', null);

    $this->assertDatabaseMissing('role_assignments', [
        'roleable_id' => $dataSource->id,
        'roleable_type' => DataSource::class,
        'user_id' => $oldCustodian->id,
    ]);
});

it('validates data custodian update request', function () {
    Sanctum::actingAs($this->user);

    $dataSource = DataSource::factory()->create();

    $response = $this->putJson(
        '/api/v1/data-sources/'.$dataSource->id.'/team',
        ['data_custodian_id' => 9999] // Non-existent user
    );

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['data_custodian_id']);
});

it('requires authentication for team update', function () {
    $dataSource = DataSource::factory()->create();

    $response = $this->putJson(
        '/api/v1/data-sources/'.$dataSource->id.'/team',
        ['data_custodian_id' => 1]
    );

    $response->assertStatus(401);
});
