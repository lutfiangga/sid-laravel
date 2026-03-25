<?php

declare(strict_types=1);

namespace Modules\Population\Services;

use App\Core\Base\BaseCrudService;
use Modules\Population\Contracts\Repositories\RwRepositoryInterface;
use Modules\Population\Contracts\Services\RwServiceInterface;

class RwService extends BaseCrudService implements RwServiceInterface
{
    public function __construct(RwRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
