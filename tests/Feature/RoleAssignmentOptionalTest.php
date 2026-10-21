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

    // Ensure domain exists for business assets
    $this->domain = Domain::factory()->create();
    $this->dataInitiative = DataInitiative::factory()->create();
});

// Test that items can be created without any roles assigned
it('can create data initiative without any roles assigned', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'NO-ROLES',
        'label' => 'Initiative Without Roles',
    ]);

    expect($initiative->dataSteward()->first())->toBeNull();
    expect($initiative->dataOwner()->first())->toBeNull();
    expect($initiative->roleAssignments()->count())->toBe(0);
});

it('can create business asset without any roles assigned', function () {
    $asset = BusinessAsset::factory()->create([
        'name' => 'Asset Without Roles',
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    expect($asset->dataSteward()->first())->toBeNull();
    expect($asset->dataOwner()->first())->toBeNull();
    expect($asset->roleAssignments()->count())->toBe(0);
});

// Test that items can be viewed without roles assigned
it('can view data initiative show page when no roles assigned', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'VIEW-NO-ROLES',
        'label' => 'View Without Roles',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Data Steward'))
        ->assertSee('-')
        ->assertSee(__('Data Owner'))
        ->assertSee('-');
});

it('can view business asset show page when no roles assigned', function () {
    $asset = BusinessAsset::factory()->create([
        'name' => 'View Asset Without Roles',
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Data Steward'))
        ->assertSee('-')
        ->assertSee(__('Data Owner'))
        ->assertSee('-');
});

// Test that items can be listed without roles assigned
it('can list data initiatives without roles in index', function () {
    DataInitiative::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('-'); // Should show dash for steward
});

it('can list business assets without roles in index', function () {
    BusinessAsset::factory()->count(3)->create([
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('-'); // Should show dash for steward
});

// Test that items can be updated without changing roles
it('can update data initiative without assigning roles', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'UPDATE-NO-ROLES',
        'label' => 'Update Without Roles',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.update', $initiative), [
            'code' => 'UPDATE-NO-ROLES',
            'label' => 'Updated Without Roles',
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('web.data-initiatives.index'));

    // Verify roles are still not assigned
    $initiative->refresh();
    expect($initiative->dataSteward()->first())->toBeNull();
    expect($initiative->dataOwner()->first())->toBeNull();
});

it('can update business asset without assigning roles', function () {
    $asset = BusinessAsset::factory()->create([
        'name' => 'Update Asset Without Roles',
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.update', $asset), [
            'name' => 'Updated Asset Without Roles',
            'definition' => 'Updated definition',
            'domain_id' => $this->domain->id,
            'data_initiative_id' => $this->dataInitiative->id,
        ])
        ->assertRedirect(route('web.business-assets.index'));

    // Verify roles are still not assigned
    $asset->refresh();
    expect($asset->dataSteward()->first())->toBeNull();
    expect($asset->dataOwner()->first())->toBeNull();
});

// Test that role queries return null/empty when no roles assigned
it('data steward query returns null when not assigned', function () {
    $initiative = DataInitiative::factory()->create();

    expect($initiative->dataSteward()->first())->toBeNull();
    expect($initiative->dataSteward()->get())->toHaveCount(0);
});

it('data owner query returns null when not assigned', function () {
    $initiative = DataInitiative::factory()->create();

    expect($initiative->dataOwner()->first())->toBeNull();
    expect($initiative->dataOwner()->get())->toHaveCount(0);
});

it('business asset data steward query returns null when not assigned', function () {
    $asset = BusinessAsset::factory()->create([
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    expect($asset->dataSteward()->first())->toBeNull();
    expect($asset->dataSteward()->get())->toHaveCount(0);
});

it('business asset data owner query returns null when not assigned', function () {
    $asset = BusinessAsset::factory()->create([
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    expect($asset->dataOwner()->first())->toBeNull();
    expect($asset->dataOwner()->get())->toHaveCount(0);
});

// Test that we can query for items without roles
it('can query data initiatives without any role assignments', function () {
    $initiative1 = DataInitiative::factory()->create(['code' => 'NO-ROLES-1']);
    $initiative2 = DataInitiative::factory()->create(['code' => 'NO-ROLES-2']);

    // Query for initiatives with no data steward
    $noSteward = DataInitiative::whereIn('code', ['NO-ROLES-1', 'NO-ROLES-2'])
        ->whereDoesntHave('dataSteward')
        ->get();
    expect($noSteward)->toHaveCount(2);

    // Query for initiatives with no data owner
    $noOwner = DataInitiative::whereIn('code', ['NO-ROLES-1', 'NO-ROLES-2'])
        ->whereDoesntHave('dataOwner')
        ->get();
    expect($noOwner)->toHaveCount(2);
});

it('can query business assets without any role assignments', function () {
    $asset1 = BusinessAsset::factory()->create([
        'name' => 'NO-ROLES-ASSET-1',
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);
    $asset2 = BusinessAsset::factory()->create([
        'name' => 'NO-ROLES-ASSET-2',
        'domain_id' => $this->domain->id,
        'data_initiative_id' => $this->dataInitiative->id,
    ]);

    // Query for assets with no data steward
    $noSteward = BusinessAsset::whereDoesntHave('dataSteward')->get();
    expect($noSteward)->toHaveCount(2);

    // Query for assets with no data owner
    $noOwner = BusinessAsset::whereDoesntHave('dataOwner')->get();
    expect($noOwner)->toHaveCount(2);
});

// Test mixed scenarios - some with roles, some without
it('can handle mixed initiatives with and without roles', function () {
    $steward = User::factory()->create();
    $owner = User::factory()->create();

    $initiativeWithRoles = DataInitiative::factory()->create(['code' => 'WITH-ROLES']);
    $initiativeWithoutRoles = DataInitiative::factory()->create(['code' => 'WITHOUT-ROLES']);

    // Assign roles to one initiative
    Role::firstOrCreate(['name' => 'Data Steward'], ['description' => 'Data Steward']);
    Role::firstOrCreate(['name' => 'Data Owner'], ['description' => 'Data Owner']);
    $initiativeWithRoles->assignDataSteward($steward);
    $initiativeWithRoles->assignDataOwner($owner);

    // Verify the other has no roles
    expect($initiativeWithRoles->dataSteward()->first())->not->toBeNull();
    expect($initiativeWithRoles->dataOwner()->first())->not->toBeNull();
    expect($initiativeWithoutRoles->dataSteward()->first())->toBeNull();
    expect($initiativeWithoutRoles->dataOwner()->first())->toBeNull();

    // Can list all initiatives with these specific codes
    $all = DataInitiative::whereIn('code', ['WITH-ROLES', 'WITHOUT-ROLES'])->get();
    expect($all)->toHaveCount(2);
});
