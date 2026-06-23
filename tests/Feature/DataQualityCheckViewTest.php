<?php

use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays data quality checks index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-quality-checks.index')
        ->assertSee(__('Data Quality Checks'));
});

it('displays empty state when no data quality checks exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertSee(__('No data quality checks found. Create one to get started.'));
});

it('displays data quality checks in a table', function () {
    $businessRule = BusinessRule::factory()->create();
    DataQualityCheck::factory()->create([
        'name' => 'Test Data Quality Check',
        'description' => 'Test Description',
        'business_rule_id' => $businessRule->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertSee('Test Data Quality Check')
        ->assertSee('Test Description')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Rule'))
        ->assertSee(__('Data Sources'))
        ->assertSee(__('Created At'))
        ->assertSee(__('Actions'));
});

it('displays data sources count for data quality check', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    DataSource::factory()->count(3)->create()->each(function ($dataSource) use ($dataQualityCheck) {
        $dataSource->dataQualityChecks()->attach($dataQualityCheck->id);
    });

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.data-quality-checks.create'));
});

it('displays edit and delete links for each data quality check', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.data-quality-checks.edit', $dataQualityCheck));
});

it('requires authentication to access data quality checks index', function () {
    $this->get(route('web.data-quality-checks.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create data quality check page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-quality-checks.create')
        ->assertSee(__('Create Data Quality Check'));
});

it('displays data quality check form with business rules and data sources on create page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataSource = DataSource::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.create'))
        ->assertStatus(200)
        ->assertSee(__('Data Quality Check Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Rule'))
        ->assertSee(__('Data Sources'))
        ->assertSee($businessRule->name)
        ->assertSee($dataSource->name);
});

it('requires authentication to access create data quality check page', function () {
    $this->get(route('web.data-quality-checks.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new data quality check', function () {
    $businessRule = BusinessRule::factory()->create();
    $data = [
        'name' => 'New Data Quality Check',
        'description' => 'New Description',
        'business_rule_id' => $businessRule->id,
    ];

    $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.store'), $data)
        ->assertRedirect(route('web.data-quality-checks.index'))
        ->assertSessionHas('success', __('Data quality check created successfully.'));

    $this->assertDatabaseHas('data_quality_checks', $data);
});

it('can store a new data quality check with data sources', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataSource = DataSource::factory()->create();
    $data = [
        'name' => 'New Data Quality Check',
        'description' => 'New Description',
        'business_rule_id' => $businessRule->id,
        'data_source_ids' => [$dataSource->id],
    ];

    $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.store'), $data)
        ->assertRedirect(route('web.data-quality-checks.index'))
        ->assertSessionHas('success', __('Data quality check created successfully.'));

    $this->assertDatabaseHas('data_quality_checks', [
        'name' => 'New Data Quality Check',
        'business_rule_id' => $businessRule->id,
    ]);

    $this->assertDatabaseHas('data_quality_check_data_source', [
        'data_quality_check_id' => 1,
        'data_source_id' => $dataSource->id,
    ]);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.store'), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

it('validates business rule is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-quality-checks.store'), ['name' => 'Test'])
        ->assertSessionHasErrors('business_rule_id');
});

// Show Page Tests

it('displays data quality check show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertViewIs('pages.data-quality-checks.show')
        ->assertSee($dataQualityCheck->name);
});

it('displays data quality check name as title', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create([
        'name' => 'Test Data Quality Check',
        'business_rule_id' => $businessRule->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee('Test Data Quality Check');
});

it('displays data quality check description', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create([
        'description' => 'Test Description',
        'business_rule_id' => $businessRule->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays edit and delete buttons on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.data-quality-checks.edit', $dataQualityCheck));
});

it('displays business rule section on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee(__('Business Rule'));
});

it('displays associated business rule on show page', function () {
    $businessRule = BusinessRule::factory()->create(['name' => 'Test Business Rule']);
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee('Test Business Rule');
});

it('displays data sources section on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee(__('Data Sources'));
});

it('displays associated data sources on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $dataSource = DataSource::factory()->create(['name' => 'Test Data Source']);
    $dataQualityCheck->dataSources()->attach($dataSource->id);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee('Test Data Source');
});

it('displays empty message when no data sources exist', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee(__('No data sources associated with this data quality check.'));
});

it('displays back to data quality checks link on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee(__('Back to Data Quality Checks'))
        ->assertSee(route('web.data-quality-checks.index'));
});

it('requires authentication to access data quality check show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->get(route('web.data-quality-checks.show', $dataQualityCheck))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit data quality check page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.edit', $dataQualityCheck))
        ->assertStatus(200)
        ->assertViewIs('pages.data-quality-checks.edit')
        ->assertSee(__('Edit Data Quality Check'));
});

it('displays data quality check form with pre-filled values on edit page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create([
        'name' => 'Existing Data Quality Check',
        'description' => 'Existing Description',
        'business_rule_id' => $businessRule->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.edit', $dataQualityCheck))
        ->assertStatus(200)
        ->assertSee('Existing Data Quality Check')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit data quality check page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->get(route('web.data-quality-checks.edit', $dataQualityCheck))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a data quality check', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $data = [
        'name' => 'Updated Data Quality Check',
        'description' => 'Updated Description',
        'business_rule_id' => $businessRule->id,
    ];

    $this->actingAs($this->user)
        ->put(route('web.data-quality-checks.update', $dataQualityCheck), $data)
        ->assertRedirect(route('web.data-quality-checks.index'))
        ->assertSessionHas('success', __('Data quality check updated successfully.'));

    $this->assertDatabaseHas('data_quality_checks', $data);
});

it('validates name is required when updating', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->put(route('web.data-quality-checks.update', $dataQualityCheck), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Delete Tests

it('can delete a data quality check', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataQualityCheck = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->delete(route('web.data-quality-checks.destroy', $dataQualityCheck))
        ->assertRedirect(route('web.data-quality-checks.index'))
        ->assertSessionHas('success', __('Data quality check deleted successfully.'));

    $this->assertDatabaseMissing('data_quality_checks', ['id' => $dataQualityCheck->id]);
});

// Pagination Test

it('paginates data quality checks', function () {
    $businessRule = BusinessRule::factory()->create();
    DataQualityCheck::factory()->count(15)->create(['business_rule_id' => $businessRule->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-quality-checks.index'))
        ->assertStatus(200);
});
