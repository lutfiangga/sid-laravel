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
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $with
     */
    public function paginate(int $perPage = 15, string $search = '', array $filters = [], array $with = []): LengthAwarePaginator;

    /**
     * Get results for export.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $with
     */
    public function export(string $search = '', array $filters = [], array $with = []): Collection;
}
