<?php

declare(strict_types=1);

namespace Modules\PublicService\Services;

use App\Core\Base\BaseCrudService;
use Modules\PublicService\Repositories\ComplaintRepository;

class ComplaintService extends BaseCrudService
{
    public function __construct(ComplaintRepository $repository)
    {
        parent::__construct($repository);
    }
}
