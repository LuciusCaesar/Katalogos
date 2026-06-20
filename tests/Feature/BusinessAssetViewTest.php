<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('displays business assets index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.index')
        ->assertSee(__('Business Assets'));
});

it('displays empty state when no business assets exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee(__('No business assets found. Create one to get started.'));
});

it('displays business assets in a table', function () {
    $dataInitiative = DataInitiative::factory()->create(['label' => 'Test Initiative']);

    BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertSee('Test Definition')
        ->assertSee('Test Initiative')
        ->assertSee(__('Name'))
        ->assertSee(__('Definition'))
        ->assertSee(__('Data Initiative'))
        ->assertSee(__('Data Steward'))
        ->assertSee(__('Data Owner'));
});

it('displays data steward and data owner when assigned', function () {
    $steward = User::factory()->create(['name' => 'John Steward']);
    $owner = User::factory()->create(['name' => 'Jane Owner']);
    $dataInitiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $asset->assignRoleToUser($steward, $stewardRole);
    $asset->assignRoleToUser($owner, $ownerRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('John Steward')
        ->assertSee('Jane Owner');
});

it('displays dash when no data steward is assigned', function () {
    $dataInitiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSeeInOrder(['Test Asset', '-', '-']); // name, steward (dash), owner (dash)
});

it('requires authentication to access business assets index', function () {
    $this->get(route('web.business-assets.index'))
        ->assertRedirect(route('login'));
});

// Show Page Tests

it('displays business asset show page', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Show Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.show')
        ->assertSee('Show Test Asset');
});

it('displays business asset name as title', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Title Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSeeInOrder(['Title Test Asset', 'Test Definition']);
});

it('displays business asset definition', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Asset Name',
        'definition' => 'Unique Definition Text',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee('Unique Definition Text');
});

it('displays data steward in right column card', function () {
    $steward = User::factory()->create(['name' => 'Alice Steward']);
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Steward Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $stewardRole = Role::factory()->dataSteward()->create();
    $asset->assignRoleToUser($steward, $stewardRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Steward'))
        ->assertSee('Alice Steward');
});

it('displays data owner in right column card', function () {
    $owner = User::factory()->create(['name' => 'Bob Owner']);
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Owner Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $ownerRole = Role::factory()->dataOwner()->create();
    $asset->assignRoleToUser($owner, $ownerRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Owner'))
        ->assertSee('Bob Owner');
});

it('displays data initiative in right column card', function () {
    $dataInitiative = DataInitiative::factory()->create(['label' => 'Initiative Label']);
    $asset = BusinessAsset::factory()->create([
        'name' => 'Initiative Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Initiative'))
        ->assertSee('Initiative Label');
});

it('displays dash when data steward not assigned', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'No Steward Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Steward'))
        ->assertSeeInOrder([__('Data Steward'), '-']);
});

it('displays dash when data owner not assigned', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'No Owner Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Owner'))
        ->assertSeeInOrder([__('Data Owner'), '-']);
});

it('requires authentication to access business asset show page', function () {
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Auth Test Asset',
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->get(route('web.business-assets.show', $asset))
        ->assertRedirect(route('login'));
});
