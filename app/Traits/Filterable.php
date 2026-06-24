<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Filterable
 *
 * Provides reusable filtering functionality for Eloquent models.
 * Supports filtering by related models, foreign keys, and custom filter logic.
 */
trait Filterable
{
    /**
     * Apply filters to the query based on request parameters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Handle different filter types
            // First, try to strip _id suffix for custom filter methods
            $filterField = str_replace('_id', '', $field);

            if (method_exists($this, 'apply'.str_replace('_', '', ucwords($filterField, '_')).'Filter')) {
                // Custom filter method exists (e.g., applyDomainFilter for domain_id)
                $method = 'apply'.str_replace('_', '', ucwords($filterField, '_')).'Filter';
                $query = $this->$method($query, $value);
            } elseif (method_exists($this, $filterField) && $field !== $filterField) {
                // Relationship filter - filter by related model (e.g., dataSteward for data_steward_id)
                $query = $this->applyRelationFilter($query, $filterField, $value);
            } elseif (method_exists($this, $field)) {
                // Relationship filter - filter by related model
                $query = $this->applyRelationFilter($query, $field, $value);
            } elseif (in_array($field, $this->getFilterableFields())) {
                // Direct field filter
                $query = $this->applyDirectFilter($query, $field, $value);
            }
        }

        return $query;
    }

    /**
     * Apply filter for a direct model field.
     */
    protected function applyDirectFilter(Builder $query, string $field, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn($field, $value);
        }

        return $query->where($field, $value);
    }

    /**
     * Apply filter for a relationship.
     */
    protected function applyRelationFilter(Builder $query, string $relation, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereHas($relation, function (Builder $q) use ($value) {
                $q->whereIn('id', $value);
            });
        }

        return $query->whereHas($relation, function (Builder $q) use ($value) {
            $q->where('id', $value);
        });
    }

    /**
     * Get fields that can be directly filtered.
     * Override this method in models to specify filterable fields.
     *
     * @return array<string>
     */
    public function getFilterableFields(): array
    {
        return $this->fillable ?? [];
    }

    /**
     * Apply domain filter (for BusinessAsset and similar models).
     */
    protected function applyDomainFilter(Builder $query, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn('domain_id', $value);
        }

        return $query->where('domain_id', $value);
    }

    /**
     * Apply dataInitiative filter.
     */
    protected function applyDataInitiativeFilter(Builder $query, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn('data_initiative_id', $value);
        }

        return $query->where('data_initiative_id', $value);
    }

    /**
     * Apply dataSteward filter using polymorphic relationship.
     */
    protected function applyDataStewardFilter(Builder $query, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereHas('roleAssignments', function (Builder $q) use ($value) {
                $q->whereIn('user_id', $value)
                    ->whereHas('role', function (Builder $rq) {
                        $rq->where('name', 'Data Steward');
                    });
            });
        }

        return $query->whereHas('roleAssignments', function (Builder $q) use ($value) {
            $q->where('user_id', $value)
                ->whereHas('role', function (Builder $rq) {
                    $rq->where('name', 'Data Steward');
                });
        });
    }

    /**
     * Apply dataOwner filter using polymorphic relationship.
     */
    protected function applyDataOwnerFilter(Builder $query, mixed $value): Builder
    {
        if (is_array($value)) {
            return $query->whereHas('roleAssignments', function (Builder $q) use ($value) {
                $q->whereIn('user_id', $value)
                    ->whereHas('role', function (Builder $rq) {
                        $rq->where('name', 'Data Owner');
                    });
            });
        }

        return $query->whereHas('roleAssignments', function (Builder $q) use ($value) {
            $q->where('user_id', $value)
                ->whereHas('role', function (Builder $rq) {
                    $rq->where('name', 'Data Owner');
                });
        });
    }
}
