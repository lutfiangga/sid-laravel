<?php

declare(strict_types=1);

namespace Modules\PublicService\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\PublicService\Repositories\ApparatusRepository;
use Modules\PublicService\Services\ApparatusService;
use Modules\PublicService\Repositories\AnnouncementRepository;
use Modules\PublicService\Services\AnnouncementService;
use Modules\PublicService\Repositories\ComplaintRepository;
use Modules\PublicService\Services\ComplaintService;

class PublicServiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApparatusService::class, function ($app) {
            return new ApparatusService($app->make(ApparatusRepository::class));
        });

        $this->app->bind(AnnouncementService::class, function ($app) {
            return new AnnouncementService($app->make(AnnouncementRepository::class));
        });

        $this->app->bind(ComplaintService::class, function ($app) {
            return new ComplaintService($app->make(ComplaintRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
