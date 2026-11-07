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

it('displays domain in table when available', function () {
    $domain = Domain::factory()->create(['name' => 'Test Domain']);
    $dataInitiative = DataInitiative::factory()->create();

    BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('Test Domain')
        ->assertSee(__('Domain'));
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

    $asset->assignDataSteward($steward);
    $asset->assignDataOwner($owner);

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

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.business-assets.create'));
});

it('requires authentication to access business assets index', function () {
    $this->get(route('web.business-assets.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create business asset page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.create')
        ->assertSee(__('Create Business Asset'));
});

it('displays business asset form on create page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.create'))
        ->assertStatus(200)
        ->assertSee(__('Business Asset Name'))
        ->assertSee(__('Definition'))
        ->assertSee(__('Domain'))
        ->assertSee(__('Data Initiative'))
        ->assertSee(__('Create Business Asset'));
});

it('requires authentication to access create business asset page', function () {
    $this->get(route('web.business-assets.create'))
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
    $asset->assignDataSteward($steward);

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
    $asset->assignDataOwner($owner);

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

it('displays domain in right column card', function () {
    $domain = Domain::factory()->create(['name' => 'Test Domain']);
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Domain Test Asset',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Domain'))
        ->assertSee('Test Domain');
});

it('displays categorization level on show page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Categorized Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
        'consistency' => 'high',
        'integrity' => 'middle',
        'availability' => 'low',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Categorization Level'))
        ->assertSee(__('Consistency'))
        ->assertSee(__('Integrity'))
        ->assertSee(__('Availability'))
        ->assertSee('High')
        ->assertSee('Middle')
        ->assertSee('Low');
});

it('displays dash for null categorization values on show page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $asset = BusinessAsset::factory()->create([
        'name' => 'Uncategorized Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset))
        ->assertStatus(200)
        ->assertSee(__('Categorization Level'))
        ->assertSee(__('Consistency'))
        ->assertSee(__('Integrity'))
        ->assertSee(__('Availability'))
        ->assertSee('-');
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

it('displays edit and delete buttons on show page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $businessAsset))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.business-assets.edit', $businessAsset));
});

it('displays back to business assets link on show page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $businessAsset))
        ->assertStatus(200)
        ->assertSee(__('Back to Business Assets'))
        ->assertSee(route('web.business-assets.index'));
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

it('does not show user as data owner when only assigned as data steward on different asset', function () {
    $steward = User::factory()->create(['name' => 'Cross Asset Steward']);
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    $asset1 = BusinessAsset::factory()->create([
        'name' => 'Asset One',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    $asset2 = BusinessAsset::factory()->create([
        'name' => 'Asset Two',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    // Assign steward as Data Steward on Asset 1
    $asset1->assignDataSteward($steward);

    // Assign same user as Data Owner on Asset 2
    $asset2->assignDataOwner($steward);

    // Verify show page for Asset 1 only shows steward, not owner
    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset1))
        ->assertStatus(200)
        ->assertSee('Cross Asset Steward')
        ->assertSeeInOrder([__('Data Steward'), 'Cross Asset Steward'])
        ->assertSeeInOrder([__('Data Owner'), '-']);

    // Verify show page for Asset 2 only shows owner, not steward
    $this->actingAs($this->user)
        ->get(route('web.business-assets.show', $asset2))
        ->assertStatus(200)
        ->assertSee('Cross Asset Steward')
        ->assertSeeInOrder([__('Data Owner'), 'Cross Asset Steward'])
        ->assertSeeInOrder([__('Data Steward'), '-']);
});

// Update Tests

it('can update a business asset', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $newDomain = Domain::factory()->create();
    $newDataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Original Name',
        'definition' => 'Original Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.update', $businessAsset), [
            'name' => 'Updated Name',
            'definition' => 'Updated Definition',
            'data_initiative_id' => $newDataInitiative->id,
            'domain_id' => $newDomain->id,
        ])
        ->assertRedirect(route('web.business-assets.index'))
        ->assertSessionHas('success', __('Business Asset updated successfully.'));

    $this->assertDatabaseHas('business_assets', [
        'id' => $businessAsset->id,
        'name' => 'Updated Name',
        'definition' => 'Updated Definition',
        'data_initiative_id' => $newDataInitiative->id,
        'domain_id' => $newDomain->id,
    ]);
});

