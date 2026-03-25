<?php

declare(strict_types=1);

namespace App\Core\Base;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class BasePolicy
{
    use AuthorizesRequests, HandlesAuthorization;

    /**
     * The module name for permission checks (e.g. "penduduk", "surat").
     */
    abstract protected function module(): string;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(mixed $user): bool
    {
        return $user->can("{$this->module()}.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.view");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(mixed $user): bool
    {
        return $user->can("{$this->module()}.create");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.update");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.delete");
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.approve");
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.reject");
    }

    /**
     * Determine whether the user can print the model.
     */
    public function print(mixed $user, mixed $model): bool
    {
        return $user->can("{$this->module()}.print");
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(mixed $user): bool
    {
        return $user->can("{$this->module()}.export");
    }

    /**
     * Determine whether the user can audit the model.
     */
    public function audit(mixed $user): bool
    {
        return $user->can("{$this->module()}.audit");
    }
}
