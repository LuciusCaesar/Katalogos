<?php

use App\Models\BusinessAsset;
use App\Models\BusinessEvent;
use App\Models\BusinessProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays business events index page', function () {
    BusinessEvent::factory()->count(3)->create();

    $response = $this->get(route('web.business-events.index'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-events.index')
        ->assertViewHas('businessEvents');
});

it('displays create business event page', function () {
    $response = $this->get(route('web.business-events.create'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-events.create')
        ->assertViewHas('businessAssets')
        ->assertViewHas('businessProcesses');
});

it('creates a new business event', function () {
    $data = [
        'name' => 'Test Business Event',
        'description' => 'Test Description',
    ];

    $response = $this->post(route('web.business-events.store'), $data);

    $response->assertRedirect(route('web.business-events.index'))
        ->assertSessionHas('success', __('Business Event created successfully.'));

    $this->assertDatabaseHas('business_events', $data);
});

it('creates a business event with business assets and business processes', function () {
    $businessAssets = BusinessAsset::factory()->count(2)->create();
    $businessProcesses = BusinessProcess::factory()->count(2)->create();

    $data = [
        'name' => 'Test Business Event with Relations',
        'description' => 'Test Description',
        'business_asset_ids' => $businessAssets->pluck('id')->toArray(),
        'business_process_ids' => $businessProcesses->pluck('id')->toArray(),
    ];

    $response = $this->post(route('web.business-events.store'), $data);

    $response->assertRedirect(route('web.business-events.index'))
        ->assertSessionHas('success', __('Business Event created successfully.'));

    $businessEvent = BusinessEvent::where('name', 'Test Business Event with Relations')->first();
    expect($businessEvent)->not()->toBeNull();
    expect($businessEvent->businessAssets->pluck('id')->toArray())->toBe($businessAssets->pluck('id')->toArray());
    expect($businessEvent->businessProcesses->pluck('id')->toArray())->toBe($businessProcesses->pluck('id')->toArray());
});

it('validates business event creation', function () {
    $response = $this->post(route('web.business-events.store'), []);

    $response->assertSessionHasErrors(['name']);
});

it('displays business event show page', function () {
    $businessEvent = BusinessEvent::factory()->create();

    $response = $this->get(route('web.business-events.show', $businessEvent));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-events.show')
        ->assertViewHas('businessEvent');
});

it('displays business event edit page', function () {
    $businessEvent = BusinessEvent::factory()->create();

    $response = $this->get(route('web.business-events.edit', $businessEvent));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-events.edit')
        ->assertViewHas('businessEvent')
        ->assertViewHas('businessAssets')
        ->assertViewHas('businessProcesses');
});

it('updates a business event', function () {
    $businessEvent = BusinessEvent::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->put(route('web.business-events.update', $businessEvent), $updatedData);

    $response->assertRedirect(route('web.business-events.index'))
        ->assertSessionHas('success', __('Business Event updated successfully.'));

    $this->assertDatabaseHas('business_events', $updatedData);
});

it('updates a business event with business assets and business processes', function () {
    $businessEvent = BusinessEvent::factory()->create();
    $businessAssets = BusinessAsset::factory()->count(2)->create();
    $businessProcesses = BusinessProcess::factory()->count(2)->create();

    $updatedData = [
        'name' => 'Updated Name with Relations',
        'description' => 'Updated Description',
        'business_asset_ids' => $businessAssets->pluck('id')->toArray(),
        'business_process_ids' => $businessProcesses->pluck('id')->toArray(),
    ];

    $response = $this->put(route('web.business-events.update', $businessEvent), $updatedData);

    $response->assertRedirect(route('web.business-events.index'))
        ->assertSessionHas('success', __('Business Event updated successfully.'));

    expect($businessEvent->fresh()->businessAssets->pluck('id')->toArray())->toBe($businessAssets->pluck('id')->toArray());
    expect($businessEvent->fresh()->businessProcesses->pluck('id')->toArray())->toBe($businessProcesses->pluck('id')->toArray());
});

it('deletes a business event', function () {
    $businessEvent = BusinessEvent::factory()->create();

    $response = $this->delete(route('web.business-events.destroy', $businessEvent));

    $response->assertRedirect(route('web.business-events.index'))
        ->assertSessionHas('success', __('Business Event deleted successfully.'));

    $this->assertDatabaseMissing('business_events', ['id' => $businessEvent->id]);
});

it('shows business assets and business processes associated with business event', function () {
    $businessEvent = BusinessEvent::factory()->create();
    $asset = BusinessAsset::factory()->create();
    $process = BusinessProcess::factory()->create();

    $businessEvent->businessAssets()->attach($asset->id);
    $businessEvent->businessProcesses()->attach($process->id);

    $response = $this->get(route('web.business-events.show', $businessEvent));

    $response->assertStatus(200)
        ->assertViewHas('businessEvent');

    $this->assertCount(1, $businessEvent->fresh()->businessAssets);
    $this->assertCount(1, $businessEvent->fresh()->businessProcesses);
});

it('paginates business events', function () {
    BusinessEvent::factory()->count(15)->create();

    $response = $this->get(route('web.business-events.index'));

    $response->assertStatus(200);
    $response->assertViewHas('businessEvents');
});
