<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginatesCatalog
{
    /**
     * Paginate a catalog listing.
     *
     * The listing is pre-filtered by the global search query (?q=, Scout
     * database driver) and, on the "my" routes (web.my-*), scoped to the
     * entities the authenticated user is responsible for.
     *
     * @param  class-string<Model>  $model
     * @param  array<int, string|\Closure>  $with
     * @param  array<int, string>  $withCount
     */
    protected function paginateCatalog(string $model, array $with = [], array $withCount = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = trim((string) request()->query('q', ''));

        $applyScope = function (Builder $builder) use ($with, $withCount): Builder {
            $builder->with($with)->withCount($withCount);

            if (request()->routeIs('web.my-*')) {
                $builder->whereHas('roleAssignments', fn (Builder $sub) => $sub->where('user_id', auth()->id()));
            }

            return $builder->latest();
        };

        if ($query !== '') {
            return $model::search($query)
                ->query($applyScope)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $applyScope($model::query())->paginate($perPage)->withQueryString();
    }
}
