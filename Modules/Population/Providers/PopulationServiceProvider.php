<?php

declare(strict_types=1);

namespace Modules\Population\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Population\Contracts\Repositories\DusunRepositoryInterface;
use Modules\Population\Contracts\Repositories\KartuKeluargaRepositoryInterface;
use Modules\Population\Contracts\Repositories\PendudukRepositoryInterface;
use Modules\Population\Contracts\Repositories\RtRepositoryInterface;
use Modules\Population\Contracts\Repositories\RwRepositoryInterface;
use Modules\Population\Contracts\Services\DusunServiceInterface;
use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;
use Modules\Population\Contracts\Services\PendudukServiceInterface;
use Modules\Population\Contracts\Services\RtServiceInterface;
use Modules\Population\Contracts\Services\RwServiceInterface;
use Modules\Population\Repositories\DusunRepository;
use Modules\Population\Repositories\KartuKeluargaRepository;
use Modules\Population\Repositories\PendudukRepository;
use Modules\Population\Repositories\RtRepository;
use Modules\Population\Repositories\RwRepository;
use Modules\Population\Services\DusunService;
use Modules\Population\Services\KartuKeluargaService;
use Modules\Population\Services\PendudukService;
use Modules\Population\Services\RtService;
use Modules\Population\Services\RwService;

class PopulationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repositories
        $this->app->bind(DusunRepositoryInterface::class, DusunRepository::class);
        $this->app->bind(RwRepositoryInterface::class, RwRepository::class);
        $this->app->bind(RtRepositoryInterface::class, RtRepository::class);
        $this->app->bind(KartuKeluargaRepositoryInterface::class, KartuKeluargaRepository::class);
        $this->app->bind(PendudukRepositoryInterface::class, PendudukRepository::class);

        // Bind Services
        $this->app->bind(DusunServiceInterface::class, DusunService::class);
        $this->app->bind(RwServiceInterface::class, RwService::class);
        $this->app->bind(RtServiceInterface::class, RtService::class);
        $this->app->bind(KartuKeluargaServiceInterface::class, KartuKeluargaService::class);
        $this->app->bind(PendudukServiceInterface::class, PendudukService::class);
    }

    public function boot(): void
    {
        // Add policies, routes, etc. if needed later
    }
}
