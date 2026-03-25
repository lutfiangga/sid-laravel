<?php

declare(strict_types=1);

namespace App\Core\Traits;

/**
 * HasWorkflow trait — placeholder for Phase 3 workflow integration.
 *
 * Will provide: relationships to workflow_instances / workflow_steps,
 * getCurrentStatus(), canTransitionTo(), and workflow state management.
 */
trait HasWorkflow
{
    /**
     * Get the current workflow status.
     */
    public function getCurrentWorkflowStatus(): ?string
    {
        return $this->workflow_status ?? null;
    }

    /**
     * Check if the model can transition to a given workflow status.
     */
    public function canTransitionTo(string $status): bool
    {
        $transitions = $this->getAllowedTransitions();

        $currentStatus = $this->getCurrentWorkflowStatus();

        if ($currentStatus === null) {
            return in_array($status, ['draft', 'submitted'], true);
        }

        return in_array($status, $transitions[$currentStatus] ?? [], true);
    }

    /**
     * Get the allowed workflow transitions.
     *
     * @return array<string, array<int, string>>
     */
    protected function getAllowedTransitions(): array
    {
        return [
            'draft' => ['submitted'],
            'submitted' => ['rt_review', 'admin_review', 'rejected', 'returned'],
            'rt_review' => ['rw_review', 'rejected', 'returned'],
            'rw_review' => ['admin_review', 'rejected', 'returned'],
            'admin_review' => ['approved', 'rejected', 'returned'],
            'rejected' => ['draft'],
            'returned' => ['draft'],
        ];
    }
}
