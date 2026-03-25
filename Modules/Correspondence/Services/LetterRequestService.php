<?php

declare(strict_types=1);

namespace Modules\Correspondence\Services;

use App\Core\Base\BaseCrudService;
use Illuminate\Support\Facades\Auth;
use Modules\Correspondence\Models\WorkflowLog;
use Modules\Correspondence\Repositories\LetterRequestRepository;

class LetterRequestService extends BaseCrudService
{
    public function __construct(LetterRequestRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Submit a request for process.
     */
    public function submitRequest(string $requestId, ?string $note = null): bool
    {
        $request = $this->repository->find($requestId);
        if (! $request) {
            return false;
        }

        if (! $request->canTransitionTo('submitted')) {
            return false;
        }

        $request->workflow_status = 'submitted';
        $request->save();

        WorkflowLog::create([
            'request_id' => $request->id,
            'action' => 'submit',
            'actor_id' => Auth::id(),
            'note' => $note,
        ]);

        return true;
    }

    /**
     * Approve a request.
     */
    public function approveRequest(string $requestId, ?string $note = null): bool
    {
        $request = $this->repository->find($requestId);
        if (! $request || ! $request->type) {
            return false;
        }

        $levels = $request->type->approval_levels ?? [];
        $currentStatus = $request->workflow_status;

        // Map status to level codes
        $statusToLevel = [
            'submitted' => null, // Beginning
            'rt_review' => 'rt',
            'rw_review' => 'rw',
            'lurah_review' => 'lurah',
            'admin_review' => 'admin',
        ];

        $currentLevelCode = $statusToLevel[$currentStatus] ?? null;

        // Find index of current level (or -1 if just submitted)
        $currentIndex = -1;
        if ($currentLevelCode) {
            $currentIndex = array_search($currentLevelCode, $levels);
        }

        // Determine next level
        $nextIndex = $currentIndex + 1;

        if (isset($levels[$nextIndex])) {
            $nextLevelCode = $levels[$nextIndex];
            $nextStatus = match ($nextLevelCode) {
                'rt' => 'rt_review',
                'rw' => 'rw_review',
                'lurah' => 'lurah_review',
                'admin' => 'admin_review',
                default => 'approved',
            };
        } else {
            // No more levels, final approval
            $nextStatus = 'approved';
        }

        if ($nextStatus === 'approved') {
            if (! $request->nomor_surat) {
                $request->nomor_surat = $this->generateNomorSurat($request);
            }
        }

        if (! $request->canTransitionTo($nextStatus)) {
            return false;
        }

        $request->workflow_status = $nextStatus;
        $request->save();

        WorkflowLog::create([
            'request_id' => $request->id,
            'action' => 'approve',
            'actor_id' => Auth::id(),
            'note' => $note,
        ]);

        return true;
    }

    /**
     * Generate a letter number.
     */
    protected function generateNomorSurat($request): string
    {
        $count = $this->repository->getModel()::whereYear('created_at', now()->year)
            ->whereNotNull('nomor_surat')
            ->count() + 1;

        return sprintf('%03d/%s/%s/%d', $count, $request->type->kode, 'PEM-DS', now()->year);
    }
}
