<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Find a model by its primary key.
     */
    public function find(string $id): ?Model;

    /**
     * Find a model by its primary key or throw an exception.
     */
    public function findOrFail(string $id): Model;

    /**
     * Get all models.
     */
    public function all(): Collection;

    /**
     * Get paginated models.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
