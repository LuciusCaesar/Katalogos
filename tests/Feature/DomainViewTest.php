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
});

// Index Page Tests

it('displays domains index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.domains.index')
        ->assertSee(__('Domains'));
});

it('displays empty state when no domains exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee(__('No domains found. Create one to get started.'));
});

it('displays domains in a table', function () {
    Domain::factory()->create([
        'name' => 'Test Domain',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee('Test Domain')
        ->assertSee('Test Description')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Terms'))
        ->assertSee(__('Domain Owner'))
        ->assertSee(__('Actions'));
});

it('displays business assets count for domain', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    BusinessAsset::factory()->count(3)->create([
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.domains.create'));
});

it('displays edit and delete links for each domain', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.domains.edit', $domain));
});

it('requires authentication to access domains index', function () {
    $this->get(route('web.domains.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create domain page', function () {
    $this->actingAs($this->user)
        ->get(route('web.domains.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.domains.create')
        ->assertSee(__('Create Domain'));
});

it('displays domain form on create page', function () {
    $this->actingAs($this->user)
        ->get(route('web.domains.create'))
        ->assertStatus(200)
        ->assertSee(__('Domain Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Create Domain'));
});

it('requires authentication to access create domain page', function () {
    $this->get(route('web.domains.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new domain', function () {
    $this->actingAs($this->user)
        ->post(route('web.domains.store'), [
            'name' => 'New Domain',
            'description' => 'New Description',
        ])
        ->assertRedirect(route('web.domains.index'))
        ->assertSessionHas('success', __('Domain created successfully.'));

    $this->assertDatabaseHas('domains', [
        'name' => 'New Domain',
        'description' => 'New Description',
    ]);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.domains.store'), [
            'description' => 'Test Description',
        ])
        ->assertSessionHasErrors('name');
});

it('validates name is unique when storing', function () {
    Domain::factory()->create(['name' => 'Existing Domain']);

    $this->actingAs($this->user)
        ->post(route('web.domains.store'), [
            'name' => 'Existing Domain',
            'description' => 'New Description',
        ])
        ->assertSessionHasErrors('name');
});

// Show Page Tests

it('displays domain show page', function () {
    $domain = Domain::factory()->create([
        'name' => 'Show Test Domain',
        'description' => 'Show Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertViewIs('pages.domains.show')
        ->assertSee('Show Test Domain');
});

it('displays domain name as title', function () {
    $domain = Domain::factory()->create([
        'name' => 'Title Test Domain',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSeeInOrder(['Title Test Domain', 'Test Description']);
});

it('displays domain description', function () {
    $domain = Domain::factory()->create([
        'name' => 'Domain Name',
        'description' => 'Unique Description Text',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee('Unique Description Text');
});

it('displays edit and delete buttons on show page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.domains.edit', $domain));
});

it('displays business terms section on show page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('Business Terms'));
});

it('displays associated business terms on show page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Business Term',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee('Test Business Term')
        ->assertSee('Test Definition')
        ->assertSee(route('web.business-assets.show', $asset));
});

it('displays empty message when no business terms exist', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('No business terms associated with this domain.'));
});

it('displays back to domains link on show page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('Back to Domains'))
        ->assertSee(route('web.domains.index'));
});

it('requires authentication to access domain show page', function () {
    $domain = Domain::factory()->create();

    $this->get(route('web.domains.show', $domain))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit domain page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.edit', $domain))
        ->assertStatus(200)
        ->assertViewIs('pages.domains.edit')
        ->assertSee(__('Edit Domain'));
});

it('displays domain form with pre-filled values on edit page', function () {
    $domain = Domain::factory()->create([
        'name' => 'Existing Domain',
        'description' => 'Existing Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.domains.edit', $domain))
        ->assertStatus(200)
        ->assertSee(__('Domain Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Update Domain'))
        ->assertSee('Existing Domain')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit domain page', function () {
    $domain = Domain::factory()->create();

    $this->get(route('web.domains.edit', $domain))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a domain', function () {
    $domain = Domain::factory()->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.domains.update', $domain), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ])
        ->assertRedirect(route('web.domains.index'))
        ->assertSessionHas('success', __('Domain updated successfully.'));

    $this->assertDatabaseHas('domains', [
        'id' => $domain->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ]);
});

it('validates name is required when updating', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.domains.update', $domain), [
            'description' => 'Updated Description',
        ])
        ->assertSessionHasErrors('name');
});

it('validates name is unique when updating', function () {
    $domain1 = Domain::factory()->create(['name' => 'Domain 1']);
    $domain2 = Domain::factory()->create(['name' => 'Domain 2']);

    $this->actingAs($this->user)
        ->put(route('web.domains.update', $domain1), [
            'name' => 'Domain 2',
            'description' => 'Updated Description',
        ])
        ->assertSessionHasErrors('name');
});

// Destroy Tests

it('can delete a domain', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.domains.destroy', $domain))
        ->assertRedirect(route('web.domains.index'))
        ->assertSessionHas('success', __('Domain deleted successfully.'));

    $this->assertDatabaseMissing('domains', ['id' => $domain->id]);
});

it('can delete domain', function () {
    $domain = Domain::factory()->create([
        'name' => 'Domain to Delete',
    ]);

    $this->actingAs($this->user)
        ->delete(route('web.domains.destroy', $domain))
        ->assertRedirect(route('web.domains.index'))
        ->assertSessionHas('success', __('Domain deleted successfully.'));

    $this->assertDatabaseMissing('domains', ['id' => $domain->id]);
});

// Pagination Tests

it('paginates domains', function () {
    Domain::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee('Next');
});

// Menu Link Test

it('has domains link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee(__('Domains'))
        ->assertSee(route('web.domains.index'));
});

