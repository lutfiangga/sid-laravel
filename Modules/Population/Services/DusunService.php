<?php

declare(strict_types=1);

namespace Modules\Population\Services;

use App\Core\Base\BaseCrudService;
use Modules\Population\Contracts\Repositories\DusunRepositoryInterface;
use Modules\Population\Contracts\Services\DusunServiceInterface;

class DusunService extends BaseCrudService implements DusunServiceInterface
{
    public function __construct(DusunRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
