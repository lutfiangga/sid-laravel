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
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->newQuery()->latest()->paginate($perPage);
    }

    /**
     * Get a new query builder for the model with eager loads applied.
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery()->with($this->with);
    }
}
