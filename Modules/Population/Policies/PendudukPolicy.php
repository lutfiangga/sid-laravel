<?php

declare(strict_types=1);

namespace Modules\Population\Policies;

use App\Core\Base\BasePolicy;

class PendudukPolicy extends BasePolicy
{
    protected function module(): string
    {
        return 'penduduk';
    }
}