it('validates name is required when updating', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.update', $businessAsset), [
            'definition' => 'Updated Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
        ])
        ->assertSessionHasErrors('name');
});

it('validates definition is required when updating', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.update', $businessAsset), [
            'name' => 'Updated Name',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
        ])
        ->assertSessionHasErrors('definition');
});

// Destroy Tests

it('can delete a business asset', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Business Asset to Delete',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('web.business-assets.destroy', $businessAsset))
        ->assertRedirect(route('web.business-assets.index'))
        ->assertSessionHas('success', __('Business Asset deleted successfully.'));

    $this->assertDatabaseMissing('business_assets', ['id' => $businessAsset->id]);
});

// Pagination Tests

it('paginates business assets', function () {
    Domain::factory()->create();
    DataInitiative::factory()->create();

    BusinessAsset::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.business-assets.index'))
        ->assertStatus(200)
        ->assertSee('Next');
});

// Menu Link Test

it('has business assets link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee(route('web.business-assets.index'));
});

// Store Tests

it('can store a new business asset', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'New Business Asset',
            'definition' => 'New Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
        ])
        ->assertRedirect(route('web.business-assets.index'))
        ->assertSessionHas('success', __('Business Asset created successfully.'));

    $this->assertDatabaseHas('business_assets', [
        'name' => 'New Business Asset',
        'definition' => 'New Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);
});

it('validates name is required when storing', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
        ])
        ->assertSessionHasErrors('name');
});

it('validates definition is required when storing', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
        ])
        ->assertSessionHasErrors('definition');
});

it('validates data_initiative_id is required when storing', function () {
    $domain = Domain::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'definition' => 'Test Definition',
            'domain_id' => $domain->id,
        ])
        ->assertSessionHasErrors('data_initiative_id');
});

it('validates domain_id is required when storing', function () {
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
        ])
        ->assertSessionHasErrors('domain_id');
});

// Edit Page Tests

it('displays edit business asset page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.edit', $businessAsset))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.edit')
        ->assertSee(__('Edit Business Asset'));
});

it('displays business asset form with pre-filled values on edit page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Existing Business Asset',
        'definition' => 'Existing Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.edit', $businessAsset))
        ->assertStatus(200)
        ->assertSee(__('Business Asset Name'))
        ->assertSee(__('Definition'))
        ->assertSee(__('Domain'))
        ->assertSee(__('Data Initiative'))
        ->assertSee(__('Update Business Asset'))
        ->assertSee('Existing Business Asset')
        ->assertSee('Existing Definition')
        ->assertSee('selected', false); // Verify select dropdowns are present
});

it('requires authentication to access edit business asset page', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
    ]);

    $this->get(route('web.business-assets.edit', $businessAsset))
        ->assertRedirect(route('login'));
});

// Categorization Tests

it('displays categorization fields on create form', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-assets.create'))
        ->assertStatus(200)
        ->assertSee(__('Categorization Level'))
        ->assertSee(__('Consistency'))
        ->assertSee(__('Integrity'))
        ->assertSee(__('Availability'))
        ->assertSee(__('Low'))
        ->assertSee(__('Middle'))
        ->assertSee(__('High'));
});

it('displays categorization fields on edit form', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
        'consistency' => 'high',
        'integrity' => 'middle',
        'availability' => 'low',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('web.business-assets.edit', $businessAsset));

    $response->assertStatus(200)
        ->assertSee(__('Categorization Level'))
        ->assertSee(__('Consistency'))
        ->assertSee(__('Integrity'))
        ->assertSee(__('Availability'));

    $response->assertSee('value="high" selected', false);
    $response->assertSee('value="middle" selected', false);
    $response->assertSee('value="low" selected', false);
});

it('can store business asset with categorization', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Categorized Asset',
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
            'consistency' => 'high',
            'integrity' => 'middle',
            'availability' => 'low',
        ])
        ->assertRedirect(route('web.business-assets.index'))
        ->assertSessionHas('success', __('Business Asset created successfully.'));

    $this->assertDatabaseHas('business_assets', [
        'name' => 'Categorized Asset',
        'consistency' => 'high',
        'integrity' => 'middle',
        'availability' => 'low',
    ]);
});

