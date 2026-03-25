<?php

declare(strict_types=1);

namespace Modules\Correspondence\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Correspondence\Repositories\LetterTypeRepository;
use Modules\Correspondence\Repositories\LetterRequestRepository;
use Modules\Correspondence\Services\LetterTypeService;
use Modules\Correspondence\Services\LetterRequestService;

class CorrespondenceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LetterTypeRepository::class);
        $this->app->singleton(LetterRequestRepository::class);
        
        $this->app->singleton(LetterTypeService::class);
        $this->app->singleton(LetterRequestService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
