<?php

declare(strict_types=1);

namespace App\Core\Base;

use App\Core\Contracts\BaseCrudRepositoryInterface;
use App\Core\Contracts\BaseCrudServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService extends BaseService implements BaseCrudServiceInterface
{
    public function __construct(
        protected BaseCrudRepositoryInterface $repository,
    ) {}

    /**
     * Get all records.
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get a record by ID.
     */
    public function getById(string $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get paginated records.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginated(int $perPage = 15, string $search = '', array $filters = [], array $with = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search, $filters, $with);
    }

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        $data = $this->beforeCreate($data);
        $model = $this->repository->create($data);
        $this->afterCreate($model);

        return $model;
    }

    /**
     * Update an existing record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): Model
    {
        $data = $this->beforeUpdate($id, $data);
        $model = $this->repository->update($id, $data);
        $this->afterUpdate($model);

        return $model;
    }

    /**
     * Delete a record.
     */
    public function delete(string $id): bool
    {
        $this->beforeDelete($id);
        $result = $this->repository->delete($id);
        $this->afterDelete($id);

        return $result;
    }

    /**
     * Hook: before creating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * Hook: after creating a record.
     */
    protected function afterCreate(Model $model): void
    {
        //
    }

    /**
     * Hook: before updating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function beforeUpdate(string $id, array $data): array
    {
        return $data;
    }

    /**
     * Hook: after updating a record.
     */
    protected function afterUpdate(Model $model): void
    {
        //
    }

    /**
     * Hook: before deleting a record.
     */
    protected function beforeDelete(string $id): void
    {
        //
    }

    /**
     * Hook: after deleting a record.
     */
    protected function afterDelete(string $id): void
    {
        //
    }
}
