<?php

use App\Models\BusinessAsset;
use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataSource;
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
 *
 * The controller passes an Illuminate\Support\Js::from(...) instance, which
 * renders as `JSON.parse('...')`. The inner string is JSON-encoded twice
 * (the Js class hex-escapes quotes on the first pass), so we decode twice
 * to get the payload array back.
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
 * Build a diagram scenario: an asset conforming to a rule, contained in a
 * source, the rule being checked by a dq check, and the check applying to
 * the source.
 */
function impactDiagramScenario(): array
{
    $asset = BusinessAsset::factory()->create(['name' => 'Customer Master Record']);
    $rule = BusinessRule::factory()->create(['name' => 'Customer Email Validity Rule']);
    $source = DataSource::factory()->create(['name' => 'CRM Customer Table']);
    $check = DataQualityCheck::factory()->create([
        'name' => 'Email Format Check',
        'business_rule_id' => $rule->id,
    ]);

    $asset->businessRules()->attach($rule->id);
    $asset->dataSources()->attach($source->id);
    $check->dataSources()->attach($source->id);

    return compact('asset', 'rule', 'source', 'check');
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
    // The asset id exists, but no business rule carries that id.
    $asset = BusinessAsset::factory()->create();
    expect(BusinessRule::whereKey($asset->id)->exists())->toBeFalse();

    $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-rule', $asset->id))
        ->assertNotFound();
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

it('includes the asset, its business rules and its data sources in the diagram', function () {
    ['asset' => $asset, 'rule' => $rule, 'source' => $source] = impactDiagramScenario();

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
        ->and($assetNode['label'])->toBe('Customer Master Record');

    $ruleNode = impactDiagramNode($payload, "rule:{$rule->id}");
    expect($ruleNode)->not->toBeNull()
        ->and($ruleNode['type'])->toBe('rule');

    $sourceNode = impactDiagramNode($payload, "source:{$source->id}");
    expect($sourceNode)->not->toBeNull()
        ->and($sourceNode['type'])->toBe('source');

    $conformEdge = impactDiagramEdge($payload, "asset:{$asset->id}", "rule:{$rule->id}", 'conform');
    expect($conformEdge)->not->toBeNull()
        ->and($conformEdge['label'])->toBe('must conform to')
        ->and($conformEdge['hidden'])->toBeFalse();

    $containedEdge = impactDiagramEdge($payload, "asset:{$asset->id}", "source:{$source->id}", 'contained');
    expect($containedEdge)->not->toBeNull()
        ->and($containedEdge['label'])->toBe('is contained')
        ->and($containedEdge['hidden'])->toBeFalse();
});

it('includes data quality checks of the related rules with hidden edges', function () {
    ['asset' => $asset, 'rule' => $rule, 'source' => $source, 'check' => $check] = impactDiagramScenario();

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertSee('Email Format Check');

    $payload = impactDiagramPayload($response);

    $checkNode = impactDiagramNode($payload, "dq:{$check->id}");
    expect($checkNode)->not->toBeNull()
        ->and($checkNode['type'])->toBe('dq');

    $checkedEdge = impactDiagramEdge($payload, "rule:{$rule->id}", "dq:{$check->id}", 'checked');
    expect($checkedEdge)->not->toBeNull()
        ->and($checkedEdge['label'])->toBe('is checked by')
        ->and($checkedEdge['hidden'])->toBeTrue();

    $appliesEdge = impactDiagramEdge($payload, "dq:{$check->id}", "source:{$source->id}", 'applies');
    expect($appliesEdge)->not->toBeNull()
        ->and($appliesEdge['label'])->toBe('applies to')
        ->and($appliesEdge['hidden'])->toBeTrue();
});

it('extends the connected component transitively through shared sources', function () {
    ['asset' => $asset, 'rule' => $rule, 'source' => $source, 'check' => $check] = impactDiagramScenario();

    // Another asset uses the same source, but has no direct relation to the
    // root asset: it is only reachable via rule -> dq check -> source.
    $otherAsset = BusinessAsset::factory()->create(['name' => 'Loyalty Program Metrics']);
    $otherAsset->dataSources()->attach($source->id);

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertSee('Loyalty Program Metrics');

    $payload = impactDiagramPayload($response);

    expect(impactDiagramNode($payload, "asset:{$otherAsset->id}"))->not->toBeNull();

    // The transitive chain rule -> dq check -> source is present as edges.
    expect(impactDiagramEdge($payload, "rule:{$rule->id}", "dq:{$check->id}", 'checked'))->not->toBeNull()
        ->and(impactDiagramEdge($payload, "dq:{$check->id}", "source:{$source->id}", 'applies'))->not->toBeNull()
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
        ->and(impactDiagramNode($payload, "asset:{$asset->id}"))->not->toBeNull();
});

it('renders a single-node graph for an isolated entity', function () {
    $asset = BusinessAsset::factory()->create(['name' => 'Standalone Asset']);

    $response = $this->actingAs($this->user)
        ->get(impactDiagramUrl('business-asset', $asset->id));

    $response->assertStatus(200)
        ->assertSee('Standalone Asset');

    $payload = impactDiagramPayload($response);

    expect($payload['rootId'])->toBe("asset:{$asset->id}")
        ->and($payload['nodes'])->toHaveCount(1)
        ->and($payload['nodes'][0]['id'])->toBe("asset:{$asset->id}")
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
