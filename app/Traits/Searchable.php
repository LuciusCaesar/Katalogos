<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null || $search === '') {
            return $query;
        }

        $searchTerm = '%'.$search.'%';
        $searchableFields = $this->getSearchableFields();

        // @phpstan-ignore-next-line
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
        /** @var array<string>|null $fillable */
        $fillable = $this->fillable ?? null;

        return $fillable ?: [];
    }

    /**
     * Apply search with relationship support.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, array<string>|string>  $relations
     * @return Builder<TModel>
     */
    public function scopeSearchWithRelations(Builder $query, ?string $search, array $relations = []): Builder
    {
        if ($search === null || $search === '') {
            return $query;
        }

        $searchTerm = '%'.$search.'%';

        // @phpstan-ignore-next-line
        $query->where(function (Builder $q) use ($searchTerm, $relations) {
            // Search in direct fields
            $searchableFields = $this->getSearchableFields();
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'LIKE', $searchTerm);
            }

            // Search in relationships
            foreach ($relations as $relation => $fields) {
                if (is_array($fields)) {
                    // @phpstan-ignore-next-line
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
