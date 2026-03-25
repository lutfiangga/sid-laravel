<?php

declare(strict_types=1);

namespace Modules\PublicService\Policies;

use App\Core\Base\BasePolicy;

class AnnouncementPolicy extends BasePolicy
{
    /**
     * Get the module name.
     */
    protected function module(): string
    {
        return 'announcement';
    }
}
