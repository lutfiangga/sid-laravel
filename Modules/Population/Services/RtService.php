<?php

declare(strict_types=1);

namespace Modules\Population\Services;

use App\Core\Base\BaseCrudService;
use Modules\Population\Contracts\Repositories\RtRepositoryInterface;
use Modules\Population\Contracts\Services\RtServiceInterface;

class RtService extends BaseCrudService implements RtServiceInterface
{
    public function __construct(RtRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
