<?php

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a domain', function () {
    $domain = Domain::create([
        'name' => 'Test Domain',
        'description' => 'Test Description',
    ]);

    expect($domain->name)->toBe('Test Domain');
    expect($domain->description)->toBe('Test Description');

    $this->assertDatabaseHas('domains', [
        'name' => 'Test Domain',
        'description' => 'Test Description',
    ]);
});

it('domain factory works', function () {
    $domain = Domain::factory()->create();

    expect($domain->name)->not()->toBeEmpty();
    expect($domain->description)->not()->toBeEmpty();
});

it('domain name is required', function () {
    $domain = new Domain([
        'description' => 'Test Description',
    ]);

    expect(fn () => $domain->save())->toThrow(QueryException::class);
});

it('domain has many business assets', function () {
    $domain = Domain::factory()->create();

    BusinessAsset::factory()->count(3)->create([
        'domain_id' => $domain->id,
    ]);

    expect($domain->businessAssets()->count())->toBe(3);
});

it('business asset belongs to domain', function () {
    $domain = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::create([
        'name' => 'Test Asset',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain->id,
    ]);

    expect($asset->domain->id)->toBe($domain->id);
    expect($asset->domain->name)->toBe($domain->name);
});

it('business asset domain can be null', function () {
    $dataInitiative = DataInitiative::factory()->create();

    $asset = BusinessAsset::create([
        'name' => 'Asset Without Domain',
        'definition' => 'Test Definition',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => null,
    ]);

    expect($asset->domain)->toBeNull();
});

it('can query business assets by domain', function () {
    $domain1 = Domain::factory()->create();
    $domain2 = Domain::factory()->create();
    $dataInitiative = DataInitiative::factory()->create();

    BusinessAsset::factory()->create([
        'name' => 'Asset 1',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain1->id,
    ]);

    BusinessAsset::factory()->create([
        'name' => 'Asset 2',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain1->id,
    ]);

    BusinessAsset::factory()->create([
        'name' => 'Asset 3',
        'data_initiative_id' => $dataInitiative->id,
        'domain_id' => $domain2->id,
    ]);

    expect($domain1->businessAssets()->count())->toBe(2);
    expect($domain2->businessAssets()->count())->toBe(1);
});
