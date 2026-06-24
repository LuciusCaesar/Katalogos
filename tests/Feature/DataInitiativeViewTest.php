<?php

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

// Index Page Tests

it('displays data initiatives index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.index')
        ->assertSee(__('Data Initiatives'));
});

it('displays empty state when no data initiatives exist', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee(__('No data initiatives found. Create one to get started.'));
});

it('displays data initiatives in a table', function () {
    DataInitiative::factory()->create([
        'code' => 'TEST-001',
        'label' => 'Test Initiative',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('TEST-001')
        ->assertSee('Test Initiative')
        ->assertSee('Test Description')
        ->assertSee(__('Code'))
        ->assertSee(__('Label'))
        ->assertSee(__('Description'))
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Data Steward'))
        ->assertSee(__('Data Owner'))
        ->assertSee(__('Avg Governance Score'))
        ->assertSee(__('Avg Data Quality Score'));
});

it('displays business assets count in table', function () {
    $initiative = DataInitiative::factory()->create();
    BusinessAsset::factory()->count(3)->create(['data_initiative_id' => $initiative->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('3');
});

it('displays new button on index page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee(__('New'))
        ->assertSee(route('web.data-initiatives.create'));
});

it('requires authentication to access data initiatives index', function () {
    $this->get(route('web.data-initiatives.index'))
        ->assertRedirect(route('login'));
});

// Create Page Tests

it('displays create data initiative page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.create'))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.create')
        ->assertSee(__('Create Data Initiative'));
});

it('displays data initiative form on create page', function () {
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.create'))
        ->assertStatus(200)
        ->assertSee(__('Code'))
        ->assertSee(__('Label'))
        ->assertSee(__('Description'))
        ->assertSee(__('Create Data Initiative'));
});

it('requires authentication to access create data initiative page', function () {
    $this->get(route('web.data-initiatives.create'))
        ->assertRedirect(route('login'));
});

// Show Page Tests

it('displays data initiative show page', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'SHOW-TEST',
        'label' => 'Show Test Initiative',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.show')
        ->assertSee('Show Test Initiative')
        ->assertSee('SHOW-TEST');
});

it('displays data initiative label as title', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'TITLE-TEST',
        'label' => 'Title Test Initiative',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSeeInOrder(['Title Test Initiative', 'TITLE-TEST', 'Test Description']);
});

it('displays data initiative description', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'DESC-TEST',
        'label' => 'Description Test Initiative',
        'description' => 'Unique Description Text',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee('Unique Description Text');
});

it('displays business assets count in right column card', function () {
    $initiative = DataInitiative::factory()->create();
    BusinessAsset::factory()->count(5)->create(['data_initiative_id' => $initiative->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee('5');
});

it('displays data steward in right column card', function () {
    $steward = User::factory()->create(['name' => 'Alice Steward']);
    $initiative = DataInitiative::factory()->create();

    $stewardRole = Role::factory()->dataSteward()->create();
    $initiative->assignRoleToUser($steward, $stewardRole);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Assigned Roles'))
        ->assertSee(__('Data Steward'))
        ->assertSee('Alice Steward');
});

it('displays data owner in right column card', function () {
    $owner = User::factory()->create(['name' => 'Bob Owner']);
    $initiative = DataInitiative::factory()->create();

    $ownerRole = Role::factory()->dataOwner()->create();
    $initiative->assignRoleToUser($owner, $ownerRole);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Data Owner'))
        ->assertSee('Bob Owner');
});

it('displays dash when data steward not assigned', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Data Steward'))
        ->assertSee('-');
});

it('displays dash when data owner not assigned', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Data Owner'))
        ->assertSee('-');
});

it('displays edit and delete buttons on show page', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'EDIT-TEST',
        'label' => 'Edit Test Initiative',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Edit'))
        ->assertSee(__('Delete'))
        ->assertSee(route('web.data-initiatives.edit', $initiative));
});

it('displays back to data initiatives link on show page', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Back to Data Initiatives'))
        ->assertSee(route('web.data-initiatives.index'));
});

it('requires authentication to access data initiative show page', function () {
    $initiative = DataInitiative::factory()->create();

    $this->get(route('web.data-initiatives.show', $initiative))
        ->assertRedirect(route('login'));
});

