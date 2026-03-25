<?php

declare(strict_types=1);

namespace App\Core\Base;

use App\Core\Contracts\BaseCrudRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudRepository extends BaseRepository implements BaseCrudRepositoryInterface
{
    /**
     * Create a new model.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing model.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);

        return $model->fresh();
    }

    /**
     * Soft delete a model.
     */
    public function delete(string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    /**
     * Permanently delete a model.
     */
    public function forceDelete(string $id): bool
    {
        return (bool) $this->model->withTrashed()->findOrFail($id)->forceDelete();
    }

    /**
     * Restore a soft-deleted model.
     */
    public function restore(string $id): Model
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        $model->restore();

        return $model->fresh();
    }
}
