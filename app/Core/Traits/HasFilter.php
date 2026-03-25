<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    /**
     * Scope to apply filters to the query.
     *
     * @param  Builder<static>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<static>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $filterable = $this->getFilterable();

        foreach ($filters as $column => $value) {
            if (! in_array($column, $filterable, true) || blank($value)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    /**
     * Get filterable columns.
     *
     * @return array<int, string>
     */
    abstract public function getFilterable(): array;
}
