<?php

declare(strict_types=1);

namespace Modules\Finance\Services;

use App\Core\Base\BaseCrudService;
use Modules\Finance\Repositories\FinanceAccountRepository;

class FinanceAccountService extends BaseCrudService
{
    public function __construct(FinanceAccountRepository $repository)
    {
        parent::__construct($repository);
    }
}
