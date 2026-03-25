<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Models\User;

/**
 * HasApproval trait — depends on HasWorkflow.
 *
 * Provides approve/reject convenience methods and
 * will integrate with workflow_histories in Phase 3.
 */
trait HasApproval
{
    /**
     * Approve the model, transitioning its workflow status.
     */
    public function approve(User $user, ?string $note = null): bool
    {
        if (! $this->canTransitionTo('approved')) {
            return false;
        }

        $this->workflow_status = 'approved';
        $this->approved_by = $user->id;
        $this->approved_at = now();

        if ($note) {
            $this->approval_note = $note;
        }

        return $this->save();
    }

    /**
     * Reject the model, transitioning its workflow status.
     */
    public function reject(User $user, ?string $note = null): bool
    {
        if (! $this->canTransitionTo('rejected')) {
            return false;
        }

        $this->workflow_status = 'rejected';
        $this->rejected_by = $user->id;
        $this->rejected_at = now();

        if ($note) {
            $this->rejection_note = $note;
        }

        return $this->save();
    }
}
