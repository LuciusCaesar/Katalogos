<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Searchable
 *
 * Provides reusable search functionality for Eloquent models.
 * Supports searching across multiple fields with LIKE queries.
 */
trait Searchable
{
    /**
     * Apply search to the query based on search term.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === '' || $search === null) {
            return $query;
        }

        $searchTerm = '%'.$search.'%';
        $searchableFields = $this->getSearchableFields();

        $query->where(function (Builder $q) use ($searchTerm, $searchableFields) {
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'LIKE', $searchTerm);
            }
        });

        return $query;
    }

    /**
     * Get fields that should be searched.
     * Override this method in models to specify searchable fields.
     *
     * @return array<string>
     */
    public function getSearchableFields(): array
    {
        return $this->fillable ?? [];
    }

    /**
     * Apply search with relationship support.
     *
     * @param  array<string>  $relations
     */
    public function scopeSearchWithRelations(Builder $query, string $search, array $relations = []): Builder
    {
        if ($search === '' || $search === null) {
            return $query;
        }

        $searchTerm = '%'.$search.'%';

        $query->where(function (Builder $q) use ($searchTerm, $relations) {
            // Search in direct fields
            $searchableFields = $this->getSearchableFields();
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'LIKE', $searchTerm);
            }

            // Search in relationships
            foreach ($relations as $relation => $fields) {
                if (is_array($fields)) {
                    $q->orWhereHas($relation, function (Builder $rq) use ($searchTerm, $fields) {
                        foreach ($fields as $field) {
                            $rq->orWhere($field, 'LIKE', $searchTerm);
                        }
                    });
                }
            }
        });

        return $query;
    }
}
