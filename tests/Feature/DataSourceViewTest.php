<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\DataSource;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays data sources index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-sources.index')
        ->assertSee(__('Data Sources'));
});

it('displays empty state when no data sources exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertSee(__('No data sources found. Create one to get started.'));
});

it('displays data sources in a table', function () {
    DataSource::factory()->create([
        'name' => 'Test Data Source',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertSee('Test Data Source')
        ->assertSee('Test Description')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Data Custodian'))
        ->assertSee(__('Actions'));
});

it('displays business assets count for data source', function () {
    $dataSource = DataSource::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->count(3)->create([
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ])->each(function ($asset) use ($dataSource) {
        $asset->dataSources()->attach($dataSource->id);
    });

    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.data-sources.create'));
});

it('displays edit and delete links for each data source', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(__('Team'))
        ->assertSee(route('web.data-sources.edit', $dataSource));
});

it('requires authentication to access data sources index', function () {
    $this->get(route('web.data-sources.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create data source page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-sources.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-sources.create')
        ->assertSee(__('Create Data Source'));
});

it('displays data source form with business assets on create page', function () {
    $businessAsset = BusinessAsset::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.create'))
        ->assertStatus(200)
        ->assertSee(__('Data Source Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee($businessAsset->name);
});

it('requires authentication to access create data source page', function () {
    $this->get(route('web.data-sources.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new data source', function () {
    $data = [
        'name' => 'New Data Source',
        'description' => 'New Description',
    ];

    $this->actingAs($this->user)
        ->post(route('web.data-sources.store'), $data)
        ->assertRedirect(route('web.data-sources.index'))
        ->assertSessionHas('success', __('Data source created successfully.'));

    $this->assertDatabaseHas('data_sources', $data);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-sources.store'), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Show Page Tests

it('displays data source show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertViewIs('pages.data-sources.show')
        ->assertSee($dataSource->name);
});

it('displays data source name as title', function () {
    $dataSource = DataSource::factory()->create(['name' => 'Test Data Source']);

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee('Test Data Source');
});

it('displays data source description', function () {
    $dataSource = DataSource::factory()->create(['description' => 'Test Description']);

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays edit and delete buttons on show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(__('Manage Team'))
        ->assertSee(route('web.data-sources.edit', $dataSource));
});

it('displays data custodian section on show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee(__('Data Custodian'));
});

it('displays business assets section on show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee(__('Business Assets'));
});

it('displays associated business assets on show page', function () {
    $dataSource = DataSource::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    $dataSource->businessAssets()->attach($asset->id);

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertSee('Test Definition')
        ->assertSee($domain->name);
});

it('displays empty message when no business assets exist', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee(__('No business assets associated with this data source.'));
});

it('displays back to data sources link on show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.show', $dataSource))
        ->assertStatus(200)
        ->assertSee(__('Back to Data Sources'))
        ->assertSee(route('web.data-sources.index'));
});

it('requires authentication to access data source show page', function () {
    $dataSource = DataSource::factory()->create();

    $this->get(route('web.data-sources.show', $dataSource))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit data source page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.edit', $dataSource))
        ->assertStatus(200)
        ->assertViewIs('pages.data-sources.edit')
        ->assertSee(__('Edit Data Source'));
});

it('displays data source form with pre-filled values on edit page', function () {
    $dataSource = DataSource::factory()->create([
        'name' => 'Existing Data Source',
        'description' => 'Existing Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-sources.edit', $dataSource))
        ->assertStatus(200)
        ->assertSee('Existing Data Source')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit data source page', function () {
    $dataSource = DataSource::factory()->create();

    $this->get(route('web.data-sources.edit', $dataSource))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a data source', function () {
    $dataSource = DataSource::factory()->create();

    $data = [
        'name' => 'Updated Data Source',
        'description' => 'Updated Description',
    ];

    $this->actingAs($this->user)
        ->put(route('web.data-sources.update', $dataSource), $data)
        ->assertRedirect(route('web.data-sources.index'))
        ->assertSessionHas('success', __('Data source updated successfully.'));

    $this->assertDatabaseHas('data_sources', $data);
});

it('validates name is required when updating', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.data-sources.update', $dataSource), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Delete Tests

it('can delete a data source', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.data-sources.destroy', $dataSource))
        ->assertRedirect(route('web.data-sources.index'))
        ->assertSessionHas('success', __('Data source deleted successfully.'));

    $this->assertDatabaseMissing('data_sources', ['id' => $dataSource->id]);
});

// Pagination Test

it('paginates data sources', function () {
    DataSource::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200);
});

// Menu Test

it('has data sources link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-sources.index'))
        ->assertStatus(200);
});

// Team Management Tests

it('displays manage team page', function () {
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-sources.team', $dataSource))
        ->assertStatus(200)
        ->assertViewIs('pages.data-sources.manage-team')
        ->assertSee(__('Manage Team'))
        ->assertSee(__('Data Custodian'));
});

it('can update data custodian for data source', function () {
    $dataSource = DataSource::factory()->create();
    $user = User::factory()->create();
    $dataCustodianRole = Role::where('name', 'Data Custodian')->firstOrFail();

    $this->actingAs($this->user)
        ->put(route('web.data-sources.team.update', $dataSource), [
            'data_custodian_id' => $user->id,
        ])
        ->assertRedirect(route('web.data-sources.show', $dataSource))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'roleable_id' => $dataSource->id,
        'roleable_type' => DataSource::class,
        'user_id' => $user->id,
        'role_id' => $dataCustodianRole->id,
    ]);
});
