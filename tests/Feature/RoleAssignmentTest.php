<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;

it('can create roles', function () {
    $role = Role::factory()->create([
        'name' => 'Test Role',
        'description' => 'A test role',
    ]);

    expect($role->name)->toBe('Test Role');
    expect($role->description)->toBe('A test role');
});

it('seeds default roles', function () {
    $this->seed();

    expect(Role::where('name', 'Data Steward')->exists())->toBeTrue();
    expect(Role::where('name', 'Data Owner')->exists())->toBeTrue();
});

it('can assign role to user for data initiative', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();

    $assignment = $initiative->assignRoleToUser($user, $stewardRole);

    expect($assignment)->toBeInstanceOf(RoleAssignment::class);
    expect($assignment->user_id)->toBe($user->id);
    expect($assignment->role_id)->toBe($stewardRole->id);
    expect($assignment->roleable_id)->toBe($initiative->id);
    expect($assignment->roleable_type)->toBe(DataInitiative::class);
});

it('can assign role to user for business asset', function () {
    $user = User::factory()->create();
    $asset = BusinessAsset::factory()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $assignment = $asset->assignRoleToUser($user, $ownerRole);

    expect($assignment)->toBeInstanceOf(RoleAssignment::class);
    expect($assignment->user_id)->toBe($user->id);
    expect($assignment->role_id)->toBe($ownerRole->id);
    expect($assignment->roleable_id)->toBe($asset->id);
    expect($assignment->roleable_type)->toBe(BusinessAsset::class);
});

it('can get users with specific role on data initiative', function () {
    $user1 = User::factory()->create(['name' => 'Steward User']);
    $user2 = User::factory()->create(['name' => 'Owner User']);
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $initiative->assignRoleToUser($user1, $stewardRole);
    $initiative->assignRoleToUser($user2, $ownerRole);

    $steward = $initiative->dataSteward()->first();
    $owner = $initiative->dataOwner()->first();

    expect($steward->name)->toBe('Steward User');
    expect($owner->name)->toBe('Owner User');
});

it('can get users with specific role on business asset', function () {
    $user1 = User::factory()->create(['name' => 'Asset Steward']);
    $user2 = User::factory()->create(['name' => 'Asset Owner']);
    $asset = BusinessAsset::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $asset->assignRoleToUser($user1, $stewardRole);
    $asset->assignRoleToUser($user2, $ownerRole);

    $steward = $asset->dataSteward()->first();
    $owner = $asset->dataOwner()->first();

    expect($steward->name)->toBe('Asset Steward');
    expect($owner->name)->toBe('Asset Owner');
});

it('user can check if they have role on entity', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $initiative->assignRoleToUser($user, $stewardRole);
    $initiative->assignRoleToUser($user, $ownerRole);

    expect($user->isDataStewardFor($initiative))->toBeTrue();
    expect($user->isDataOwnerFor($initiative))->toBeTrue();
    expect($user->isDataStewardFor($initiative))->toBeTrue();
});

it('user can check if they do not have role on entity', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();

    // Don't assign the role

    expect($user->isDataStewardFor($initiative))->toBeFalse();
    expect($user->isDataOwnerFor($initiative))->toBeFalse();
});

it('can get all users with roles on data initiative', function () {
    $user1 = User::factory()->create(['name' => 'User 1']);
    $user2 = User::factory()->create(['name' => 'User 2']);
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $initiative->assignRoleToUser($user1, $stewardRole);
    $initiative->assignRoleToUser($user2, $ownerRole);
    $initiative->assignRoleToUser($user1, $ownerRole);

    $users = $initiative->users()->get();

    expect($users->count())->toBe(3); // user1 with 2 roles, user2 with 1 role
});

it('can get all roles assigned to data initiative', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $initiative->assignRoleToUser($user, $stewardRole);
    $initiative->assignRoleToUser($user, $ownerRole);

    $roles = $initiative->roles()->get();

    expect($roles->count())->toBe(2);
    expect($roles->pluck('name')->all())->toContain('Data Steward');
    expect($roles->pluck('name')->all())->toContain('Data Owner');
});

it('can remove role assignment from data initiative', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();

    $initiative->assignRoleToUser($user, $stewardRole);
    expect($initiative->users()->count())->toBe(1);

    $removed = $initiative->removeRoleFromUser($user, $stewardRole);
    expect($removed)->toBeTrue();
    expect($initiative->fresh()->users()->count())->toBe(0);
});

it('can remove role assignment from business asset', function () {
    $user = User::factory()->create();
    $asset = BusinessAsset::factory()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $asset->assignRoleToUser($user, $ownerRole);
    expect($asset->users()->count())->toBe(1);

    $removed = $asset->removeRoleFromUser($user, $ownerRole);
    expect($removed)->toBeTrue();
    expect($asset->fresh()->users()->count())->toBe(0);
});

it('prevents duplicate role assignments', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();

    // First assignment should succeed
    $initiative->assignRoleToUser($user, $stewardRole);
    expect($initiative->roleAssignments()->count())->toBe(1);

    // Second assignment of same role to same user should fail due to unique constraint
    expect(fn () => $initiative->assignRoleToUser($user, $stewardRole))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('user can have different roles on different entities', function () {
    $user = User::factory()->create();
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    $initiative1->assignRoleToUser($user, $stewardRole);
    $initiative2->assignRoleToUser($user, $ownerRole);

    expect($user->isDataStewardFor($initiative1))->toBeTrue();
    expect($user->isDataOwnerFor($initiative1))->toBeFalse();
    expect($user->isDataStewardFor($initiative2))->toBeFalse();
    expect($user->isDataOwnerFor($initiative2))->toBeTrue();
});

it('can get user data initiatives with roles', function () {
    $user = User::factory()->create();
    $initiative1 = DataInitiative::factory()->create(['code' => 'INIT-001']);
    $initiative2 = DataInitiative::factory()->create(['code' => 'INIT-002']);
    $stewardRole = Role::factory()->dataSteward()->create();

    $initiative1->assignRoleToUser($user, $stewardRole);
    $initiative2->assignRoleToUser($user, $stewardRole);

    $initiatives = $user->dataInitiatives()->get();

    expect($initiatives->count())->toBe(2);
    expect($initiatives->pluck('code')->all())->toContain('INIT-001');
    expect($initiatives->pluck('code')->all())->toContain('INIT-002');
});

it('can get user business assets with roles', function () {
    $user = User::factory()->create();
    $asset1 = BusinessAsset::factory()->create(['name' => 'Asset 1']);
    $asset2 = BusinessAsset::factory()->create(['name' => 'Asset 2']);
    $ownerRole = Role::factory()->dataOwner()->create();

    $asset1->assignRoleToUser($user, $ownerRole);
    $asset2->assignRoleToUser($user, $ownerRole);

    $assets = $user->businessAssets()->get();

    expect($assets->count())->toBe(2);
    expect($assets->pluck('name')->all())->toContain('Asset 1');
    expect($assets->pluck('name')->all())->toContain('Asset 2');
});

it('role assignment has pivot data', function () {
    $user = User::factory()->create();
    $initiative = DataInitiative::factory()->create();
    $stewardRole = Role::factory()->dataSteward()->create();

    $assignment = $initiative->assignRoleToUser($user, $stewardRole);

    // Access via user relationship
    $users = $initiative->users()->withPivot('role_id')->get();
    expect($users->first()->pivot->role_id)->toBe($stewardRole->id);

    // Access via role relationship
    $roles = $initiative->roles()->withPivot('user_id')->get();
    expect($roles->first()->pivot->user_id)->toBe($user->id);
});
