<?php

declare(strict_types=1);

namespace Modules\PublicService\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\PublicService\Models\Announcement;

class AnnouncementRepository extends BaseCrudRepository
{
    public function __construct(Announcement $model)
    {
        parent::__construct($model);
    }
}