it('does not show user as data owner when only assigned as data steward on different initiative', function () {
    $steward = User::factory()->create(['name' => 'Cross Initiative Steward']);

    $initiative1 = DataInitiative::factory()->create([
        'code' => 'INIT-001',
        'label' => 'Initiative One',
    ]);

    $initiative2 = DataInitiative::factory()->create([
        'code' => 'INIT-002',
        'label' => 'Initiative Two',
    ]);

    $stewardRole = Role::factory()->dataSteward()->create();
    $ownerRole = Role::factory()->dataOwner()->create();

    // Assign steward as Data Steward on Initiative 1
    $initiative1->assignRoleToUser($steward, $stewardRole);

    // Assign same user as Data Owner on Initiative 2
    $initiative2->assignRoleToUser($steward, $ownerRole);

    // Verify show page for Initiative 1 only shows steward, not owner
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative1))
        ->assertStatus(200)
        ->assertSee('Cross Initiative Steward')
        ->assertSeeInOrder([__('Data Steward'), 'Cross Initiative Steward'])
        ->assertSeeInOrder([__('Data Owner'), '-']);

    // Verify show page for Initiative 2 only shows owner, not steward
    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.show', $initiative2))
        ->assertStatus(200)
        ->assertSee('Cross Initiative Steward')
        ->assertSeeInOrder([__('Data Owner'), 'Cross Initiative Steward'])
        ->assertSeeInOrder([__('Data Steward'), '-']);
});

// Store Tests

it('can store a new data initiative', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-initiatives.store'), [
            'code' => 'NEW-001',
            'label' => 'New Data Initiative',
            'description' => 'New Description',
        ])
        ->assertRedirect(route('web.data-initiatives.index'))
        ->assertSessionHas('success', __('Data Initiative created successfully.'));

    $this->assertDatabaseHas('data_initiatives', [
        'code' => 'NEW-001',
        'label' => 'New Data Initiative',
        'description' => 'New Description',
    ]);
});

it('validates code is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-initiatives.store'), [
            'label' => 'Test Initiative',
            'description' => 'Test Description',
        ])
        ->assertSessionHasErrors('code');
});

it('validates label is required when storing', function () {
    $this->actingAs($this->user)
        ->post(route('web.data-initiatives.store'), [
            'code' => 'TEST-001',
            'description' => 'Test Description',
        ])
        ->assertSessionHasErrors('label');
});

it('validates code is unique when storing', function () {
    DataInitiative::factory()->create(['code' => 'DUPLICATE']);

    $this->actingAs($this->user)
        ->post(route('web.data-initiatives.store'), [
            'code' => 'DUPLICATE',
            'label' => 'Duplicate Initiative',
        ])
        ->assertSessionHasErrors('code');
});

// Edit Page Tests

it('displays edit data initiative page', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'EDIT-TEST',
        'label' => 'Edit Test Initiative',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.edit', $initiative))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.edit')
        ->assertSee(__('Edit Data Initiative'))
        ->assertSee('Edit Test Initiative');
});

it('displays data initiative form with pre-filled values on edit page', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'EXISTING',
        'label' => 'Existing Initiative',
        'description' => 'Existing Description',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.edit', $initiative))
        ->assertStatus(200)
        ->assertSee(__('Code'))
        ->assertSee(__('Label'))
        ->assertSee(__('Description'))
        ->assertSee(__('Update Data Initiative'))
        ->assertSee('EXISTING')
        ->assertSee('Existing Initiative')
        ->assertSee('Existing Description');
});

it('requires authentication to access edit data initiative page', function () {
    $initiative = DataInitiative::factory()->create();

    $this->get(route('web.data-initiatives.edit', $initiative))
        ->assertRedirect(route('login'));
});

// Update Tests

it('can update a data initiative', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'ORIGINAL',
        'label' => 'Original Label',
        'description' => 'Original Description',
    ]);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.update', $initiative), [
            'code' => 'UPDATED',
            'label' => 'Updated Label',
            'description' => 'Updated Description',
        ])
        ->assertRedirect(route('web.data-initiatives.index'))
        ->assertSessionHas('success', __('Data Initiative updated successfully.'));

    $this->assertDatabaseHas('data_initiatives', [
        'id' => $initiative->id,
        'code' => 'UPDATED',
        'label' => 'Updated Label',
        'description' => 'Updated Description',
    ]);
});

it('validates code is required when updating', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.update', $initiative), [
            'label' => 'Updated Label',
            'description' => 'Updated Description',
        ])
        ->assertSessionHasErrors('code');
});

it('validates label is required when updating', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.update', $initiative), [
            'code' => 'UPDATED',
            'description' => 'Updated Description',
        ])
        ->assertSessionHasErrors('label');
});

it('validates code is unique when updating', function () {
    $initiative1 = DataInitiative::factory()->create(['code' => 'ORIGINAL']);
    DataInitiative::factory()->create(['code' => 'OTHER']);

    $this->actingAs($this->user)
        ->put(route('web.data-initiatives.update', $initiative1), [
            'code' => 'OTHER',
            'label' => 'Updated Label',
        ])
        ->assertSessionHasErrors('code');
});

// Destroy Tests

it('can delete a data initiative', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'DELETE-ME',
        'label' => 'Initiative to Delete',
        'description' => 'Test Description',
    ]);

    $this->actingAs($this->user)
        ->delete(route('web.data-initiatives.destroy', $initiative))
        ->assertRedirect(route('web.data-initiatives.index'))
        ->assertSessionHas('success', __('Data Initiative deleted successfully.'));

    $this->assertDatabaseMissing('data_initiatives', ['id' => $initiative->id]);
});