it('can update business asset categorization', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $businessAsset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'domain_id' => $domain->id,
        'data_initiative_id' => $dataInitiative->id,
        'consistency' => 'low',
        'integrity' => 'low',
        'availability' => 'low',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.business-assets.update', $businessAsset), [
            'name' => 'Updated Asset',
            'definition' => 'Updated Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
            'consistency' => 'high',
            'integrity' => 'high',
            'availability' => 'high',
        ])
        ->assertRedirect(route('web.business-assets.index'))
        ->assertSessionHas('success', __('Business Asset updated successfully.'));

    $this->assertDatabaseHas('business_assets', [
        'id' => $businessAsset->id,
        'consistency' => 'high',
        'integrity' => 'high',
        'availability' => 'high',
    ]);
});

it('validates consistency is valid value when storing', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
            'consistency' => 'invalid',
        ])
        ->assertSessionHasErrors('consistency');
});

it('validates integrity is valid value when storing', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
            'integrity' => 'invalid',
        ])
        ->assertSessionHasErrors('integrity');
});

it('validates availability is valid value when storing', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->post(route('web.business-assets.store'), [
            'name' => 'Test Asset',
            'definition' => 'Test Definition',
            'data_initiative_id' => $dataInitiative->id,
            'domain_id' => $domain->id,
            'availability' => 'invalid',
        ])
        ->assertSessionHasErrors('availability');
});

// Governance Score Pages Tests

it('displays governance score details page', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Governed Asset']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.governance-score.details', $asset))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.governance-score-details')
        ->assertSee(__('Governance Score Details'))
        ->assertSee('Governed Asset')
        ->assertSee('41.4%') // 6.0 / 14.5 baseline score of a factory asset
        ->assertSee(__('Current Governance Score'))
        ->assertSee(__('Scoring Criteria'))
        ->assertSee('Has Name')
        ->assertSee(__('Met'))
        ->assertSee(__('Not Met')) // a factory asset has no data steward
        ->assertSee(__('View History'))
        ->assertSee(__('Back to Business Asset'))
        ->assertSee(route('web.business-assets.show', $asset));
});

it('displays governance score history page', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Governed Asset']);

    $this->actingAs($this->user)
        ->get(route('web.business-assets.governance-score.history', $asset))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.governance-score-history')
        ->assertSee(__('Governance Score History'))
        ->assertSee('Governed Asset')
        ->assertSee('41.4%')
        ->assertSee(__('Score History'))
        ->assertSee(__('View Details'))
        ->assertSee(__('View Current Details'))
        ->assertSee(__('Back to Business Asset'));
});

it('displays empty state for governance score history', function () {
    $asset = BusinessAsset::factory()->create();
    $asset->governanceScores()->delete();

    $this->actingAs($this->user)
        ->get(route('web.business-assets.governance-score.history', $asset))
        ->assertStatus(200)
        ->assertSee(__('No governance score history available for this business asset.'));
});

it('displays details of a specific governance score', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Governed Asset']);
    $score = $asset->governanceScore;

    $this->actingAs($this->user)
        ->get(route('web.business-assets.governance-score.show', [$asset, $score]))
        ->assertStatus(200)
        ->assertViewIs('pages.business-assets.governance-score-show')
        ->assertSee(__('Governance Score Details'))
        ->assertSee('Governed Asset')
        ->assertSee('41.4%')
        ->assertSee(__('Score Summary'))
        ->assertSee(__('Scoring Criteria'))
        ->assertSee(__('Back to History'))
        ->assertSee(__('Back to Business Asset'))
        ->assertSee(route('web.business-assets.governance-score.history', $asset));
});

it('returns 404 when the governance score belongs to another asset', function () {
    $asset = BusinessAsset::factory()->create();
    $otherAsset = BusinessAsset::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-assets.governance-score.show', [$asset, $otherAsset->governanceScore]))
        ->assertNotFound();
});
