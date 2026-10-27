<?php

use App\Models\DataInitiative;
use App\Models\Domain;
use App\Models\Role;
use App\Models\User;
use App\Services\DataInitiativeGovernanceProjection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->stewardRole = Role::factory()->dataSteward()->create();
    $this->ownerRole = Role::factory()->dataOwner()->create();
});

it('displays manage team page for data initiative', function () {
    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.team', $dataInitiative))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.manage-team')
        ->assertSee(__('Manage Team'))
        ->assertSee(__('Data Steward'))
        ->assertSee(__('Data Owner'))
        ->assertSee(__('Save Team'));
});

it('displays current data steward and owner on manage team page', function () {
    $steward = User::factory()->create(['name' => 'John Steward']);
    $owner = User::factory()->create(['name' => 'Jane Owner']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $dataInitiative->assignDataSteward($steward);
    $dataInitiative->assignDataOwner($owner);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.team', $dataInitiative))
        ->assertStatus(200)
        ->assertSee('John Steward')
        ->assertSee('Jane Owner');
});

it('can assign data steward to data initiative', function () {
    $steward = User::factory()->create(['name' => 'New Steward']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => $steward->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);
});

it('can assign data owner to data initiative', function () {
    $owner = User::factory()->create(['name' => 'New Owner']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $owner->id,
        'role_id' => $this->ownerRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);
});

it('can change data steward for data initiative', function () {
    $oldSteward = User::factory()->create(['name' => 'Old Steward']);
    $newSteward = User::factory()->create(['name' => 'New Steward']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $dataInitiative->assignDataSteward($oldSteward);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => $newSteward->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $newSteward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);

    $this->assertDatabaseMissing('role_assignments', [
        'user_id' => $oldSteward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);
});

it('can remove data steward from data initiative', function () {
    $steward = User::factory()->create(['name' => 'Test Steward']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $dataInitiative->assignDataSteward($steward);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => null,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    $this->assertDatabaseMissing('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);
});

it('can assign both data steward and data owner in one request', function () {
    $steward = User::factory()->create(['name' => 'New Steward']);
    $owner = User::factory()->create(['name' => 'New Owner']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => $steward->id,
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $steward->id,
        'role_id' => $this->stewardRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);

    $this->assertDatabaseHas('role_assignments', [
        'user_id' => $owner->id,
        'role_id' => $this->ownerRole->id,
        'roleable_id' => $dataInitiative->id,
        'roleable_type' => DataInitiative::class,
    ]);
});

it('validates data steward id exists', function () {
    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => 9999,
        ])
        ->assertSessionHasErrors('data_steward_id')
        ->assertRedirect();
});

it('validates data owner id exists', function () {
    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_owner_id' => 9999,
        ])
        ->assertSessionHasErrors('data_owner_id')
        ->assertRedirect();
});

it('does not create duplicate assignment when same user is submitted', function () {
    $steward = User::factory()->create(['name' => 'Test Steward']);
    $owner = User::factory()->create(['name' => 'Test Owner']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    // Assign initial roles
    $dataInitiative->assignDataSteward($steward);
    $dataInitiative->assignDataOwner($owner);

    // Submit the same users again - should not create duplicates
    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => $steward->id,
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative))
        ->assertSessionHas('success', __('Team updated successfully.'));

    // Verify only one assignment exists per role
    $stewardAssignments = $dataInitiative->roleAssignments()
        ->where('role_id', $this->stewardRole->id)
        ->where('user_id', $steward->id)
        ->count();

    $ownerAssignments = $dataInitiative->roleAssignments()
        ->where('role_id', $this->ownerRole->id)
        ->where('user_id', $owner->id)
        ->count();

    expect($stewardAssignments)->toBe(1);
    expect($ownerAssignments)->toBe(1);
});

