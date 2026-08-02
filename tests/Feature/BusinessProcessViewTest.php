<?php

use App\Models\BusinessAsset;
use App\Models\BusinessProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays business processes index page', function () {
    BusinessProcess::factory()->count(3)->create();

    $response = $this->get(route('web.business-processes.index'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-processes.index')
        ->assertViewHas('businessProcesses');
});

it('displays create business process page', function () {
    $response = $this->get(route('web.business-processes.create'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-processes.create')
        ->assertViewHas('businessAssets');
});

it('creates a new business process', function () {
    $data = [
        'name' => 'Test Business Process',
        'description' => 'Test Description',
    ];

    $response = $this->post(route('web.business-processes.store'), $data);

    $response->assertRedirect(route('web.business-processes.index'))
        ->assertSessionHas('success', __('Business Process created successfully.'));

    $this->assertDatabaseHas('business_processes', $data);
});

it('creates a business process with business assets', function () {
    $businessAssets = BusinessAsset::factory()->count(3)->create();

    $data = [
        'name' => 'Test Business Process with Assets',
        'description' => 'Test Description',
        'business_asset_ids' => $businessAssets->pluck('id')->toArray(),
    ];

    $response = $this->post(route('web.business-processes.store'), $data);

    $response->assertRedirect(route('web.business-processes.index'))
        ->assertSessionHas('success', __('Business Process created successfully.'));

    $businessProcess = BusinessProcess::where('name', 'Test Business Process with Assets')->first();
    expect($businessProcess)->not()->toBeNull();
    expect($businessProcess->businessAssets->pluck('id')->toArray())->toBe($businessAssets->pluck('id')->toArray());
});

it('validates business process creation', function () {
    $response = $this->post(route('web.business-processes.store'), []);

    $response->assertSessionHasErrors(['name']);
});

it('displays business process show page', function () {
    $businessProcess = BusinessProcess::factory()->create();

    $response = $this->get(route('web.business-processes.show', $businessProcess));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-processes.show')
        ->assertViewHas('businessProcess');
});

it('displays business process edit page', function () {
    $businessProcess = BusinessProcess::factory()->create();

    $response = $this->get(route('web.business-processes.edit', $businessProcess));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-processes.edit')
        ->assertViewHas('businessProcess')
        ->assertViewHas('businessAssets');
});

it('updates a business process', function () {
    $businessProcess = BusinessProcess::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->put(route('web.business-processes.update', $businessProcess), $updatedData);

    $response->assertRedirect(route('web.business-processes.index'))
        ->assertSessionHas('success', __('Business Process updated successfully.'));

    $this->assertDatabaseHas('business_processes', $updatedData);
});

it('updates a business process with business assets', function () {
    $businessProcess = BusinessProcess::factory()->create();
    $businessAssets = BusinessAsset::factory()->count(2)->create();

    $updatedData = [
        'name' => 'Updated Name with Assets',
        'description' => 'Updated Description',
        'business_asset_ids' => $businessAssets->pluck('id')->toArray(),
    ];

    $response = $this->put(route('web.business-processes.update', $businessProcess), $updatedData);

    $response->assertRedirect(route('web.business-processes.index'))
        ->assertSessionHas('success', __('Business Process updated successfully.'));

    expect($businessProcess->fresh()->businessAssets->pluck('id')->toArray())->toBe($businessAssets->pluck('id')->toArray());
});

it('deletes a business process', function () {
    $businessProcess = BusinessProcess::factory()->create();

    $response = $this->delete(route('web.business-processes.destroy', $businessProcess));

    $response->assertRedirect(route('web.business-processes.index'))
        ->assertSessionHas('success', __('Business Process deleted successfully.'));

    $this->assertDatabaseMissing('business_processes', ['id' => $businessProcess->id]);
});

it('shows business assets associated with business process', function () {
    $businessProcess = BusinessProcess::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $businessProcess->businessAssets()->attach([$asset1->id, $asset2->id]);

    $response = $this->get(route('web.business-processes.show', $businessProcess));

    $response->assertStatus(200)
        ->assertViewHas('businessProcess');

    $this->assertCount(2, $businessProcess->fresh()->businessAssets);
});

it('paginates business processes', function () {
    BusinessProcess::factory()->count(15)->create();

    $response = $this->get(route('web.business-processes.index'));

    $response->assertStatus(200);
    $response->assertViewHas('businessProcesses');
});
