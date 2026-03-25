<?php

declare(strict_types=1);

namespace Modules\Correspondence\Policies;

use App\Core\Base\BasePolicy;
use App\Models\User;
use Modules\Correspondence\Models\LetterRequest;
use Modules\Population\Models\Penduduk;

class LetterRequestPolicy extends BasePolicy
{
    public function module(): string
    {
        return 'letter-request';
    }

    /**
     * Warga can only view their own requests. Staff can view all.
     */
    public function view(mixed $user, mixed $model): bool
    {
        if (! $user instanceof User || ! $model instanceof LetterRequest) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('Warga')) {
            /** @var ?Penduduk $penduduk */
            $penduduk = Penduduk::where('user_id', $user->id)->first();

            return $penduduk && $model->penduduk_id === $penduduk->id;
        }

        return parent::view($user, $model);
    }

    /**
     * Warga can delete only their own requests (when still draft).
     */
    public function delete(mixed $user, mixed $model): bool
    {
        if (! $user instanceof User || ! $model instanceof LetterRequest) {
            return false;
        }

        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        if ($user->hasRole('Warga')) {
            /** @var ?Penduduk $penduduk */
            $penduduk = Penduduk::where('user_id', $user->id)->first();

            return $penduduk && $model->penduduk_id === $penduduk->id && $model->workflow_status === 'draft';
        }

        return parent::delete($user, $model);
    }
}
