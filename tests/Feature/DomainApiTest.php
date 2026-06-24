<?php

use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all domains', function () {
    Sanctum::actingAs($this->user);

    Domain::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/domains');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a domain', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Domain',
        'description' => 'Test Description',
    ];

    $response = $this->postJson('/api/v1/domains', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('domains', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/domains', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('validates unique name on create', function () {
    Sanctum::actingAs($this->user);

    Domain::factory()->create(['name' => 'Existing Domain']);

    $response = $this->postJson('/api/v1/domains', [
        'name' => 'Existing Domain',
        'description' => 'Test Description',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a domain', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();

    $response = $this->getJson('/api/v1/domains/'.$domain->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $domain->toArray()]);
});

it('updates a domain', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/domains/'.$domain->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('domains', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();

    $response = $this->putJson('/api/v1/domains/'.$domain->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a domain', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();

    $response = $this->deleteJson('/api/v1/domains/'.$domain->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('domains', ['id' => $domain->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/domains');

    $response->assertStatus(401);
});

// Team Management Tests

it('returns domain owner information in domain response', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();
    $owner = User::factory()->create();
    $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

    $domain->assignRoleToUser($owner, $domainOwnerRole);

    $response = $this->getJson('/api/v1/domains/'.$domain->id);

    $response->assertStatus(200)
        ->assertJsonPath('data.domain_owner', $owner->name)
        ->assertJsonPath('data.domain_owner_id', $owner->id);
});

it('updates domain owner via api', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();
    $newOwner = User::factory()->create();

    $response = $this->putJson(
        '/api/v1/domains/'.$domain->id.'/team',
        ['domain_owner_id' => $newOwner->id]
    );

    $response->assertStatus(200)
        ->assertJsonPath('data.domain_owner', $newOwner->name)
        ->assertJsonPath('data.domain_owner_id', $newOwner->id);

    $this->assertDatabaseHas('role_assignments', [
        'roleable_id' => $domain->id,
        'roleable_type' => Domain::class,
        'user_id' => $newOwner->id,
    ]);
});

it('removes domain owner via api when null is provided', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();
    $oldOwner = User::factory()->create();
    $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

    $domain->assignRoleToUser($oldOwner, $domainOwnerRole);

    $response = $this->putJson(
        '/api/v1/domains/'.$domain->id.'/team',
        ['domain_owner_id' => null]
    );

    $response->assertStatus(200)
        ->assertJsonPath('data.domain_owner', null)
        ->assertJsonPath('data.domain_owner_id', null);

    $this->assertDatabaseMissing('role_assignments', [
        'roleable_id' => $domain->id,
        'roleable_type' => Domain::class,
        'user_id' => $oldOwner->id,
    ]);
});

it('validates domain owner update request', function () {
    Sanctum::actingAs($this->user);

    $domain = Domain::factory()->create();

    $response = $this->putJson(
        '/api/v1/domains/'.$domain->id.'/team',
        ['domain_owner_id' => 9999] // Non-existent user
    );

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['domain_owner_id']);
});

it('requires authentication for team update', function () {
    $domain = Domain::factory()->create();

    $response = $this->putJson(
        '/api/v1/domains/'.$domain->id.'/team',
        ['domain_owner_id' => 1]
    );

    $response->assertStatus(401);
});
