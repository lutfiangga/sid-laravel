<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\PublicService\Services\ComplaintService;
use Modules\Population\Models\Penduduk;

new class extends Component {
    public string $title = '';
    public string $description = '';

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Mock linking to a standard resident (for demo purposes)
        $penduduk = Penduduk::first();

        app(ComplaintService::class)->create([
            'penduduk_id' => $penduduk->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'pending',
        ]);

        $this->dispatch('notify', message: __('Pengaduan berhasil dikirim.'));
        $this->redirect(route('public-service.complaints.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ __('Buat Pengaduan / Aspirasi Baru') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Sampaikan keluhan, saran, atau laporan Anda kepada apparat desa.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">
            <!-- Implicit sender mapping assumed from session. Display only for now -->
            <div class="rounded-lg bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                {{ __('Laporan ini akan dikaitkan dengan akun kependudukan Anda secara otomatis saat dikirim.') }}
            </div>

            <flux:input wire:model="title" label="{{ __('Judul Pengaduan') }}" placeholder="e.g. Jalan rusak di RT 03"
                required />

            <flux:textarea wire:model="description" label="{{ __('Deskripsi Detail') }}"
                placeholder="{{ __('Jelaskan dengan detail lokasi, waktu, dan kejadian...') }}" rows="6" required />

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('public-service.complaints.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Kirim Laporan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>