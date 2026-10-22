<?php

use App\Models\BusinessAsset;
use App\Models\BusinessProcess;
use App\Models\DataIssue;
use App\Models\DataSource;
use App\Models\Role;
use App\Models\RootCause;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The feature must use Scout's database driver (SCOUT_DRIVER=database).
    config(['scout.driver' => 'database']);

    $this->user = User::factory()->create();
});

it('redirects guests to the login page', function () {
    $this->get(route('search', ['q' => 'customer']))
        ->assertRedirect(route('login'));
});

it('redirects unverified users to the email verification notice', function () {
    $unverifiedUser = User::factory()->unverified()->create();

    $this->actingAs($unverifiedUser)
        ->get(route('search', ['q' => 'customer']))
        ->assertRedirect(route('verification.notice'));
});

it('shows the search page to authenticated users', function () {
    $this->actingAs($this->user)
        ->get(route('search', ['q' => 'customer']))
        ->assertStatus(200)
        ->assertViewIs('pages.search.index');
});

it('finds a business asset matching the query', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Loyalty Program Metrics']);
    $nonMatchingAsset = BusinessAsset::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Loyalty Program Metrics']));

    $response->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee('Loyalty Program Metrics')
        ->assertSee(route('web.business-assets.show', $asset))
        ->assertDontSee($nonMatchingAsset->name);
});

it('finds a data issue matching the query', function () {
    $dataIssue = DataIssue::factory()->create(['name' => 'Duplicate Customer Records']);
    $nonMatchingIssue = DataIssue::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Duplicate Customer Records']));

    $response->assertStatus(200)
        ->assertSee(__('Data Issues'))
        ->assertSee('Duplicate Customer Records')
        ->assertSee(route('web.data-issues.show', $dataIssue))
        ->assertDontSee($nonMatchingIssue->name);
});

it('finds a root cause matching the query', function () {
    $rootCause = RootCause::factory()->create(['name' => 'Missing Validation Step']);
    $nonMatchingRootCause = RootCause::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Missing Validation Step']));

    $response->assertStatus(200)
        ->assertSee(__('Root Causes'))
        ->assertSee('Missing Validation Step')
        ->assertSee(route('web.root-causes.show', $rootCause))
        ->assertDontSee($nonMatchingRootCause->name);
});

it('finds a solution matching the query', function () {
    $solution = Solution::factory()->create(['name' => 'Automated Reconciliation']);
    $nonMatchingSolution = Solution::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Automated Reconciliation']));

    $response->assertStatus(200)
        ->assertSee(__('Solutions'))
        ->assertSee('Automated Reconciliation')
        ->assertSee(route('web.solutions.show', $solution))
        ->assertDontSee($nonMatchingSolution->name);
});

it('finds a data source matching the query', function () {
    $dataSource = DataSource::factory()->create(['name' => 'Customer CRM Export']);
    $nonMatchingSource = DataSource::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Customer CRM Export']));

    $response->assertStatus(200)
        ->assertSee(__('Data Sources'))
        ->assertSee('Customer CRM Export')
        ->assertSee(route('web.data-sources.show', $dataSource))
        ->assertDontSee($nonMatchingSource->name);
});

it('finds a business process matching the query', function () {
    $businessProcess = BusinessProcess::factory()->create(['name' => 'Order Fulfilment Flow']);
    $nonMatchingProcess = BusinessProcess::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Order Fulfilment Flow']));

    $response->assertStatus(200)
        ->assertSee(__('Business Processes'))
        ->assertSee('Order Fulfilment Flow')
        ->assertSee(route('web.business-processes.show', $businessProcess))
        ->assertDontSee($nonMatchingProcess->name);
});

it('finds a business asset by the name of its assigned data steward', function () {
    Role::factory()->dataSteward()->create();
    $steward = User::factory()->create(['name' => 'Marguerite Steward']);
    $asset = BusinessAsset::factory()->create(['name' => 'Contract Lifecycle Repository']);
    $unrelatedAsset = BusinessAsset::factory()->create();

    $asset->assignDataSteward($steward);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Marguerite Steward']));

    $response->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee('Contract Lifecycle Repository')
        ->assertSee(route('web.business-assets.show', $asset))
        ->assertDontSee($unrelatedAsset->name);
});

