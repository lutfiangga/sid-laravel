<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseCrudRepositoryInterface extends RepositoryInterface
{
    /**
     * Create a new model.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * Update an existing model.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Model;

    /**
     * Soft delete a model.
     */
    public function delete(string $id): bool;

    /**
     * Permanently delete a model.
     */
    public function forceDelete(string $id): bool;

    /**
     * Restore a soft-deleted model.
     */
    public function restore(string $id): Model;
}
