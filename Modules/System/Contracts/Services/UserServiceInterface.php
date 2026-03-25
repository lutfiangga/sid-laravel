<?php

declare(strict_types=1);

namespace Modules\System\Contracts\Services;

use App\Core\Contracts\BaseCrudServiceInterface;
use App\Models\User;

interface UserServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $roleName
     * @return User
     */
    public function assignRole(User $user, string $roleName): User;

    /**
     * Sync multiple roles for a user.
     *
     * @param User $user
     * @param array<int, string> $roles Array of role names
     * @return User
     */
    public function syncRoles(User $user, array $roles): User;
}
