<?php

declare(strict_types=1);

namespace App\Core\Base;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Relationships to eager load.
     *
     * @var array<int, string>
     */
    protected array $with = [];

    public function __construct(
        protected Model $model,
    ) {}

    /**
     * Get the model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Find a model by its primary key.
     */
    public function find(string $id): ?Model
    {
        return $this->newQuery()->find($id);
    }

    /**
     * Find a model by its primary key or throw an exception.
     */
    public function findOrFail(string $id): Model
    {
        return $this->newQuery()->findOrFail($id);
    }

    /**
     * Get all models.
     */
    public function all(): Collection
    {
        return $this->newQuery()->get();
    }

    /**
     * Get paginated models.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $with
     */
    public function paginate(int $perPage = 15, string $search = '', array $filters = [], array $with = []): LengthAwarePaginator
    {
        return $this->applyCriteria($search, $filters, $with)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get results for export.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $with
     */
    public function export(string $search = '', array $filters = [], array $with = []): Collection
    {
        return $this->applyCriteria($search, $filters, $with)
            ->latest()
            ->get();
    }

    /**
     * Apply common criteria to query.
     */
    protected function applyCriteria(string $search = '', array $filters = [], array $with = []): Builder
    {
        $query = $this->newQuery()->with($with);

        foreach ($filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        if ($search && method_exists($this->model, 'scopeSearch')) {
            $query->search($search);
        }

        return $query;
    }

    /**
     * Get a new query builder for the model with eager loads applied.
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery()->with($this->with);
    }
}
