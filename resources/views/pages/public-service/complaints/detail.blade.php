<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\PublicService\Models\Complaint;
use Modules\PublicService\Services\ComplaintService;

new class extends Component {
    public ?Complaint $complaint = null;
    
    public string $response = '';
    public string $status = '';

    public function mount(Complaint $complaint): void
    {
        $this->complaint = $complaint->load('penduduk');
        $this->response = $complaint->response ?? '';
        $this->status = $complaint->status;
    }

    public function saveResponse(): void
    {
        $data = $this->validate([
            'response' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,resolved,rejected',
        ]);

        app(ComplaintService::class)->update($this->complaint->id, $data);
        
        $this->dispatch('notify', message: __('Tanggapan dan status berhasil diperbarui.'));
        $this->redirect(route('public-service.complaints.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            
            <div class="md:col-span-2 space-y-6">
                <!-- Complaint Information -->
                <flux:card>
                    <div class="mb-4 flex items-center justify-between border-b pb-4 dark:border-zinc-700">
                        <h2 class="text-xl font-bold">{{ $complaint->title }}</h2>
                        <flux:badge size="sm" class="uppercase">
                            {{ str_replace('_', ' ', $complaint->status) }}
                        </flux:badge>
                    </div>

                    <div class="space-y-4">
                        <section>
                            <h3 class="mb-2 text-xs font-semibold uppercase text-zinc-400">{{ __('Data Pelapor') }}</h3>
                            <div class="flex items-center gap-3 rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                                <flux:icon.user-circle class="text-zinc-400" size="xl" />
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $complaint->penduduk->nama }}</p>
                                    <p class="text-xs text-zinc-500">NIK: {{ $complaint->penduduk->nik }}</p>
                                </div>
                            </div>
                        </section>

                        <section class="border-t pt-4 dark:border-zinc-700">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-zinc-400">{{ __('Deskripsi Laporan') }}</h3>
                            <p class="whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $complaint->description }}
                            </p>
                        </section>
                    </div>
                </flux:card>

                @if($complaint->status === 'resolved' || $complaint->status === 'rejected')
                    <!-- Read-Only Response for resolved ones (or if accessed by User) -->
                    <flux:card>
                        <h3 class="mb-4 text-xs font-semibold uppercase text-zinc-400">{{ __('Tanggapan Desa') }}</h3>
                        <div class="rounded-lg bg-blue-50 p-4 text-sm text-blue-900 dark:bg-blue-900/40 dark:text-blue-200">
                            {{ $complaint->response ?: __('Selesai tanpa tanggapan tertulis.') }}
                        </div>
                    </flux:card>
                @endif
            </div>

            <!-- Action / Response Area (For Admin) -->
            <div class="space-y-6">
                <flux:card>
                    <h3 class="mb-4 text-lg font-bold">{{ __('Tindak Lanjut') }}</h3>
                    
                    <form wire:submit="saveResponse" class="space-y-4">
                        <flux:select wire:model="status" label="{{ __('Ubah Status') }}">
                            <flux:select.option value="pending">{{ __('Menunggu (Pending)') }}</flux:select.option>
                            <flux:select.option value="in_progress">{{ __('Diproses') }}</flux:select.option>
                            <flux:select.option value="resolved">{{ __('Selesai (Resolved)') }}</flux:select.option>
                            <flux:select.option value="rejected">{{ __('Ditolak (Rejected)') }}</flux:select.option>
                        </flux:select>
                        
                        <flux:textarea wire:model="response" label="{{ __('Tanggapan Resmi (Opsional)') }}" rows="6" placeholder="{{ __('Tuliskan hasil investigasi atau solusi untuk warga...') }}" />
                        
                        <div class="pt-2">
                            <flux:button type="submit" variant="primary" class="w-full">
                                {{ __('Simpan & Beritahu') }}
                            </flux:button>
                        </div>
                    </form>
                </flux:card>

                <flux:card>
                    <h3 class="mb-4 text-sm font-semibold uppercase text-zinc-400">{{ __('Info') }}</h3>
                    <div class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400">
                        <div class="flex justify-between">
                            <span>{{ __('Dibuat') }}</span>
                            <span class="font-medium">{{ $complaint->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Diperbarui') }}</span>
                            <span class="font-medium">{{ $complaint->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </flux:card>
            </div>
            
        </div>
    </div>
</div>