it('does not show user as data owner when only assigned as data steward on this initiative', function () {
    $steward = User::factory()->create(['name' => 'Cross Entity Steward']);

    $initiative1 = DataInitiative::factory()->create([
        'code' => 'INIT-001',
        'label' => 'Initiative One',
    ]);

    $initiative2 = DataInitiative::factory()->create([
        'code' => 'INIT-002',
        'label' => 'Initiative Two',
    ]);

    // Assign steward as Data Steward on Initiative 1
    $initiative1->assignDataSteward($steward);

    // Assign same user as Data Owner on Initiative 2
    $initiative2->assignDataOwner($steward);

    // Verify Initiative 1 only shows steward, not owner
    expect($initiative1->dataSteward()->first()?->id)->toBe($steward->id);
    expect($initiative1->dataOwner()->first())->toBeNull();

    // Verify Initiative 2 only shows owner, not steward
    expect($initiative2->dataOwner()->first()?->id)->toBe($steward->id);
    expect($initiative2->dataSteward()->first())->toBeNull();
});

it('does not show user as data steward when only assigned as data owner on this initiative', function () {
    $owner = User::factory()->create(['name' => 'Cross Entity Owner']);

    $initiative1 = DataInitiative::factory()->create([
        'code' => 'INIT-001',
        'label' => 'Initiative One',
    ]);

    $initiative2 = DataInitiative::factory()->create([
        'code' => 'INIT-002',
        'label' => 'Initiative Two',
    ]);

    // Assign owner as Data Owner on Initiative 1
    $initiative1->assignDataOwner($owner);

    // Assign same user as Data Steward on Initiative 2
    $initiative2->assignDataSteward($owner);

    // Verify Initiative 1 only shows owner, not steward
    expect($initiative1->dataOwner()->first()?->id)->toBe($owner->id);
    expect($initiative1->dataSteward()->first())->toBeNull();

    // Verify Initiative 2 only shows steward, not owner
    expect($initiative2->dataSteward()->first()?->id)->toBe($owner->id);
    expect($initiative2->dataOwner()->first())->toBeNull();
});

it('does not recalculate asset governance scores when assigning a data steward to the initiative', function () {
    $domain = Domain::factory()->create();
    $steward = User::factory()->create(['name' => 'New Steward']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    // Create a business asset for the initiative; its creation computes its score (6.0 points)
    $businessAsset = $dataInitiative->businessAssets()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
    ]);
    expect($businessAsset->governanceScores()->count())->toBe(1);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => $steward->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    // Initiative team changes do not affect business asset scores...
    expect($businessAsset->governanceScores()->count())->toBe(1);

    // ...and the initiative metrics, computed from asset scores, stay unchanged
    $metrics = app(DataInitiativeGovernanceProjection::class)->metrics($dataInitiative->fresh());
    expect($metrics['total_points'])->toEqualWithDelta(6.0, 1e-9);
    expect($metrics['average'])->toEqualWithDelta(6.0 / 14.5, 1e-9);
});

it('does not recalculate asset governance scores when assigning a data owner to the initiative', function () {
    $domain = Domain::factory()->create();
    $owner = User::factory()->create(['name' => 'New Owner']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $businessAsset = $dataInitiative->businessAssets()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
    ]);
    expect($businessAsset->governanceScores()->count())->toBe(1);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_owner_id' => $owner->id,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    expect($businessAsset->governanceScores()->count())->toBe(1);

    $metrics = app(DataInitiativeGovernanceProjection::class)->metrics($dataInitiative->fresh());
    expect($metrics['total_points'])->toEqualWithDelta(6.0, 1e-9);
});

it('does not recalculate asset governance scores when removing the initiative data steward', function () {
    $domain = Domain::factory()->create();
    $steward = User::factory()->create(['name' => 'Test Steward']);

    $dataInitiative = DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
    ]);

    $businessAsset = $dataInitiative->businessAssets()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
    ]);
    expect($businessAsset->governanceScores()->count())->toBe(1);

    // Assign initial steward
    $dataInitiative->assignDataSteward($steward);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.team.update', $dataInitiative), [
            'data_steward_id' => null,
        ])
        ->assertRedirect(route('web.data-initiatives.show', $dataInitiative));

    expect($businessAsset->governanceScores()->count())->toBe(1);

    $metrics = app(DataInitiativeGovernanceProjection::class)->metrics($dataInitiative->fresh());
    expect($metrics['total_points'])->toEqualWithDelta(6.0, 1e-9);
});
