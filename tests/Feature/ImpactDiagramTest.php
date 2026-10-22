<?php

use App\Models\BusinessAsset;
use App\Models\BusinessObjective;
use App\Models\BusinessProcess;
use App\Models\BusinessRule;
use App\Models\DataIssue;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
use App\Models\Domain;
use App\Models\RootCause;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Js;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

/**
 * Build the URL of the impact diagram page for a given entity.
 */
function impactDiagramUrl(string $entityType, int $entityId): string
{
    return route('web.impact-diagram.show', ['entityType' => $entityType, 'entity' => $entityId]);
}

/**
 * Decode the graph payload passed to the view.
 */
function impactDiagramPayload($response): array
{
    $graph = $response->viewData('graph');

    if (is_array($graph)) {
        return $graph;
    }

    $js = (string) $graph;
    $prefix = "JSON.parse('";

    expect($js)->toStartWith($prefix)->toEndWith("')");

    $encoded = substr($js, strlen($prefix), -2);

    // Js::from double-encodes the payload: the content between the single
    // quotes is a JSON string literal with hex-escaped quotes (\u0022), whose
    // surrounding double quotes have been stripped. Restoring them decodes
    // the inner JSON string, then a second decode yields the array.
    $inner = json_decode('"'.$encoded.'"', true);

    return is_string($inner) ? json_decode($inner, true) : $inner;
}

/**
 * Find a node in the graph payload by its id ("asset:1", "rule:2", ...).
 */
function impactDiagramNode(array $payload, string $id): ?array
{
    foreach ($payload['nodes'] as $node) {
        if ($node['id'] === $id) {
            return $node;
        }
    }

    return null;
}

/**
 * Find an edge between two nodes (in either direction) with a given relation.
 */
function impactDiagramEdge(array $payload, string $a, string $b, string $rel): ?array
{
    foreach ($payload['edges'] as $edge) {
        if ($edge['rel'] !== $rel) {
            continue;
        }

        if (($edge['from'] === $a && $edge['to'] === $b) || ($edge['from'] === $b && $edge['to'] === $a)) {
            return $edge;
        }
    }

    return null;
}

/**
 * Build a rich diagram scenario around a root business asset:
 * strategy (domain, initiative, objective), execution (rule, dq check,
 * sources, process) and quality chain (issue, root cause, solution).
 */
function impactDiagramScenario(): array
{
    $asset = BusinessAsset::factory()->create(['name' => 'Customer Master Record']);
    $rule = BusinessRule::factory()->create(['name' => 'Customer Email Validity Rule']);
    $source = DataSource::factory()->create(['name' => 'CRM Customer Table']);
    $secondSource = DataSource::factory()->create(['name' => 'Marketing Warehouse']);
    $check = DataQualityCheck::factory()->create([
        'name' => 'Email Format Check',
        'business_rule_id' => $rule->id,
    ]);
    $issue = DataIssue::factory()->create(['name' => 'Duplicate Customer Records']);
    $rootCause = RootCause::factory()->create(['name' => 'Missing Validation Step']);
    $solution = Solution::factory()->create(['name' => 'Automated Reconciliation']);
    $process = BusinessProcess::factory()->create(['name' => 'Order Fulfilment Flow']);
    $objective = BusinessObjective::factory()->create(['name' => 'Improve Customer Trust']);

    $asset->businessRules()->attach($rule->id);
    $asset->dataSources()->attach($source->id);
    $check->dataSources()->attach($secondSource->id);
    $asset->dataIssues()->attach($issue->id);
    $issue->businessRules()->attach($rule->id);
    $issue->rootCauses()->attach($rootCause->id);
    $rootCause->solutions()->attach($solution->id);
    $asset->businessProcesses()->attach($process->id);
    $asset->dataInitiative->businessObjectives()->attach($objective->id);

    return [
        'asset' => $asset,
        'rule' => $rule,
        'source' => $source,
        'secondSource' => $secondSource,
        'check' => $check,
        'issue' => $issue,
        'rootCause' => $rootCause,
        'solution' => $solution,
        'process' => $process,
        'objective' => $objective,
        'domain' => $asset->domain,
        'initiative' => $asset->dataInitiative,
    ];
}

// Authentication

it('redirects guests to the login page', function () {
    $this->get(impactDiagramUrl('business-asset', 1))
        ->assertRedirect(route('login'));
});

it('redirects unverified users to the email verification notice', function () {
    $unverifiedUser = User::factory()->unverified()->create();
    $asset = BusinessAsset::factory()->create();

    $this->actingAs($unverifiedUser)
        ->get(impactDiagramUrl('business-asset', $asset->id))
        ->assertRedirect(route('verification.notice'));
});

// Not-found handling

it('returns 404 for an unknown entity type', function () {
    $asset = BusinessAsset::factory()->create();

    $this->actingAs($this->user)
        ->get(impactDiagramUrl('unknown-type', $asset->id))
        ->assertNotFound();
});

