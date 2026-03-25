<?php

declare(strict_types=1);

namespace Modules\Finance\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Finance\Repositories\FinancePeriodRepository;
use Modules\Finance\Services\FinancePeriodService;
use Modules\Finance\Repositories\FinanceAccountRepository;
use Modules\Finance\Services\FinanceAccountService;
use Modules\Finance\Repositories\FinanceBudgetRepository;
use Modules\Finance\Services\FinanceBudgetService;
use Modules\Finance\Repositories\FinanceTransactionRepository;
use Modules\Finance\Services\FinanceTransactionService;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FinancePeriodService::class, function ($app) {
            return new FinancePeriodService($app->make(FinancePeriodRepository::class));
        });

        $this->app->bind(FinanceAccountService::class, function ($app) {
            return new FinanceAccountService($app->make(FinanceAccountRepository::class));
        });

        $this->app->bind(FinanceBudgetService::class, function ($app) {
            return new FinanceBudgetService($app->make(FinanceBudgetRepository::class));
        });

        $this->app->bind(FinanceTransactionService::class, function ($app) {
            return new FinanceTransactionService($app->make(FinanceTransactionRepository::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
