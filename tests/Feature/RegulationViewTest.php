<?php

use App\Models\BusinessAsset;
use App\Models\Regulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays regulations index page', function () {
    Regulation::factory()->count(3)->create();

    $response = $this->get(route('web.regulations.index'));

    $response->assertStatus(200)
        ->assertViewIs('pages.regulations.index')
        ->assertViewHas('regulations');
});

it('displays create regulation form', function () {
    $response = $this->get(route('web.regulations.create'));

    $response->assertStatus(200)
        ->assertViewIs('pages.regulations.create')
        ->assertViewHas('businessAssets');
});

it('creates a regulation', function () {
    $asset = BusinessAsset::factory()->create();

    $data = [
        'name' => 'Test Regulation',
        'description' => 'Test Description',
        'documentation_url' => 'https://example.com',
        'business_asset_ids' => [$asset->id],
    ];

    $response = $this->post(route('web.regulations.store'), $data);

    $response->assertRedirect(route('web.regulations.index'))
        ->assertSessionHas('success', __('Regulation created successfully.'));

    $this->assertDatabaseHas('regulations', [
        'name' => 'Test Regulation',
        'description' => 'Test Description',
        'documentation_url' => 'https://example.com',
    ]);

    $regulation = Regulation::where('name', 'Test Regulation')->first();
    $this->assertCount(1, $regulation->businessAssets);
});

it('displays regulation show page', function () {
    $regulation = Regulation::factory()->create();

    $response = $this->get(route('web.regulations.show', $regulation));

    $response->assertStatus(200)
        ->assertViewIs('pages.regulations.show')
        ->assertViewHas('regulation');
});

it('displays regulation edit page', function () {
    $regulation = Regulation::factory()->create();

    $response = $this->get(route('web.regulations.edit', $regulation));

    $response->assertStatus(200)
        ->assertViewIs('pages.regulations.edit')
        ->assertViewHas('regulation')
        ->assertViewHas('businessAssets');
});

it('updates a regulation', function () {
    $regulation = Regulation::factory()->create();
    $asset = BusinessAsset::factory()->create();

    $data = [
        'name' => 'Updated Regulation',
        'description' => 'Updated Description',
        'documentation_url' => 'https://updated.com',
        'business_asset_ids' => [$asset->id],
    ];

    $response = $this->put(route('web.regulations.update', $regulation), $data);

    $response->assertRedirect(route('web.regulations.index'))
        ->assertSessionHas('success', __('Regulation updated successfully.'));

    $this->assertDatabaseHas('regulations', [
        'id' => $regulation->id,
        'name' => 'Updated Regulation',
        'description' => 'Updated Description',
        'documentation_url' => 'https://updated.com',
    ]);

    $regulation->refresh();
    $this->assertCount(1, $regulation->businessAssets);
});

it('deletes a regulation', function () {
    $regulation = Regulation::factory()->create();

    $response = $this->delete(route('web.regulations.destroy', $regulation));

    $response->assertRedirect(route('web.regulations.index'))
        ->assertSessionHas('success', __('Regulation deleted successfully.'));

    $this->assertDatabaseMissing('regulations', ['id' => $regulation->id]);
});

it('displays empty message when no regulations exist', function () {
    $response = $this->get(route('web.regulations.index'));

    $response->assertStatus(200)
        ->assertSee(__('No regulations found. Create one to get started.'));
});

it('displays regulation with associated business assets', function () {
    $regulation = Regulation::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $regulation->businessAssets()->attach([$asset1->id, $asset2->id]);

    $response = $this->get(route('web.regulations.show', $regulation));

    $response->assertStatus(200)
        ->assertViewHas('regulation');

    $this->assertCount(2, $regulation->fresh()->businessAssets);
});

it('shows business assets count on index page', function () {
    $regulation = Regulation::factory()->create();
    $asset1 = BusinessAsset::factory()->create();
    $asset2 = BusinessAsset::factory()->create();

    $regulation->businessAssets()->attach([$asset1->id, $asset2->id]);

    $response = $this->get(route('web.regulations.index'));

    $response->assertStatus(200);
    $response->assertSee('2');
});
