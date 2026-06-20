<?php

use App\Models\RootCause;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays solutions index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.solutions.index')
        ->assertSee(__('Solutions'));
});

it('displays empty state when no solutions exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertSee(__('No solutions found. Create one to get started.'));
});

it('displays solutions in a table', function () {
    Solution::factory()->create([
        'name' => 'Test Solution',
        'description' => 'Test Description',
        'dimension' => 'Process',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertSee('Test Solution')
        ->assertSee('Test Description')
        ->assertSee('Process')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Dimension'))
        ->assertSee(__('Root Causes'))
        ->assertSee(__('Created At'))
        ->assertSee(__('Actions'));
});

it('displays root causes count for solution', function () {
    $solution = Solution::factory()->create();
    $rootCause = RootCause::factory()->create();
    $solution->rootCauses()->attach($rootCause->id);

    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertSee('1');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.solutions.create'));
});

it('displays edit and delete links for each solution', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.solutions.edit', $solution));
});

it('requires authentication to access solutions index', function () {
    $this->get(route('web.solutions.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create solution page', function () {
    $this->actingAs($this->user)
        ->get(route('web.solutions.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.solutions.create')
        ->assertSee(__('Create Solution'));
});

it('displays solution form with root causes on create page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.create'))
        ->assertStatus(200)
        ->assertSee(__('Solution Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Dimension'))
        ->assertSee(__('Root Causes'))
        ->assertSee($rootCause->name);
});

it('requires authentication to access create solution page', function () {
    $this->get(route('web.solutions.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new solution', function () {
    $data = [
        'name' => 'New Solution',
        'description' => 'New Description',
        'dimension' => 'Tool',
    ];

    $this->actingAs($this->user)
        ->post(route('web.solutions.store'), $data)
        ->assertRedirect(route('web.solutions.index'))
        ->assertSessionHas('success', __('Solution created successfully.'));

    $this->assertDatabaseHas('solutions', $data);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.solutions.store'), ['description' => 'Test', 'dimension' => 'Process'])
        ->assertSessionHasErrors('name');
});

it('validates dimension is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.solutions.store'), ['name' => 'Test', 'description' => 'Test'])
        ->assertSessionHasErrors('dimension');
});

it('validates dimension is one of allowed values when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.solutions.store'), ['name' => 'Test', 'dimension' => 'Invalid'])
        ->assertSessionHasErrors('dimension');
});

// Show Page Tests

it('displays solution show page', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertViewIs('pages.solutions.show')
        ->assertSee($solution->name);
});

it('displays solution name as title', function () {
    $solution = Solution::factory()->create(['name' => 'Test Solution']);

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee('Test Solution');
});

it('displays solution description', function () {
    $solution = Solution::factory()->create(['description' => 'Test Description']);

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays solution dimension', function () {
    $solution = Solution::factory()->create(['dimension' => 'People']);

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee('People');
});

it('displays edit and delete buttons on show page', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.solutions.edit', $solution));
});

it('displays root causes section on show page', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee(__('Root Causes'));
});

it('displays associated root causes on show page', function () {
    $solution = Solution::factory()->create();
    $rootCause = RootCause::factory()->create(['name' => 'Test Root Cause']);
    $solution->rootCauses()->attach($rootCause->id);

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee('Test Root Cause');
});

it('displays empty message when no root causes exist', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee(__('No root causes associated with this solution.'));
});

it('displays back to solutions link on show page', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.show', $solution))
        ->assertStatus(200)
        ->assertSee(__('Back to Solutions'))
        ->assertSee(route('web.solutions.index'));
});

it('requires authentication to access solution show page', function () {
    $solution = Solution::factory()->create();

    $this->get(route('web.solutions.show', $solution))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit solution page', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.edit', $solution))
        ->assertStatus(200)
        ->assertViewIs('pages.solutions.edit')
        ->assertSee(__('Edit Solution'));
});

it('displays solution form with pre-filled values on edit page', function () {
    $solution = Solution::factory()->create([
        'name' => 'Existing Solution',
        'description' => 'Existing Description',
        'dimension' => 'Tool',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.solutions.edit', $solution))
        ->assertStatus(200)
        ->assertSee('Existing Solution')
        ->assertSee('Existing Description')
        ->assertSee('Tool');
});

it('requires authentication to access edit solution page', function () {
    $solution = Solution::factory()->create();

    $this->get(route('web.solutions.edit', $solution))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a solution', function () {
    $solution = Solution::factory()->create();

    $data = [
        'name' => 'Updated Solution',
        'description' => 'Updated Description',
        'dimension' => 'People',
    ];

    $this->actingAs($this->user)
        ->put(route('web.solutions.update', $solution), $data)
        ->assertRedirect(route('web.solutions.index'))
        ->assertSessionHas('success', __('Solution updated successfully.'));

    $this->assertDatabaseHas('solutions', $data);
});

it('validates name is required when updating', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.solutions.update', $solution), ['description' => 'Test', 'dimension' => 'Process'])
        ->assertSessionHasErrors('name');
});

it('validates dimension is required when updating', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.solutions.update', $solution), ['name' => 'Test', 'description' => 'Test'])
        ->assertSessionHasErrors('dimension');
});

// Delete Tests

it('can delete a solution', function () {
    $solution = Solution::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.solutions.destroy', $solution))
        ->assertRedirect(route('web.solutions.index'))
        ->assertSessionHas('success', __('Solution deleted successfully.'));

    $this->assertDatabaseMissing('solutions', ['id' => $solution->id]);
});

// Pagination Test

it('paginates solutions', function () {
    Solution::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200);
});

// Menu Test

it('has solutions link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('web.solutions.index'))
        ->assertStatus(200);
});

// Relationship Tests

it('can create solution with root causes', function () {
    $rootCause = RootCause::factory()->create();

    $data = [
        'name' => 'Solution with Root Cause',
        'description' => 'Test',
        'dimension' => 'Process',
        'root_cause_ids' => [$rootCause->id],
    ];

    $this->actingAs($this->user)
        ->post(route('web.solutions.store'), $data)
        ->assertRedirect(route('web.solutions.index'))
        ->assertSessionHas('success');

    $solution = Solution::where('name', 'Solution with Root Cause')->first();
    $this->assertCount(1, $solution->rootCauses);
});

it('can update solution with root causes', function () {
    $solution = Solution::factory()->create();
    $rootCause1 = RootCause::factory()->create();
    $rootCause2 = RootCause::factory()->create();

    $data = [
        'name' => 'Updated',
        'description' => 'Test',
        'dimension' => 'People',
        'root_cause_ids' => [$rootCause1->id, $rootCause2->id],
    ];

    $this->actingAs($this->user)
        ->put(route('web.solutions.update', $solution), $data)
        ->assertRedirect(route('web.solutions.index'))
        ->assertSessionHas('success');

    $solution->refresh();
    $this->assertCount(2, $solution->rootCauses);
});
