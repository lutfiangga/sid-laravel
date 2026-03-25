<?php

declare(strict_types=1);

namespace Modules\PublicService\Services;

use App\Core\Base\BaseCrudService;
use Modules\PublicService\Repositories\AnnouncementRepository;

class AnnouncementService extends BaseCrudService
{
    public function __construct(AnnouncementRepository $repository)
    {
        parent::__construct($repository);
    }
}
