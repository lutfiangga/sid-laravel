<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    Modules\Population\Providers\PopulationServiceProvider::class,
    Modules\Correspondence\Providers\CorrespondenceServiceProvider::class,
    Modules\PublicService\Providers\PublicServiceServiceProvider::class,
    Modules\Finance\Providers\FinanceServiceProvider::class,
    Modules\System\Providers\SystemServiceProvider::class,
];
