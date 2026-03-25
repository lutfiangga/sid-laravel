<?php

declare(strict_types=1);

namespace Modules\System\Services;

use App\Core\Base\BaseCrudService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\System\Contracts\Repositories\UserRepositoryInterface;
use Modules\System\Contracts\Services\UserServiceInterface;

class UserService extends BaseCrudService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Hook to hash user password if provided during creation or update.
     */
    protected function beforeCreate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $data;
    }

    protected function beforeUpdate(string $id, array $data): array
    {
        // Only hash password if explicitly provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password field so it doesn't overwrite with empty
            unset($data['password']);
        }

        return $data;
    }

    /**
     * Assign a single role to a user.
     *
     * @param User $user
     * @param string $roleName
     * @return User
     */
    public function assignRole(User $user, string $roleName): User
    {
        $user->assignRole($roleName);

        return $user;
    }

    /**
     * Sync multiple roles for a user.
     *
     * @param User $user
     * @param array<int, string> $roles Array of role names
     * @return User
     */
    public function syncRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);

        return $user;
    }
}
