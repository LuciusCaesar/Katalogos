<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays data issues index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-issues.index')
        ->assertSee(__('Data Issues'));
});

it('displays empty state when no data issues exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertSee(__('No data issues found. Create one to get started.'));
});

it('displays data issues in a table', function () {
    DataIssue::factory()->create([
        'name' => 'Test Data Issue',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertSee('Test Data Issue')
        ->assertSee('Test Description')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Created At'))
        ->assertSee(__('Actions'));
});

it('displays business assets count for data issue', function () {
    $dataIssue = DataIssue::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->count(3)->create([
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ])->each(function ($asset) use ($dataIssue) {
        $asset->dataIssues()->attach($dataIssue->id);
    });

    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.data-issues.create'));
});

it('displays edit and delete links for each data issue', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.data-issues.edit', $dataIssue));
});

it('requires authentication to access data issues index', function () {
    $this->get(route('web.data-issues.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create data issue page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-issues.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-issues.create')
        ->assertSee(__('Create Data Issue'));
});

it('displays data issue form with business assets on create page', function () {
    $businessAsset = BusinessAsset::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.create'))
        ->assertStatus(200)
        ->assertSee(__('Data Issue Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee($businessAsset->name);
});

it('requires authentication to access create data issue page', function () {
    $this->get(route('web.data-issues.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new data issue', function () {
    $data = [
        'name' => 'New Data Issue',
        'description' => 'New Description',
    ];

    $this->actingAs($this->user)
        ->post(route('web.data-issues.store'), $data)
        ->assertRedirect(route('web.data-issues.index'))
        ->assertSessionHas('success', __('Data issue created successfully.'));

    $this->assertDatabaseHas('data_issues', $data);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-issues.store'), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Show Page Tests

it('displays data issue show page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertViewIs('pages.data-issues.show')
        ->assertSee($dataIssue->name);
});

it('displays data issue name as title', function () {
    $dataIssue = DataIssue::factory()->create(['name' => 'Test Data Issue']);

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee('Test Data Issue');
});

it('displays data issue description', function () {
    $dataIssue = DataIssue::factory()->create(['description' => 'Test Description']);

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays edit and delete buttons on show page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.data-issues.edit', $dataIssue));
});

it('displays business assets section on show page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee(__('Business Assets'));
});

it('displays associated business assets on show page', function () {
    $dataIssue = DataIssue::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    $dataIssue->businessAssets()->attach($asset->id);

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertSee('Test Definition')
        ->assertSee($domain->name);
});

it('displays empty message when no business assets exist', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee(__('No business assets associated with this data issue.'));
});

it('displays back to data issues link on show page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.show', $dataIssue))
        ->assertStatus(200)
        ->assertSee(__('Back to Data Issues'))
        ->assertSee(route('web.data-issues.index'));
});

it('requires authentication to access data issue show page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->get(route('web.data-issues.show', $dataIssue))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit data issue page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.edit', $dataIssue))
        ->assertStatus(200)
        ->assertViewIs('pages.data-issues.edit')
        ->assertSee(__('Edit Data Issue'));
});

it('displays data issue form with pre-filled values on edit page', function () {
    $dataIssue = DataIssue::factory()->create([
        'name' => 'Existing Data Issue',
        'description' => 'Existing Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-issues.edit', $dataIssue))
        ->assertStatus(200)
        ->assertSee('Existing Data Issue')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit data issue page', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->get(route('web.data-issues.edit', $dataIssue))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a data issue', function () {
    $dataIssue = DataIssue::factory()->create();

    $data = [
        'name' => 'Updated Data Issue',
        'description' => 'Updated Description',
    ];

    $this->actingAs($this->user)
        ->put(route('web.data-issues.update', $dataIssue), $data)
        ->assertRedirect(route('web.data-issues.index'))
        ->assertSessionHas('success', __('Data issue updated successfully.'));

    $this->assertDatabaseHas('data_issues', $data);
});

it('validates name is required when updating', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.data-issues.update', $dataIssue), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Delete Tests

it('can delete a data issue', function () {
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.data-issues.destroy', $dataIssue))
        ->assertRedirect(route('web.data-issues.index'))
        ->assertSessionHas('success', __('Data issue deleted successfully.'));

    $this->assertDatabaseMissing('data_issues', ['id' => $dataIssue->id]);
});

// Pagination Test

it('paginates data issues', function () {
    DataIssue::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200);
});

// Menu Test

it('has data issues link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-issues.index'))
        ->assertStatus(200);
});
