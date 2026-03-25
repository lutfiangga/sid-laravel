<?php

declare(strict_types=1);

namespace Modules\Correspondence\Services;

use App\Core\Base\BaseCrudService;
use Modules\Correspondence\Models\WorkflowLog;
use Modules\Correspondence\Repositories\LetterRequestRepository;
use Illuminate\Support\Facades\Auth;

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
        if (!$request) {
            return false;
        }

        if (!$request->canTransitionTo('submitted')) {
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
        if (!$request) {
            return false;
        }

        // Determine next status based on current status and user role
        // This is a simplified logic for initial implementation
        $nextStatus = 'approved'; 
        
        // If RT approves, maybe next is RW
        // If RW approves, maybe next is Admin
        // For now, let's keep it simple or follow the trait's transitions

        if ($request->workflow_status === 'submitted') {
            $nextStatus = 'rt_review';
        } elseif ($request->workflow_status === 'rt_review') {
            $nextStatus = 'rw_review';
        } elseif ($request->workflow_status === 'rw_review') {
            $nextStatus = 'admin_review';
        } elseif ($request->workflow_status === 'admin_review') {
            $nextStatus = 'approved';
            // Generate nomor surat here if needed
            if (!$request->nomor_surat) {
                $request->nomor_surat = $this->generateNomorSurat($request);
            }
        }

        if (!$request->canTransitionTo($nextStatus)) {
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
            
        return sprintf("%03d/%s/%s/%d", $count, $request->type->kode, 'PEM-DS', now()->year);
    }
}
