<?php

declare(strict_types=1);

namespace Modules\Correspondence\Policies;

use App\Core\Base\BasePolicy;

class LetterTypePolicy extends BasePolicy
{
    public function module(): string
    {
        return 'letter-type';
    }
}
