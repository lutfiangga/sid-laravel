<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Correspondence\Models\LetterRequest;
use Modules\Correspondence\Services\LetterRequestService;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public ?LetterRequest $request = null;
    public string $note = '';

    public function mount(LetterRequest $letterRequest): void
    {
        $this->request = $letterRequest->load(['penduduk', 'type', 'logs.actor']);
    }

    public function approve(): void
    {
        $success = app(LetterRequestService::class)->approveRequest($this->request->id, $this->note);
        
        if ($success) {
            $this->dispatch('notify', message: __('Permohonan berhasil disetujui.'));
            $this->redirect(route('correspondence.letter-request.index'), navigate: true);
        } else {
            $this->dispatch('notify', message: __('Gagal menyetujui permohonan. Periksa status workflow.'), variant: 'danger');
        }
    }

    public function reject(): void
    {
        // Simple rejection logic
        $this->request->workflow_status = 'rejected';
        $this->request->save();

        Modules\Correspondence\Models\WorkflowLog::create([
            'request_id' => $this->request->id,
            'action' => 'reject',
            'actor_id' => Auth::id(),
            'note' => $this->note,
        ]);

        $this->dispatch('notify', message: __('Permohonan ditolak.'));
        $this->redirect(route('correspondence.letter-request.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Request Details -->
            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <div class="mb-6 flex items-center justify-between border-b pb-4 dark:border-zinc-700">
                        <div>
                            <h2 class="text-xl font-bold">{{ $request->type->nama }}</h2>
                            <p class="text-sm text-zinc-500">{{ __('No. Surat: ') }}{{ $request->nomor_surat ?? '-' }}</p>
                        </div>
                        <flux:badge variant="neutral" class="uppercase">
                            {{ str_replace('_', ' ', $request->workflow_status) }}
                        </flux:badge>
                    </div>

                    <div class="space-y-4">
                        <section>
                            <h3 class="mb-2 text-xs font-semibold uppercase text-zinc-400">{{ __('Data Pemohon') }}</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-zinc-500">{{ __('Nama') }}</p>
                                    <p class="font-medium">{{ $request->penduduk->nama }}</p>
                                </div>
                                <div>
                                    <p class="text-zinc-500">{{ __('NIK') }}</p>
                                    <p class="font-medium">{{ $request->penduduk->nik }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-zinc-500">{{ __('Tempat, Tanggal Lahir') }}</p>
                                    <p class="font-medium">{{ $request->penduduk->tempat_lahir }}, {{ $request->penduduk->tanggal_lahir->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </section>

                        <section class="border-t pt-4 dark:border-zinc-700">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-zinc-400">{{ __('Keterangan / Keperluan') }}</h3>
                            <p class="text-sm italic text-zinc-700 dark:text-zinc-300">
                                "{{ $request->data['keperluan'] ?? '-' }}"
                            </p>
                        </section>
                    </div>
                </flux:card>

                <!-- Status Activity Log -->
                <flux:card>
                    <h3 class="mb-4 text-lg font-bold">{{ __('Riwayat Alur Kerja') }}</h3>
                    <div class="space-y-6">
                        @foreach($request->logs->sortByDesc('created_at') as $log)
                            <div class="flex gap-4">
                                <div class="relative flex flex-col items-center">
                                    <div class="h-10 w-10 flex-none rounded-full bg-zinc-100 flex items-center justify-center dark:bg-zinc-800">
                                        @if($log->action === 'submit') <flux:icon.paper-airplane size="sm" class="text-blue-500" />
                                        @elseif($log->action === 'approve') <flux:icon.check-circle size="sm" class="text-emerald-500" />
                                        @elseif($log->action === 'reject') <flux:icon.x-circle size="sm" class="text-red-500" />
                                        @else <flux:icon.chat-bubble-left size="sm" class="text-zinc-400" />
                                        @endif
                                    </div>
                                    @if(!$loop->last)
                                        <div class="mt-2 w-px flex-grow bg-zinc-200 dark:bg-zinc-700"></div>
                                    @endif
                                </div>
                                <div class="pb-6">
                                    <p class="text-sm font-semibold capitalize">{{ $log->action }} {{ __('oleh') }} {{ $log->actor->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $log->created_at->diffForHumans() }}</p>
                                    @if($log->note)
                                        <div class="mt-2 rounded bg-zinc-50 p-2 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                            {{ $log->note }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            </div>

            <!-- Right Column: Actions -->
            <div class="space-y-6">
                @if(in_array($request->workflow_status, ['submitted', 'rt_review', 'rw_review', 'admin_review']))
                    <flux:card>
                        <h3 class="mb-4 text-lg font-bold">{{ __('Tindakan Persetujuan') }}</h3>
                        <div class="space-y-4">
                            <flux:textarea wire:model="note" label="{{ __('Catatan (Opsional)') }}" placeholder="{{ __('Tambahkan alasan atau instruksi...') }}" />
                            
                            <div class="grid grid-cols-1 gap-2">
                                <flux:button wire:click="approve" variant="primary" icon="check" class="w-full">
                                    {{ __('Setujui / Teruskan') }}
                                </flux:button>
                                <flux:button wire:click="reject" 
                                    wire:confirm="{{ __('Apakah Anda yakin ingin menolak permohonan ini?') }}"
                                    variant="ghost" color="danger" icon="x-mark" class="w-full">
                                    {{ __('Tolak Permohonan') }}
                                </flux:button>
                            </div>
                        </div>
                    </flux:card>
                @endif

                <flux:card>
                    <h3 class="mb-4 text-sm font-semibold uppercase text-zinc-400">{{ __('Info Tambahan') }}</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Dibuat') }}</span>
                            <span>{{ $request->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Diperbarui') }}</span>
                            <span>{{ $request->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</div>