it('returns 404 for a non-existent entity id', function () {
    $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', 999999))
        ->assertNotFound();
});

it('returns 404 when the entity id belongs to a different entity type', function () {
    $asset = BusinessAsset::factory()->create();
    expect(BusinessRule::whereKey($asset->id)->exists())->toBeFalse();

    $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-rule', $asset->id))
        ->assertNotFound();
});

// Every business entity type can be the entry point

it('accepts every supported entity type as the diagram entry point', function () {
    $scenario = impactDiagramScenario();

    $entryPoints = [
        'business-asset' => ['asset', $scenario['asset']->id],
        'business-rule' => ['rule', $scenario['rule']->id],
        'data-source' => ['source', $scenario['source']->id],
        'data-quality-check' => ['dq', $scenario['check']->id],
        'data-initiative' => ['initiative', $scenario['initiative']->id],
        'business-objective' => ['objective', $scenario['objective']->id],
        'domain' => ['domain', $scenario['domain']->id],
        'data-issue' => ['issue', $scenario['issue']->id],
        'root-cause' => ['rootcause', $scenario['rootCause']->id],
        'solution' => ['solution', $scenario['solution']->id],
        'business-process' => ['process', $scenario['process']->id],
    ];

    foreach ($entryPoints as $slug => [$typeKey, $id]) {
        $response = $this->actingAs($this->user)
            ->get(impactDiagramUrl($slug, $id));

        $response->assertStatus(200)
            ->assertViewIs('pages.impact-diagram.index');

        $payload = impactDiagramPayload($response);

        expect($payload['rootId'])->toBe("{$typeKey}:{$id}")
            ->and(impactDiagramNode($payload, "{$typeKey}:{$id}"))->not->toBeNull();
    }
});

// Page rendering

it('shows the impact diagram page to authenticated users', function () {
    $asset = BusinessAsset::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertViewIs('pages.impact-diagram.index')
        ->assertViewHas('graph');

    expect($response->viewData('graph'))->toBeInstanceOf(Js::class);
});

// Graph content

