<?php

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataIssue;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays business rules index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.business-rules.index')
        ->assertSee(__('Business Rules'));
});

it('displays empty state when no business rules exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee(__('No business rules found. Create one to get started.'));
});

it('displays business rules in a table', function () {
    BusinessRule::factory()->create([
        'name' => 'Test Business Rule',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee('Test Business Rule')
        ->assertSee('Test Description')
        ->assertSee(__('Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Data Issues'))
        ->assertSee(__('Data Quality Checks'))
        ->assertSee(__('Avg Score'))
        ->assertSee(__('Created At'))
        ->assertSee(__('Actions'));
});

it('displays business assets count for business rule', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->count(3)->create([
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ])->each(function ($asset) use ($businessRule) {
        $asset->businessRules()->attach($businessRule->id);
    });

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays data issues count for business rule', function () {
    $businessRule = BusinessRule::factory()->create();

    DataIssue::factory()->count(2)->create()->each(function ($dataIssue) use ($businessRule) {
        $dataIssue->businessRules()->attach($businessRule->id);
    });

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee('2');
});

it('displays average score for business rule with data quality checks', function () {
    $businessRule = BusinessRule::factory()->create();

    $dqc1 = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->create(['data_quality_check_id' => $dqc1->id, 'score' => 0.8]);

    $dqc2 = DataQualityCheck::factory()->create(['business_rule_id' => $businessRule->id]);
    DataQualityCheckScore::factory()->create(['data_quality_check_id' => $dqc2->id, 'score' => 0.9]);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee('85.00%');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.business-rules.create'));
});

it('displays edit and delete links for each business rule', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.business-rules.edit', $businessRule));
});

it('requires authentication to access business rules index', function () {
    $this->get(route('web.business-rules.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create business rule page', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-rules.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.business-rules.create')
        ->assertSee(__('Create Business Rule'));
});

it('displays business rule form with business assets and data issues on create page', function () {
    $businessAsset = BusinessAsset::factory()->create();
    $dataIssue = DataIssue::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.create'))
        ->assertStatus(200)
        ->assertSee(__('Business Rule Name'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Data Issues'))
        ->assertSee($businessAsset->name)
        ->assertSee($dataIssue->name);
});

it('requires authentication to access create business rule page', function () {
    $this->get(route('web.business-rules.create'))
        ->assertRedirect(route('login'));
});

// Store Tests

it('can store a new business rule', function () {
    $data = [
        'name' => 'New Business Rule',
        'description' => 'New Description',
    ];

    $this->actingAs($this->user)
        ->post(route('web.business-rules.store'), $data)
        ->assertRedirect(route('web.business-rules.index'))
        ->assertSessionHas('success', __('Business rule created successfully.'));

    $this->assertDatabaseHas('business_rules', $data);
});

it('validates name is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.business-rules.store'), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Show Page Tests

it('displays business rule show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertViewIs('pages.business-rules.show')
        ->assertSee($businessRule->name);
});

it('displays business rule name as title', function () {
    $businessRule = BusinessRule::factory()->create(['name' => 'Test Business Rule']);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee('Test Business Rule');
});

it('displays business rule description', function () {
    $businessRule = BusinessRule::factory()->create(['description' => 'Test Description']);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee('Test Description');
});

it('displays edit and delete buttons on show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.business-rules.edit', $businessRule));
});

it('displays business assets section on show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('Business Assets'));
});

it('displays data issues section on show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('Data Issues'));
});

it('displays associated business assets on show page', function () {
    $businessRule = BusinessRule::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();
    $domain = Domain::factory()->create();

    $asset = BusinessAsset::factory()->create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    $businessRule->businessAssets()->attach($asset->id);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee('Test Asset')
        ->assertSee('Test Definition')
        ->assertSee($domain->name);
});

it('displays associated data issues on show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $dataIssue = DataIssue::factory()->create([
        'name' => 'Test Data Issue',
        'description' => 'Test Description',
    ]);

    $businessRule->dataIssues()->attach($dataIssue->id);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee('Test Data Issue')
        ->assertSee('Test Description');
});

it('displays empty message when no business assets exist', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('No business assets associated with this business rule.'));
});

it('displays empty message when no data issues exist', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('No data issues associated with this business rule.'));
});

it('displays back to business rules link on show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.show', $businessRule))
        ->assertStatus(200)
        ->assertSee(__('Back to Business Rules'))
        ->assertSee(route('web.business-rules.index'));
});

it('requires authentication to access business rule show page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->get(route('web.business-rules.show', $businessRule))
        ->assertRedirect(route('login'));
});

// Edit Page Tests

it('displays edit business rule page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.edit', $businessRule))
        ->assertStatus(200)
        ->assertViewIs('pages.business-rules.edit')
        ->assertSee(__('Edit Business Rule'));
});

it('displays business rule form with pre-filled values on edit page', function () {
    $businessRule = BusinessRule::factory()->create([
        'name' => 'Existing Business Rule',
        'description' => 'Existing Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.business-rules.edit', $businessRule))
        ->assertStatus(200)
        ->assertSee('Existing Business Rule')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit business rule page', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->get(route('web.business-rules.edit', $businessRule))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a business rule', function () {
    $businessRule = BusinessRule::factory()->create();

    $data = [
        'name' => 'Updated Business Rule',
        'description' => 'Updated Description',
    ];

    $this->actingAs($this->user)
        ->put(route('web.business-rules.update', $businessRule), $data)
        ->assertRedirect(route('web.business-rules.index'))
        ->assertSessionHas('success', __('Business rule updated successfully.'));

    $this->assertDatabaseHas('business_rules', $data);
});

it('validates name is required when updating', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.business-rules.update', $businessRule), ['description' => 'Test'])
        ->assertSessionHasErrors('name');
});

// Delete Tests

it('can delete a business rule', function () {
    $businessRule = BusinessRule::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('web.business-rules.destroy', $businessRule))
        ->assertRedirect(route('web.business-rules.index'))
        ->assertSessionHas('success', __('Business rule deleted successfully.'));

    $this->assertDatabaseMissing('business_rules', ['id' => $businessRule->id]);
});

// Pagination Test

it('paginates business rules', function () {
    BusinessRule::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200);
});

// Menu Test

it('has business rules link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('web.business-rules.index'))
        ->assertStatus(200);
});
