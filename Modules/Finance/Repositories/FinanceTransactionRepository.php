<?php

declare(strict_types=1);

namespace Modules\Finance\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Finance\Models\FinanceTransaction;

class FinanceTransactionRepository extends BaseCrudRepository
{
    public function __construct(FinanceTransaction $model)
    {
        parent::__construct($model);
    }
}