it('includes first-degree entities with their BFS depth', function () {
    ['asset' => $asset, 'rule' => $rule, 'source' => $source, 'check' => $check, 'secondSource' => $secondSource] = impactDiagramScenario();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertSee('Customer Master Record')
        ->assertSee('Customer Email Validity Rule')
        ->assertSee('CRM Customer Table');

    $payload = impactDiagramPayload($response);

    expect($payload['rootId'])->toBe("asset:{$asset->id}");

    $assetNode = impactDiagramNode($payload, "asset:{$asset->id}");
    expect($assetNode)->not->toBeNull()
        ->and($assetNode['type'])->toBe('asset')
        ->and($assetNode['label'])->toBe('Customer Master Record')
        ->and($assetNode['depth'])->toBe(0);

    expect(impactDiagramNode($payload, "rule:{$rule->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "source:{$source->id}"))->not->toBeNull();

    // First-degree nodes carry depth 1.
    expect(impactDiagramNode($payload, "rule:{$rule->id}")['depth'])->toBe(1)
        ->and(impactDiagramNode($payload, "source:{$source->id}")['depth'])->toBe(1);

    // Second- and third-degree nodes carry deeper BFS depths.
    expect(impactDiagramNode($payload, "dq:{$check->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "dq:{$check->id}")['depth'])->toBe(2)
        ->and(impactDiagramNode($payload, "source:{$secondSource->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "source:{$secondSource->id}")['depth'])->toBe(3);

    // Edges carry the relation type and business label, but no hidden flag:
    // progressive display is a frontend concern.
    $conformEdge = impactDiagramEdge($payload, "asset:{$asset->id}", "rule:{$rule->id}", 'conform');
    expect($conformEdge)->not->toBeNull()
        ->and($conformEdge['label'])->toBe('must conform to')
        ->and($conformEdge)->not->toHaveKey('hidden');

    $containedEdge = impactDiagramEdge($payload, "asset:{$asset->id}", "source:{$source->id}", 'contained');
    expect($containedEdge)->not->toBeNull()
        ->and($containedEdge['label'])->toBe('is contained');

    $checkedEdge = impactDiagramEdge($payload, "rule:{$rule->id}", "dq:{$check->id}", 'checked');
    expect($checkedEdge)->not->toBeNull()
        ->and($checkedEdge['label'])->toBe('is checked by');

    $appliesEdge = impactDiagramEdge($payload, "dq:{$check->id}", "source:{$secondSource->id}", 'applies');
    expect($appliesEdge)->not->toBeNull()
        ->and($appliesEdge['label'])->toBe('applies to');
});

it('covers the strategy relationships: domain, initiative and objective', function () {
    ['asset' => $asset, 'domain' => $domain, 'initiative' => $initiative, 'objective' => $objective] = impactDiagramScenario();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $payload = impactDiagramPayload($response);

    $domainNode = impactDiagramNode($payload, "domain:{$domain->id}");
    $initiativeNode = impactDiagramNode($payload, "initiative:{$initiative->id}");
    $objectiveNode = impactDiagramNode($payload, "objective:{$objective->id}");

    expect($domainNode)->not->toBeNull()
        ->and($initiativeNode)->not->toBeNull()
        ->and($objectiveNode)->not->toBeNull()
        // Data initiatives are labelled by their label column.
        ->and($initiativeNode['label'])->toBe($initiative->label)
        ->and($initiativeNode['depth'])->toBe(1)
        ->and($objectiveNode['depth'])->toBe(2);

    expect(impactDiagramEdge($payload, "asset:{$asset->id}", "domain:{$domain->id}", 'in-domain'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "asset:{$asset->id}", "initiative:{$initiative->id}", 'in-initiative'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "initiative:{$initiative->id}", "objective:{$objective->id}", 'contributes-to'))->not->toBeNull();
});

it('covers the execution and quality chains: process, issue, root cause and solution', function () {
    [
        'asset' => $asset,
        'process' => $process,
        'issue' => $issue,
        'rootCause' => $rootCause,
        'solution' => $solution,
        'rule' => $rule,
    ] = impactDiagramScenario();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $payload = impactDiagramPayload($response);

    expect(impactDiagramNode($payload, "process:{$process->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "issue:{$issue->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "rootcause:{$rootCause->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "solution:{$solution->id}"))->not->toBeNull();

    expect(impactDiagramEdge($payload, "process:{$process->id}", "asset:{$asset->id}", 'used-by'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "issue:{$issue->id}", "asset:{$asset->id}", 'affects'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "issue:{$issue->id}", "rule:{$rule->id}", 'violates'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "issue:{$issue->id}", "rootcause:{$rootCause->id}", 'caused-by'))->not->toBeNull();
    expect(impactDiagramEdge($payload, "rootcause:{$rootCause->id}", "solution:{$solution->id}", 'solved-by'))->not->toBeNull();
});

it('extends the connected component transitively through shared sources', function () {
    ['asset' => $asset, 'source' => $source] = impactDiagramScenario();

    $otherAsset = BusinessAsset::factory()->create(['name' => 'Loyalty Program Metrics']);
    $otherAsset->dataSources()->attach($source->id);

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertSee('Loyalty Program Metrics');

    $payload = impactDiagramPayload($response);

    expect(impactDiagramNode($payload, "asset:{$otherAsset->id}"))->not->toBeNull()
        ->and(impactDiagramEdge($payload, "asset:{$otherAsset->id}", "source:{$source->id}", 'contained'))->not->toBeNull();
});

it('builds the diagram from a business rule root', function () {
    ['asset' => $asset, 'rule' => $rule] = impactDiagramScenario();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-rule', $rule->id));

    $response->assertStatus(200)
        ->assertViewIs('pages.impact-diagram.index')
        ->assertSee('Customer Email Validity Rule');

    $payload = impactDiagramPayload($response);

    expect($payload['rootId'])->toBe("rule:{$rule->id}")
        ->and(impactDiagramNode($payload, "asset:{$asset->id}"))->not->toBeNull()
        ->and(impactDiagramNode($payload, "asset:{$asset->id}")['depth'])->toBe(1);
});

it('renders a single-node graph for an isolated entity', function () {
    // A business rule without data quality checks or data issues is fully
    // isolated (a business asset always belongs to an initiative).
    $rule = BusinessRule::factory()->create(['name' => 'Standalone Rule']);

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-rule', $rule->id));

    $response->assertStatus(200)
        ->assertSee('Standalone Rule');

    $payload = impactDiagramPayload($response);

    expect($payload['rootId'])->toBe("rule:{$rule->id}")
        ->and($payload['nodes'])->toHaveCount(1)
        ->and($payload['nodes'][0]['id'])->toBe("rule:{$rule->id}")
        ->and($payload['edges'])->toBeEmpty();
});

it('includes the show url and side panel attributes in the asset node payload', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Customer Master Record']);

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $payload = impactDiagramPayload($response);

    $assetNode = impactDiagramNode($payload, "asset:{$asset->id}");

    expect($assetNode)->not->toBeNull()
        ->and($assetNode['url'])->toBe(route('web.business-assets.show', $asset))
        ->and($assetNode['attributes'])->toHaveKey('Definition')
        ->and($assetNode['attributes']['Definition'])->toBe($asset->definition)
        ->and($assetNode['attributes'])->toHaveKey('Domain')
        ->and($assetNode['attributes']['Domain'])->toBe($asset->domain->name)
        ->and($assetNode['attributes'])->toHaveKey('Data Initiative')
        ->and($assetNode['attributes']['Data Initiative'])->toBe($asset->dataInitiative->label);
});
