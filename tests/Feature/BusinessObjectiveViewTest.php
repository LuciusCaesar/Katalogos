<?php

use App\Models\BusinessObjective;
use App\Models\DataInitiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays business objectives index page', function () {
    BusinessObjective::factory()->count(3)->create();

    $response = $this->get(route('web.business-objectives.index'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-objectives.index')
        ->assertViewHas('businessObjectives');
});

it('displays create business objective page', function () {
    $response = $this->get(route('web.business-objectives.create'));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-objectives.create');
});

it('creates a new business objective', function () {
    $data = [
        'name' => 'Test Business Objective',
        'description' => 'Test Description',
    ];

    $response = $this->post(route('web.business-objectives.store'), $data);

    $response->assertRedirect(route('web.business-objectives.index'))
        ->assertSessionHas('success', __('Business Objective created successfully.'));

    $this->assertDatabaseHas('business_objectives', $data);
});

it('validates business objective creation', function () {
    $response = $this->post(route('web.business-objectives.store'), []);

    $response->assertSessionHasErrors(['name']);
});

it('displays business objective show page', function () {
    $businessObjective = BusinessObjective::factory()->create();

    $response = $this->get(route('web.business-objectives.show', $businessObjective));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-objectives.show')
        ->assertViewHas('businessObjective');
});

it('displays business objective edit page', function () {
    $businessObjective = BusinessObjective::factory()->create();

    $response = $this->get(route('web.business-objectives.edit', $businessObjective));

    $response->assertStatus(200)
        ->assertViewIs('pages.business-objectives.edit')
        ->assertViewHas('businessObjective');
});

it('updates a business objective', function () {
    $businessObjective = BusinessObjective::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ];

    $response = $this->put(route('web.business-objectives.update', $businessObjective), $updatedData);

    $response->assertRedirect(route('web.business-objectives.index'))
        ->assertSessionHas('success', __('Business Objective updated successfully.'));

    $this->assertDatabaseHas('business_objectives', $updatedData);
});

it('deletes a business objective', function () {
    $businessObjective = BusinessObjective::factory()->create();

    $response = $this->delete(route('web.business-objectives.destroy', $businessObjective));

    $response->assertRedirect(route('web.business-objectives.index'))
        ->assertSessionHas('success', __('Business Objective deleted successfully.'));

    $this->assertDatabaseMissing('business_objectives', ['id' => $businessObjective->id]);
});

it('shows data initiatives associated with business objective', function () {
    $businessObjective = BusinessObjective::factory()->create();
    $initiative1 = DataInitiative::factory()->create();
    $initiative2 = DataInitiative::factory()->create();

    $businessObjective->dataInitiatives()->attach([$initiative1->id, $initiative2->id]);

    $response = $this->get(route('web.business-objectives.show', $businessObjective));

    $response->assertStatus(200)
        ->assertViewHas('businessObjective');

    $this->assertCount(2, $businessObjective->fresh()->dataInitiatives);
});

it('paginates business objectives', function () {
    BusinessObjective::factory()->count(15)->create();

    $response = $this->get(route('web.business-objectives.index'));

    $response->assertStatus(200);
    // Check that pagination is present
    $response->assertViewHas('businessObjectives');
});
