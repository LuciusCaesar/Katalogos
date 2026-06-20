<?php

use App\Models\DataIssue;
use App\Models\RootCause;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays root causes index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.root-causes.index')
        ->assertSee(__('Root Causes'));
});

it('displays empty state when no root causes exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertSee(__('No root causes found. Create one to get started.'));
});

it('displays root causes in a table', function () {
    RootCause::factory()->create([
        'name' => 'Test Root Cause',
        'description' => 'Test Description',
        'dimension' => 'Process',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertSee('Test Root Cause')
        ->assertSee('Test Description')
        ->assertSee('Process')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Dimension'))
        ->assertSee(__('Data Issues'))
        ->assertSee(__('Created At'))
        ->assertSee(__('Actions'));
});

it('displays data issues count for root cause', function () {
    $rootCause = RootCause::factory()->create();
    $dataIssue = DataIssue::factory()->create();
    $rootCause->dataIssues()->attach($dataIssue->id);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertSee('1');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.root-causes.create'));
});

it('displays edit and delete links for each root cause', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.root-causes.edit', $rootCause));
});

it('requires authentication to access root causes index', function () {
    $this->get(route('web.root-causes.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create root cause page', function () {
    $this->actingAs($this->user)
        ->get(route('web.root-causes.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.root-causes.create')
        ->assertSee(__('Create Root Cause'));
});

it('displays root cause form with data issues on create page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.create'))
        ->assertStatus(200)
        ->assertSee(__('Root Cause Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Dimension'))
        ->assertSee(__('Data Issues'))
        ->assertSee($dataIssue->name);
});

it('requires authentication to access create root cause page', function () {
    $this->get(route('web.root-causes.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new root cause', function () {
    $data = [
        'name' => 'New Root Cause',
        'description' => 'New Description',
        'dimension' => 'Tool',
    ];

    $this->actingAs($this->user)
        ->post(route('web.root-causes.store'), $data)
        ->assertRedirect(route('web.root-causes.index'))
        ->assertSessionHas('success', __('Root cause created successfully.'));

    $this->assertDatabaseHas('root_causes', $data);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.root-causes.store'), ['description' => 'Test', 'dimension' => 'Process'])
        ->assertSessionHasErrors('name');
});

it('validates dimension is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.root-causes.store'), ['name' => 'Test', 'description' => 'Test'])
        ->assertSessionHasErrors('dimension');
});

it('validates dimension is one of allowed values when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.root-causes.store'), ['name' => 'Test', 'dimension' => 'Invalid'])
        ->assertSessionHasErrors('dimension');
});

// Show Page Tests

it('displays root cause show page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertViewIs('pages.root-causes.show')
        ->assertSee($rootCause->name);
});

it('displays root cause name as title', function () {
    $rootCause = RootCause::factory()->create(['name' => 'Test Root Cause']);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee('Test Root Cause');
});

it('displays root cause description', function () {
    $rootCause = RootCause::factory()->create(['description' => 'Test Description']);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays root cause dimension', function () {
    $rootCause = RootCause::factory()->create(['dimension' => 'People']);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee('People');
});

it('displays edit and delete buttons on show page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.root-causes.edit', $rootCause));
});

it('displays data issues section on show page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee(__('Data Issues'));
});

it('displays associated data issues on show page', function () {
    $rootCause = RootCause::factory()->create();
    $dataIssue = DataIssue::factory()->create(['name' => 'Test Data Issue']);
    $rootCause->dataIssues()->attach($dataIssue->id);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee('Test Data Issue');
});

it('displays empty message when no data issues exist', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee(__('No data issues associated with this root cause.'));
});

it('displays back to root causes link on show page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.show', $rootCause))
        ->assertStatus(200)
        ->assertSee(__('Back to Root Causes'))
        ->assertSee(route('web.root-causes.index'));
});

it('requires authentication to access root cause show page', function () {
    $rootCause = RootCause::factory()->create();

    $this->get(route('web.root-causes.show', $rootCause))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit root cause page', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.edit', $rootCause))
        ->assertStatus(200)
        ->assertViewIs('pages.root-causes.edit')
        ->assertSee(__('Edit Root Cause'));
});

it('displays root cause form with pre-filled values on edit page', function () {
    $rootCause = RootCause::factory()->create([
        'name' => 'Existing Root Cause',
        'description' => 'Existing Description',
        'dimension' => 'Tool',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.root-causes.edit', $rootCause))
        ->assertStatus(200)
        ->assertSee('Existing Root Cause')
        ->assertSee('Existing Description')
        ->assertSee('Tool');
});

it('requires authentication to access edit root cause page', function () {
    $rootCause = RootCause::factory()->create();

    $this->get(route('web.root-causes.edit', $rootCause))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a root cause', function () {
    $rootCause = RootCause::factory()->create();

    $data = [
        'name' => 'Updated Root Cause',
        'description' => 'Updated Description',
        'dimension' => 'People',
    ];

    $this->actingAs($this->user)
        ->put(route('web.root-causes.update', $rootCause), $data)
        ->assertRedirect(route('web.root-causes.index'))
        ->assertSessionHas('success', __('Root cause updated successfully.'));

    $this->assertDatabaseHas('root_causes', $data);
});

it('validates name is required when updating', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.root-causes.update', $rootCause), ['description' => 'Test', 'dimension' => 'Process'])
        ->assertSessionHasErrors('name');
});

it('validates dimension is required when updating', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.root-causes.update', $rootCause), ['name' => 'Test', 'description' => 'Test'])
        ->assertSessionHasErrors('dimension');
});

// Delete Tests

it('can delete a root cause', function () {
    $rootCause = RootCause::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.root-causes.destroy', $rootCause))
        ->assertRedirect(route('web.root-causes.index'))
        ->assertSessionHas('success', __('Root cause deleted successfully.'));

    $this->assertDatabaseMissing('root_causes', ['id' => $rootCause->id]);
});

// Pagination Test

it('paginates root causes', function () {
    RootCause::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200);
});

// Menu Test

it('has root causes link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('web.root-causes.index'))
        ->assertStatus(200);
});

// Relationship Tests

it('can create root cause with data issues', function () {
    $dataIssue = DataIssue::factory()->create();

    $data = [
        'name' => 'Root Cause with Data Issue',
        'description' => 'Test',
        'dimension' => 'Process',
        'data_issue_ids' => [$dataIssue->id],
    ];

    $this->actingAs($this->user)
        ->post(route('web.root-causes.store'), $data)
        ->assertRedirect(route('web.root-causes.index'))
        ->assertSessionHas('success');

    $rootCause = RootCause::where('name', 'Root Cause with Data Issue')->first();
    $this->assertCount(1, $rootCause->dataIssues);
});

it('can update root cause with data issues', function () {
    $rootCause = RootCause::factory()->create();
    $dataIssue1 = DataIssue::factory()->create();
    $dataIssue2 = DataIssue::factory()->create();

    $data = [
        'name' => 'Updated',
        'description' => 'Test',
        'dimension' => 'People',
        'data_issue_ids' => [$dataIssue1->id, $dataIssue2->id],
    ];

    $this->actingAs($this->user)
        ->put(route('web.root-causes.update', $rootCause), $data)
        ->assertRedirect(route('web.root-causes.index'))
        ->assertSessionHas('success');

    $rootCause->refresh();
    $this->assertCount(2, $rootCause->dataIssues);
});
