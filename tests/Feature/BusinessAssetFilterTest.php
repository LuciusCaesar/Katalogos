<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'Data Steward'], ['description' => 'Data Steward']);
    Role::firstOrCreate(['name' => 'Data Owner'], ['description' => 'Data Owner']);
});

// Search Tests

it('can search business assets by name', function () {
    BusinessAsset::factory()->create(['name' => 'Test Asset', 'definition' => 'Some definition']);
    BusinessAsset::factory()->create(['name' => 'Other Asset', 'definition' => 'Another definition']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'Test']))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertDontSee('Other Asset');
});

it('can search business assets by definition', function () {
    BusinessAsset::factory()->create(['name' => 'Test Asset', 'definition' => 'Some definition']);
    BusinessAsset::factory()->create(['name' => 'Other Asset', 'definition' => 'Another definition']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'definition']))
        ->assertStatus(200)
        ->assertSee('Some definition')
        ->assertSee('Another definition');
});

it('search is case insensitive', function () {
    BusinessAsset::factory()->create(['name' => 'TEST Asset', 'definition' => 'Some definition']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'test']))
        ->assertStatus(200)
        ->assertSee('TEST Asset');
});

it('search with no results shows empty message', function () {
    BusinessAsset::factory()->create(['name' => 'Test Asset', 'definition' => 'Some definition']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'NonExistent']))
        ->assertStatus(200)
        ->assertSee(__('No business assets found matching your criteria. Try adjusting your filters.'));
});

// Filter Tests

it('can filter business assets by domain', function () {
    $domain1 = Domain::factory()->create();
    $domain2 = Domain::factory()->create();

    BusinessAsset::factory()->create(['name' => 'Domain 1 Asset', 'domain_id' => $domain1->id]);
    BusinessAsset::factory()->create(['name' => 'Domain 2 Asset', 'domain_id' => $domain2->id]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['domain_id' => $domain1->id]))
        ->assertStatus(200)
        ->assertSee('Domain 1 Asset')
        ->assertDontSee('Domain 2 Asset');
});

it('can filter business assets by data initiative', function () {
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();

    BusinessAsset::factory()->create(['name' => 'Initiative 1 Asset', 'data_initiative_id' => $initiative1->id]);
    BusinessAsset::factory()->create(['name' => 'Initiative 2 Asset', 'data_initiative_id' => $initiative2->id]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['data_initiative_id' => $initiative1->id]))
        ->assertStatus(200)
        ->assertSee('Initiative 1 Asset')
        ->assertDontSee('Initiative 2 Asset');
});

it('can filter business assets by data steward', function () {
    $steward1 = User::factory()->create();
    $steward2 = User::factory()->create();
    $dataStewardRole = Role::where('name', 'Data Steward')->firstOrFail();

    $asset1 = BusinessAsset::factory()->create(['name' => 'Steward 1 Asset']);
    $asset2 = BusinessAsset::factory()->create(['name' => 'Steward 2 Asset']);

    $asset1->assignRoleToUser($steward1, $dataStewardRole);
    $asset2->assignRoleToUser($steward2, $dataStewardRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['data_steward_id' => $steward1->id]))
        ->assertStatus(200)
        ->assertSee('Steward 1 Asset');

    // Verify only one asset is returned
    $this->assertCount(1, BusinessAsset::filter(['data_steward_id' => $steward1->id])->get());
});

it('can filter business assets by data owner', function () {
    $owner1 = User::factory()->create();
    $owner2 = User::factory()->create();
    $dataOwnerRole = Role::where('name', 'Data Owner')->firstOrFail();

    $asset1 = BusinessAsset::factory()->create(['name' => 'Owner 1 Asset']);
    $asset2 = BusinessAsset::factory()->create(['name' => 'Owner 2 Asset']);

    $asset1->assignRoleToUser($owner1, $dataOwnerRole);
    $asset2->assignRoleToUser($owner2, $dataOwnerRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['data_owner_id' => $owner1->id]))
        ->assertStatus(200)
        ->assertSee('Owner 1 Asset');

    // Verify only one asset is returned
    $this->assertCount(1, BusinessAsset::filter(['data_owner_id' => $owner1->id])->get());
});

// Combined Search and Filter Tests

it('can combine search and filters', function () {
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->create([
        'name' => 'Matching Asset',
        'definition' => 'test definition',
        'domain_id' => $domain->id,
    ]);
    BusinessAsset::factory()->create([
        'name' => 'Non Matching Asset',
        'definition' => 'other definition',
        'domain_id' => $domain->id,
    ]);
    BusinessAsset::factory()->create([
        'name' => 'Search Match But Wrong Domain',
        'definition' => 'test definition',
        'domain_id' => null,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', [
            'search' => 'test',
            'domain_id' => $domain->id,
        ]))
        ->assertStatus(200)
        ->assertSee('Matching Asset')
        ->assertDontSee('Non Matching Asset')
        ->assertDontSee('Search Match But Wrong Domain');
});

it('can filter by multiple criteria', function () {
    $domain = Domain::factory()->create();
    $initiative = DataInitiative::factory()->create();

    BusinessAsset::factory()->create([
        'name' => 'Matching Asset',
        'domain_id' => $domain->id,
        'data_initiative_id' => $initiative->id,
    ]);
    BusinessAsset::factory()->create([
        'name' => 'Wrong Domain',
        'domain_id' => Domain::factory()->create()->id,
        'data_initiative_id' => $initiative->id,
    ]);
    BusinessAsset::factory()->create([
        'name' => 'Wrong Initiative',
        'domain_id' => $domain->id,
        'data_initiative_id' => DataInitiative::factory()->create()->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', [
            'domain_id' => $domain->id,
            'data_initiative_id' => $initiative->id,
        ]))
        ->assertStatus(200)
        ->assertSee('Matching Asset')
        ->assertDontSee('Wrong Domain')
        ->assertDontSee('Wrong Initiative');
});

// Filter UI Tests

it('displays filter form on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee(__('Search'))
        ->assertSee(__('Domain'))
        ->assertSee(__('Data Initiative'))
        ->assertSee(__('Data Steward'))
        ->assertSee(__('Data Owner'))
        ->assertSee(__('Filter'))
        ->assertSee(__('Reset'));
});

it('displays filter options in dropdowns', function () {
    $domain = Domain::factory()->create(['name' => 'Test Domain']);
    $initiative = DataInitiative::factory()->create(['label' => 'Test Initiative']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('Test Domain')
        ->assertSee('Test Initiative');
});

it('retains filter values after submission', function () {
    $domain = Domain::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['domain_id' => $domain->id]));

    $response->assertStatus(200);
    $response->assertSee('selected');
});

it('retains search value after submission', function () {
    $response = $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'test']));

    $response->assertStatus(200);
    $response->assertSee('test', false); // Don't escape HTML
});

it('reset button clears all filters', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee(__('Reset'));
});

it('filters work with empty search bar', function () {
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->create(['name' => 'Domain Asset', 'domain_id' => $domain->id]);
    BusinessAsset::factory()->create(['name' => 'Other Asset']);

    // Apply filter with empty search
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => '', 'domain_id' => $domain->id]))
        ->assertStatus(200)
        ->assertSee('Domain Asset')
        ->assertDontSee('Other Asset');
});

it('search works without any filters', function () {
    BusinessAsset::factory()->create(['name' => 'Test Asset', 'definition' => 'Some definition']);
    BusinessAsset::factory()->create(['name' => 'Other Asset', 'definition' => 'Another definition']);

    // Apply search without any filters
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index', ['search' => 'Test']))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertDontSee('Other Asset');
});
