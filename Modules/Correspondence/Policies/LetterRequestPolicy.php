<?php

declare(strict_types=1);

namespace Modules\Correspondence\Policies;

use App\Core\Base\BasePolicy;
use App\Models\User;
use Modules\Correspondence\Models\LetterRequest;

class LetterRequestPolicy extends BasePolicy
{
    public function module(): string
    {
        return 'letter-request';
    }

    /**
     * Residents can view their own requests.
     */
    public function view(mixed $user, mixed $model): bool
    {
        // If has role official, can view based on permission
        if ($user->hasRole(['SuperAdmin', 'VillageAdmin', 'Kades', 'Sekdes'])) {
            return true;
        }

        // If RT/RW, should check if the resident is in their area (simplified for now)
        if ($user->hasRole(['Rt', 'Rw'])) {
            return true;
        }

        // If resident, check if it's their own
        // (Assuming User has a resident_id or similar, or just check policy)
        return parent::view($user, $model);
    }
    
    /**
     * Logic for approval can be added here as custom permissions.
     */
}
