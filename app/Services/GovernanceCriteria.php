<?php

namespace App\Services;

use App\Models\BusinessAsset;

/**
 * Code-defined governance scoring criteria for business assets.
 *
 * The criteria definition lives in code (not in the database) so that the
 * scoring logic is versioned with the application. Each score row snapshots
 * the definition version it was computed with (GovernanceScore
 * .definition_version), which allows the history to be reinterpreted or
 * recomputed if the definition evolves.
 */
final class GovernanceCriteria
{
    /**
     * Version of the criteria definition. Bump when criteria, weights or
     * evaluation logic change; historical score rows keep their own snapshot.
     */
    public const DEFINITION_VERSION = 1;

    /**
     * All governance criteria, in evaluation order.
     *
     * @return array<int, array{key: string, name: string, description: string, weight: float, category: string, evaluate: callable(BusinessAsset): bool}>
     */
    public static function all(): array
    {
        return [
            [
                'key' => 'has_name',
                'name' => 'Has Name',
                'description' => 'Business Asset has a name',
                'weight' => 1.0,
                'category' => 'attribute',
                'evaluate' => fn (BusinessAsset $asset): bool => ! empty($asset->name),
            ],
            [
                'key' => 'has_definition',
                'name' => 'Has Definition',
                'description' => 'Business Asset has a definition',
                'weight' => 2.0,
                'category' => 'attribute',
                'evaluate' => fn (BusinessAsset $asset): bool => ! empty($asset->definition),
            ],
            [
                'key' => 'has_domain',
                'name' => 'Has Domain',
                'description' => 'Business Asset has a domain assigned',
                'weight' => 1.5,
                'category' => 'relationship',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->domain !== null,
            ],
            [
                'key' => 'has_data_initiative',
                'name' => 'Has Data Initiative',
                'description' => 'Business Asset has a data initiative',
                'weight' => 1.5,
                'category' => 'relationship',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->dataInitiative !== null,
            ],
            [
                'key' => 'has_business_rule',
                'name' => 'Has Business Rule',
                'description' => 'Business Asset has at least one business rule',
                'weight' => 2.0,
                'category' => 'relationship',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->businessRules()->exists(),
            ],
            [
                'key' => 'has_data_source',
                'name' => 'Has Data Source',
                'description' => 'Business Asset has at least one data source',
                'weight' => 1.5,
                'category' => 'relationship',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->dataSources()->exists(),
            ],
            [
                'key' => 'has_data_steward',
                'name' => 'Has Data Steward',
                'description' => 'Business Asset has a data steward assigned',
                'weight' => 2.5,
                'category' => 'role',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->dataSteward()->exists(),
            ],
            [
                'key' => 'has_data_owner',
                'name' => 'Has Data Owner',
                'description' => 'Business Asset has a data owner assigned',
                'weight' => 2.5,
                'category' => 'role',
                'evaluate' => fn (BusinessAsset $asset): bool => $asset->dataOwner()->exists(),
            ],
        ];
    }

    /**
     * Find a single criterion definition by key.
     *
     * @return array{key: string, name: string, description: string, weight: float, category: string, evaluate: callable(BusinessAsset): bool}|null
     */
    public static function find(string $key): ?array
    {
        foreach (self::all() as $criterion) {
            if ($criterion['key'] === $key) {
                return $criterion;
            }
        }

        return null;
    }

    /**
     * Maximum achievable score in points (sum of all criteria weights).
     */
    public static function maxPossibleScore(): float
    {
        return array_sum(array_column(self::all(), 'weight'));
    }
}
