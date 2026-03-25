<?php

declare(strict_types=1);

namespace Modules\PublicService\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\PublicService\Models\Complaint;

class ComplaintRepository extends BaseCrudRepository
{
    public function __construct(Complaint $model)
    {
        parent::__construct($model);
    }
}