// Pagination Tests

it('paginates data initiatives', function () {
    DataInitiative::factory()->count(15)->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('Next');
});

// Governance Score History Tests

it('displays governance score history page', function () {
    $initiative = DataInitiative::factory()->create();
    DataInitiativeGovernanceScoreHistory::factory()->create([
        'data_initiative_id' => $initiative->id,
        'score' => 0.85,
        'event' => 'Test Event',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.governance-score.history', $initiative))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.governance-score-history')
        ->assertSee(__('Governance Score History'))
        ->assertSee('85.0%')
        ->assertSee('Test Event');
});

it('displays empty state for governance score history', function () {
    $initiative = DataInitiative::factory()->create();

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.governance-score.history', $initiative))
        ->assertStatus(200)
        ->assertSee(__('No governance score history available for this data initiative.'));
});

it('displays specific governance score show page', function () {
    $initiative = DataInitiative::factory()->create();
    $history = DataInitiativeGovernanceScoreHistory::factory()->create([
        'data_initiative_id' => $initiative->id,
        'score' => 0.90,
        'event' => 'Specific Event',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.governance-score.show', [$initiative, $history]))
        ->assertStatus(200)
        ->assertViewIs('pages.data-initiatives.governance-score-show')
        ->assertSee(__('Governance Score Details'))
        ->assertSee('90.0%')
        ->assertSee('Specific Event');
});

// Menu Link Test

it('has data initiatives link in sidebar menu', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee(__('Data Initiatives'))
        ->assertSee(route('web.data-initiatives.index'));
});

// Average Data Quality Score Tests

it('displays dash for average data quality score when no business assets exist', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'NO-ASSETS',
        'label' => 'No Assets Initiative',
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSeeInOrder(['NO-ASSETS', '-']);
});

it('displays dash for average data quality score when business assets have no data quality checks', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'NO-CHECKS',
        'label' => 'No Checks Initiative',
    ]);

    BusinessAsset::factory()->count(2)->create(['data_initiative_id' => $initiative->id]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSeeInOrder(['NO-CHECKS', '-']);
});

it('displays average data quality score when business assets have data quality checks', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'WITH-SCORES',
        'label' => 'With Scores Initiative',
    ]);

    // Create first business asset with score 0.8
    $asset1 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule1 = BusinessRule::factory()->create();
    $asset1->businessRules()->attach($rule1->id);
    $check1 = DataQualityCheck::factory()->create(['business_rule_id' => $rule1->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $check1->id,
        'score' => 0.8,
        'rows_passed' => 80,
        'rows_failed' => 20,
        'total_rows' => 100,
    ]);

    // Create second business asset with score 0.9
    $asset2 = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule2 = BusinessRule::factory()->create();
    $asset2->businessRules()->attach($rule2->id);
    $check2 = DataQualityCheck::factory()->create(['business_rule_id' => $rule2->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $check2->id,
        'score' => 0.9,
        'rows_passed' => 90,
        'rows_failed' => 10,
        'total_rows' => 100,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('85.0%'); // Average of 0.8 and 0.9 is 0.85
});

it('displays average data quality score with correct color for high scores', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'HIGH-SCORE',
        'label' => 'High Score Initiative',
    ]);

    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $check = DataQualityCheck::factory()->create(['business_rule_id' => $rule->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $check->id,
        'score' => 0.95,
        'rows_passed' => 95,
        'rows_failed' => 5,
        'total_rows' => 100,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('95.0%')
        ->assertSee('text-green');
});

it('displays average data quality score with correct color for medium scores', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'MEDIUM-SCORE',
        'label' => 'Medium Score Initiative',
    ]);

    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $check = DataQualityCheck::factory()->create(['business_rule_id' => $rule->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $check->id,
        'score' => 0.75,
        'rows_passed' => 75,
        'rows_failed' => 25,
        'total_rows' => 100,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('75.0%')
        ->assertSee('text-yellow');
});

it('displays average data quality score with correct color for low scores', function () {
    $initiative = DataInitiative::factory()->create([
        'code' => 'LOW-SCORE',
        'label' => 'Low Score Initiative',
    ]);

    $asset = BusinessAsset::factory()->create(['data_initiative_id' => $initiative->id]);
    $rule = BusinessRule::factory()->create();
    $asset->businessRules()->attach($rule->id);
    $check = DataQualityCheck::factory()->create(['business_rule_id' => $rule->id]);
    DataQualityCheckScore::factory()->create([
        'data_quality_check_id' => $check->id,
        'score' => 0.60,
        'rows_passed' => 60,
        'rows_failed' => 40,
        'total_rows' => 100,
    ]);

    $this->actingAs($this->user)
        ->get(route('web.data-initiatives.index'))
        ->assertStatus(200)
        ->assertSee('60.0%')
        ->assertSee('text-red');
});
