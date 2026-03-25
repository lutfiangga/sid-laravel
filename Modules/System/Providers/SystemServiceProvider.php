<?php

declare(strict_types=1);

namespace Modules\System\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\System\Contracts\Repositories\RoleRepositoryInterface;
use Modules\System\Contracts\Repositories\UserRepositoryInterface;
use Modules\System\Contracts\Services\RoleServiceInterface;
use Modules\System\Contracts\Services\UserServiceInterface;
use Modules\System\Repositories\RoleRepository;
use Modules\System\Repositories\UserRepository;
use Modules\System\Services\RoleService;
use Modules\System\Services\UserService;

class SystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Bind services
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations or views here if required in the future for system settings
    }
}