// Team Management Tests

it('displays domain owner on index page', function () {
    $domain = Domain::factory()->create();
    $owner = User::factory()->create();
    $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

    $domain->assignRoleToUser($owner, $domainOwnerRole);

    $this->actingAs($this->user)
        ->get(route('web.domains.index'))
        ->assertStatus(200)
        ->assertSee($owner->name)
        ->assertSee(__('Domain Owner'));
});

it('displays domain owner on show page', function () {
    $domain = Domain::factory()->create();
    $owner = User::factory()->create();
    $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

    $domain->assignRoleToUser($owner, $domainOwnerRole);

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('Domain Owner'))
        ->assertSee($owner->name);
});

it('displays manage team button on show page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.show', $domain))
        ->assertStatus(200)
        ->assertSee(__('Manage Team'))
        ->assertSee(route('web.domains.team', $domain));
});

it('displays team management page', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.domains.team', $domain))
        ->assertStatus(200)
        ->assertViewIs('pages.domains.manage-team')
        ->assertSee(__('Manage Team'))
        ->assertSee(__('Domain Owner'));
});

it('can update domain owner via web form', function () {
    $domain = Domain::factory()->create();
    $newOwner = User::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.domains.team.update', $domain), [
            'domain_owner_id' => $newOwner->id,
        ])
        ->assertRedirect(route('web.domains.show', $domain))
        ->assertSessionHas('success', __('Team updated successfully.'));

    $this->assertDatabaseHas('role_assignments', [
        'roleable_id' => $domain->id,
        'roleable_type' => Domain::class,
        'user_id' => $newOwner->id,
    ]);
});

it('displays current domain owner on team management page', function () {
    $domain = Domain::factory()->create();
    $owner = User::factory()->create();
    $domainOwnerRole = Role::where('name', 'Domain Owner')->firstOrFail();

    $domain->assignRoleToUser($owner, $domainOwnerRole);

    $this->actingAs($this->user)
        ->get(route('web.domains.team', $domain))
        ->assertStatus(200)
        ->assertSee($owner->name)
        ->assertSee('selected');
});
