<?php

declare(strict_types=1);

namespace Modules\Population\Policies;

use App\Core\Base\BasePolicy;

class RwPolicy extends BasePolicy
{
    protected function module(): string
    {
        return 'rw';
    }
}
