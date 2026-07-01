<?php

use App\Models\BusinessObjective;
use App\Models\KPI;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays kpis index page', function () {
    KPI::factory()->count(3)->create();

    $response = $this->get(route('web.kpis.index'));

    $response->assertStatus(200)
        ->assertViewIs('pages.kpis.index')
        ->assertViewHas('kpis');
});

it('displays create kpi page', function () {
    $response = $this->get(route('web.kpis.create'));

    $response->assertStatus(200)
        ->assertViewIs('pages.kpis.create')
        ->assertViewHas('businessObjectives');
});

it('creates a new kpi', function () {
    $businessObjective = BusinessObjective::factory()->create();

    $data = [
        'name' => 'Test KPI',
        'description' => 'Test Description',
        'business_objective_id' => $businessObjective->id,
    ];

    $response = $this->post(route('web.kpis.store'), $data);

    $response->assertRedirect(route('web.kpis.index'))
        ->assertSessionHas('success', __('KPI created successfully.'));

    $this->assertDatabaseHas('k_p_i_s', $data);
});

it('validates kpi creation', function () {
    $response = $this->post(route('web.kpis.store'), []);

    $response->assertSessionHasErrors(['name', 'business_objective_id']);
});

it('displays kpi show page', function () {
    $kpi = KPI::factory()->create();

    $response = $this->get(route('web.kpis.show', $kpi));

    $response->assertStatus(200)
        ->assertViewIs('pages.kpis.show')
        ->assertViewHas('kpi');
});

it('displays kpi edit page', function () {
    $kpi = KPI::factory()->create();

    $response = $this->get(route('web.kpis.edit', $kpi));

    $response->assertStatus(200)
        ->assertViewIs('pages.kpis.edit')
        ->assertViewHas('kpi')
        ->assertViewHas('businessObjectives');
});

it('updates a kpi', function () {
    $kpi = KPI::factory()->create();
    $businessObjective = BusinessObjective::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'business_objective_id' => $businessObjective->id,
    ];

    $response = $this->put(route('web.kpis.update', $kpi), $updatedData);

    $response->assertRedirect(route('web.kpis.index'))
        ->assertSessionHas('success', __('KPI updated successfully.'));

    $this->assertDatabaseHas('k_p_i_s', $updatedData);
});

it('validates kpi update', function () {
    $kpi = KPI::factory()->create();

    $response = $this->put(route('web.kpis.update', $kpi), []);

    $response->assertSessionHasErrors(['name', 'business_objective_id']);
});

it('deletes a kpi', function () {
    $kpi = KPI::factory()->create();

    $response = $this->delete(route('web.kpis.destroy', $kpi));

    $response->assertRedirect(route('web.kpis.index'))
        ->assertSessionHas('success', __('KPI deleted successfully.'));

    $this->assertDatabaseMissing('k_p_i_s', ['id' => $kpi->id]);
});

it('shows business objective associated with kpi', function () {
    $businessObjective = BusinessObjective::factory()->create();
    $kpi = KPI::factory()->create(['business_objective_id' => $businessObjective->id]);

    $response = $this->get(route('web.kpis.show', $kpi));

    $response->assertStatus(200)
        ->assertViewHas('kpi');

    $this->assertEquals($businessObjective->id, $kpi->fresh()->business_objective_id);
});

it('displays kpis associated with business objective', function () {
    $businessObjective = BusinessObjective::factory()->create();
    $kpi1 = KPI::factory()->create(['business_objective_id' => $businessObjective->id]);
    $kpi2 = KPI::factory()->create(['business_objective_id' => $businessObjective->id]);

    $response = $this->get(route('web.business-objectives.show', $businessObjective));

    $response->assertStatus(200)
        ->assertViewHas('businessObjective');

    $this->assertCount(2, $businessObjective->fresh()->kpis);
});

it('paginates kpis', function () {
    KPI::factory()->count(15)->create();

    $response = $this->get(route('web.kpis.index'));

    $response->assertStatus(200);
    $response->assertViewHas('kpis');
});

it('shows kpi count on business objectives index', function () {
    $businessObjective = BusinessObjective::factory()->create();
    KPI::factory()->count(3)->create(['business_objective_id' => $businessObjective->id]);

    $response = $this->get(route('web.business-objectives.index'));

    $response->assertStatus(200)
        ->assertViewHas('businessObjectives');
});
