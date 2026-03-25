<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseCrudServiceInterface
{
    /**
     * Get all records.
     */
    public function getAll(): Collection;

    /**
     * Get a record by ID.
     */
    public function getById(string $id): Model;

    /**
     * Get paginated records.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginated(int $perPage = 15, string $search = '', array $filters = [], array $with = []): LengthAwarePaginator;

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * Update an existing record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Model;

    /**
     * Delete a record.
     */
    public function delete(string $id): bool;
}
