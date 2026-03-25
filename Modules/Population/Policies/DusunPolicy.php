<?php

declare(strict_types=1);

namespace Modules\Population\Policies;

use App\Core\Base\BasePolicy;

class DusunPolicy extends BasePolicy
{
    protected function module(): string
    {
        return 'dusun';
    }
}
