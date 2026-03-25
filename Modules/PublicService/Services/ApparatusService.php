<?php

declare(strict_types=1);

namespace Modules\PublicService\Services;

use App\Core\Base\BaseCrudService;
use Modules\PublicService\Repositories\ApparatusRepository;

class ApparatusService extends BaseCrudService
{
    public function __construct(ApparatusRepository $repository)
    {
        parent::__construct($repository);
    }
}