it('finds a business asset by the name of its assigned data owner', function () {
    Role::factory()->dataOwner()->create();
    $owner = User::factory()->create(['name' => 'Bartholomew Owner']);
    $asset = BusinessAsset::factory()->create(['name' => 'Vendor Spend Ledger']);
    $unrelatedAsset = BusinessAsset::factory()->create();

    $asset->assignDataOwner($owner);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Bartholomew Owner']));

    $response->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee('Vendor Spend Ledger')
        ->assertSee(route('web.business-assets.show', $asset))
        ->assertDontSee($unrelatedAsset->name);
});

it('matches queries case-insensitively', function () {
    BusinessAsset::factory()->create(['name' => 'CustomerLoyaltyIndex']);
    $nonMatchingAsset = BusinessAsset::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'customerloyaltyindex']));

    $response->assertStatus(200)
        ->assertSee('CustomerLoyaltyIndex')
        ->assertDontSee($nonMatchingAsset->name);
});

it('shows a no results message when nothing matches', function () {
    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'zzz-no-such-record-zzz']));

    $response->assertStatus(200)
        ->assertSee(__('No results'));
});

it('renders a prompt without results when the q parameter is empty', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Promptless Search Asset']);

    $withEmptyQuery = $this->actingAs($this->user)
        ->get(route('search', ['q' => '']));

    $withEmptyQuery->assertStatus(200)
        ->assertDontSee('Promptless Search Asset');

    $withoutQuery = $this->actingAs($this->user)
        ->get('/search');

    $withoutQuery->assertStatus(200)
        ->assertDontSee('Promptless Search Asset');
});

it('groups results by entity type', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Compliance Business Asset']);
    $dataIssue = DataIssue::factory()->create(['name' => 'Compliance Data Issue']);
    $rootCause = RootCause::factory()->create(['name' => 'Compliance Root Cause']);
    $solution = Solution::factory()->create(['name' => 'Compliance Solution']);
    $dataSource = DataSource::factory()->create(['name' => 'Compliance Data Source']);
    $businessProcess = BusinessProcess::factory()->create(['name' => 'Compliance Business Process']);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Compliance']));

    $response->assertStatus(200)
        ->assertSee(__('Business Assets'))
        ->assertSee(__('Data Issues'))
        ->assertSee(__('Root Causes'))
        ->assertSee(__('Solutions'))
        ->assertSee(__('Data Sources'))
        ->assertSee(__('Business Processes'))
        ->assertSee('Compliance Business Asset')
        ->assertSee('Compliance Data Issue')
        ->assertSee('Compliance Root Cause')
        ->assertSee('Compliance Solution')
        ->assertSee('Compliance Data Source')
        ->assertSee('Compliance Business Process');
});

it('shows at most five results per entity group', function () {
    $names = collect(range(1, 7))->map(fn (int $i) => "Satellite Telemetry Feed {$i}");

    $names->each(fn (string $name) => BusinessAsset::factory()->create(['name' => $name]));

    $content = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Satellite Telemetry Feed']))
        ->assertStatus(200)
        ->getContent();

    $missingNames = $names->filter(fn (string $name) => ! str_contains($content, $name));

    expect($missingNames)->toHaveCount(2);
});

it('ignores LIKE wildcards in the query', function () {
    $assets = BusinessAsset::factory()->count(3)->create();

    foreach (['%', '_'] as $wildcardQuery) {
        // A query made only of wildcards is sanitized down to an empty query,
        // which renders the empty-state prompt instead of any results.
        $content = $this->actingAs($this->user)
            ->get(route('search', ['q' => $wildcardQuery]))
            ->assertStatus(200)
            ->getContent();

        expect($content)->toContain(__('Start typing above to search across business assets, data issues, root causes, solutions, data sources and business processes.'));

        foreach ($assets as $asset) {
            expect($content)->not->toContain($asset->name);
        }
    }
});

it('does not duplicate a business asset matching by both name and steward', function () {
    Role::factory()->dataSteward()->create();
    $steward = User::factory()->create(['name' => 'Cordelia Contract Ledger']);
    $asset = BusinessAsset::factory()->create(['name' => 'Cordelia Contract Ledger']);

    $asset->assignDataSteward($steward);

    $content = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'Cordelia Contract Ledger']))
        ->assertStatus(200)
        ->assertSee('Cordelia Contract Ledger')
        ->getContent();

    expect(substr_count($content, route('web.business-assets.show', $asset)))->toBe(1);
});
