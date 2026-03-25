<?php

declare(strict_types=1);

namespace Modules\Finance\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Finance\Models\FinanceAccount;

class FinanceAccountRepository extends BaseCrudRepository
{
    public function __construct(FinanceAccount $model)
    {
        parent::__construct($model);
    }
}
