<?php

use App\Models\DataIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all data issues', function () {
    Sanctum::actingAs($this->user);

    DataIssue::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/data-issues');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a data issue', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'name' => 'Test Data Issue',
        'description' => 'Test Description',
    ];

    $response = $this->postJson('/api/v1/data-issues', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_issues', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/data-issues', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a data issue', function () {
    Sanctum::actingAs($this->user);

    $dataIssue = DataIssue::factory()->create();

    $response = $this->getJson('/api/v1/data-issues/'.$dataIssue->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $dataIssue->toArray()]);
});

it('updates a data issue', function () {
    Sanctum::actingAs($this->user);

    $dataIssue = DataIssue::factory()->create();

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/data-issues/'.$dataIssue->id, $data);

    $response->assertStatus(200)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_issues', $data);
});

it('validates update request', function () {
    Sanctum::actingAs($this->user);

    $dataIssue = DataIssue::factory()->create();

    $response = $this->putJson('/api/v1/data-issues/'.$dataIssue->id, ['name' => '']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a data issue', function () {
    Sanctum::actingAs($this->user);

    $dataIssue = DataIssue::factory()->create();

    $response = $this->deleteJson('/api/v1/data-issues/'.$dataIssue->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('data_issues', ['id' => $dataIssue->id]);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/v1/data-issues');

    $response->assertStatus(401);
});
