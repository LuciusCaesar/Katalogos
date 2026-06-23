<?php

use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all data initiatives', function () {
    Sanctum::actingAs($this->user);

    DataInitiative::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/data-initiatives');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a data initiative', function () {
    Sanctum::actingAs($this->user);

    $data = [
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
        'description' => 'Test Description',
    ];

    $response = $this->postJson('/api/v1/data-initiatives', $data);

    $response->assertStatus(201)
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('data_initiatives', $data);
});

it('validates create request', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/data-initiatives', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['code', 'label']);
});

it('shows a data initiative', function () {
    Sanctum::actingAs($this->user);

    $dataInitiative = DataInitiative::factory()->create();

    $response = $this->getJson('/api/v1/data-initiatives/'.$dataInitiative->id);

    $response->assertStatus(200)
        ->assertJson(['data' => $dataInitiative->toArray()]);
});

it('updates a data initiative', function () {
    Sanctum::actingAs($this->user);

    $dataInitiative = DataInitiative::factory()->create();

    $updatedData = [
        'label' => 'Updated Label',
        'description' => 'Updated Description',
    ];

    $response = $this->putJson('/api/v1/data-initiatives/'.$dataInitiative->id, $updatedData);

    $response->assertStatus(200)
        ->assertJson(['data' => $updatedData]);

    $this->assertDatabaseHas('data_initiatives', $updatedData);
});

it('deletes a data initiative', function () {
    Sanctum::actingAs($this->user);
    $dataInitiative = DataInitiative::factory()->create();

    $response = $this->deleteJson('/api/v1/data-initiatives/'.$dataInitiative->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('data_initiatives', ['id' => $dataInitiative->id]);
});

it('requires authentication', function () {
    // Don't call Sanctum::actingAs() - test unauthenticated access
    // Clear any authenticated user
    auth()->forgetGuards();

    $response = $this->getJson('/api/v1/data-initiatives');

    $response->assertStatus(401);
});

it('gets governance score history for a data initiative', function () {
    Sanctum::actingAs($this->user);

    $initiative = DataInitiative::factory()->create();
    $history = DataInitiativeGovernanceScoreHistory::factory()->count(3)->create([
        'data_initiative_id' => $initiative->id,
    ]);

    $response = $this->getJson('/api/v1/data-initiatives/'.$initiative->id.'/governance-score-history');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'data_initiative_id',
                    'score',
                    'event',
                    'calculated_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});
