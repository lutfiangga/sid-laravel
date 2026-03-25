<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    /**
     * Scope to search across searchable columns.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $searchable = $this->getSearchable();

        if (empty($searchable)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($searchable, $term): void {
            foreach ($searchable as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';

                if (str_contains($column, '.')) {
                    [$relation, $relatedColumn] = explode('.', $column, 2);
                    $q->{$method.'Has'}($relation, function (Builder $sub) use ($relatedColumn, $term): void {
                        $sub->where($relatedColumn, 'like', "%{$term}%");
                    });
                } else {
                    $q->{$method}($column, 'like', "%{$term}%");
                }
            }
        });
    }

    /**
     * Get searchable columns.
     *
     * @return array<int, string>
     */
    abstract public function getSearchable(): array;
}
