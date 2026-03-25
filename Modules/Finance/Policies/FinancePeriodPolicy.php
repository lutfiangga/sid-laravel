<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use App\Core\Base\BasePolicy;

class FinancePeriodPolicy extends BasePolicy
{
    protected function module(): string
    {
        return 'finance-period';
    }
}
