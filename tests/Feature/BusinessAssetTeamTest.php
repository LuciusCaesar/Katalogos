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
    $this->dataInitiative = DataInitiative::factory()->create();
    $this->domain = Domain::factory()->create();
    $this->stewardRole = Role::factory()->dataSteward()->create();
    $this->ownerRole = Role::factory()->dataOwner()->create();
});

it('displays manage team page for business asset', function () {
    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.team', $businessAsset))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.manage-team')
        ->assertSee(__('Manage Team'))
        ->assertSee(__('Data Steward'))
        ->assertSee(__('Data Owner'))
        ->assertSee(__('Save Team'));
});

it('displays current data steward and owner on manage team page', function () {
    $steward = User::factory()->create(['name' => 'John Steward']);
    $owner = User::factory()->create(['name' => 'Jane Owner']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $businessAsset->assignRoleToUser($steward, $this->stewardRole);
    $businessAsset->assignRoleToUser($owner, $this->ownerRole);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.team', $businessAsset))
        ->assertStatus(200)
        ->assertSee('John Steward')
        ->assertSee('Jane Owner');
});

it('can assign data steward to business asset', function () {
    $steward = User::factory()->create(['name' => 'New Steward']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => $steward->id,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);
});

it('can assign data owner to business asset', function () {
    $owner = User::factory()->create(['name' => 'New Owner']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $owner->id,
        'role_id' => $this->ownerRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);
});

it('can change data steward for business asset', function () {
    $oldSteward = User::factory()->create(['name' => 'Old Steward']);
    $newSteward = User::factory()->create(['name' => 'New Steward']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $businessAsset->assignRoleToUser($oldSteward, $this->stewardRole);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => $newSteward->id,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $newSteward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);

    $this->assertDatabaseMissing('role_assignments', [
        'user_id' => $oldSteward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);
});

it('can remove data steward from business asset', function () {
    $steward = User::factory()->create(['name' => 'Test Steward']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $businessAsset->assignRoleToUser($steward, $this->stewardRole);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => null,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset));

    $this->assertDatabaseMissing('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);
});

it('can assign both data steward and data owner in one request', function () {
    $steward = User::factory()->create(['name' => 'New Steward']);
    $owner = User::factory()->create(['name' => 'New Owner']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => $steward->id,
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $owner->id,
        'role_id' => $this->ownerRole->id,
        'roleable_id' => $businessAsset->id,
        'roleable_type' => BusinessAsset::class,
    ]);
});

it('validates data steward id exists', function () {
    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => 9999,
        ])
        ->assertSessionHasErrors('data_steward_id')
        ->assertRedirect();
});

it('validates data owner id exists', function () {
    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_owner_id' => 9999,
        ])
        ->assertSessionHasErrors('data_owner_id')
        ->assertRedirect();
});

it('does not create duplicate assignment when same user is submitted', function () {
    $steward = User::factory()->create(['name' => 'Test Steward']);
    $owner = User::factory()->create(['name' => 'Test Owner']);

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    // Assign initial roles
    $businessAsset->assignRoleToUser($steward, $this->stewardRole);
    $businessAsset->assignRoleToUser($owner, $this->ownerRole);

    // Submit the same users again - should not create duplicates
    $this->actingAs($this->user)
        ->put(route('web.business-assets.team.update', $businessAsset), [
            'data_steward_id' => $steward->id,
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.business-assets.show', $businessAsset))
        ->assertSessionHas('success', __('Team updated successfully.'));

    // Verify only one assignment exists per role
    $stewardAssignments = $businessAsset->roleAssignments()
        ->where('role_id', $this->stewardRole->id)
        ->where('user_id', $steward->id)
        ->count();

    $ownerAssignments = $businessAsset->roleAssignments()
        ->where('role_id', $this->ownerRole->id)
        ->where('user_id', $owner->id)
        ->count();

    expect($stewardAssignments)->toBe(1);
    expect($ownerAssignments)->toBe(1);
});

it('does not show user as data owner when only assigned as data steward on this asset', function () {
    $steward = User::factory()->create(['name' => 'Cross Entity Steward']);

    $businessAsset1 = BusinessAsset::factory()->create([
        'name' => 'Asset One',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $businessAsset2 = BusinessAsset::factory()->create([
        'name' => 'Asset Two',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    // Assign steward as Data Steward on Asset 1
    $businessAsset1->assignRoleToUser($steward, $this->stewardRole);

    // Assign same user as Data Owner on Asset 2
    $businessAsset2->assignRoleToUser($steward, $this->ownerRole);

    // Verify Asset 1 only shows steward, not owner
    expect($businessAsset1->dataSteward()->first()?->id)->toBe($steward->id);
    expect($businessAsset1->dataOwner()->first())->toBeNull();

    // Verify Asset 2 only shows owner, not steward
    expect($businessAsset2->dataOwner()->first()?->id)->toBe($steward->id);
    expect($businessAsset2->dataSteward()->first())->toBeNull();
});

it('does not show user as data steward when only assigned as data owner on this asset', function () {
    $owner = User::factory()->create(['name' => 'Cross Entity Owner']);

    $businessAsset1 = BusinessAsset::factory()->create([
        'name' => 'Asset One',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    $businessAsset2 = BusinessAsset::factory()->create([
        'name' => 'Asset Two',
        'data_initiative_id' => $this->dataInitiative->id,
        'domain_id' => $this->domain->id,
    ]);

    // Assign owner as Data Owner on Asset 1
    $businessAsset1->assignRoleToUser($owner, $this->ownerRole);

    // Assign same user as Data Steward on Asset 2
    $businessAsset2->assignRoleToUser($owner, $this->stewardRole);

    // Verify Asset 1 only shows owner, not steward
    expect($businessAsset1->dataOwner()->first()?->id)->toBe($owner->id);
    expect($businessAsset1->dataSteward()->first())->toBeNull();

    // Verify Asset 2 only shows steward, not owner
    expect($businessAsset2->dataSteward()->first()?->id)->toBe($owner->id);
    expect($businessAsset2->dataOwner()->first())->toBeNull();
});
