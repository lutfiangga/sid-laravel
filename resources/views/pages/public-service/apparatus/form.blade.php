<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\PublicService\Models\Apparatus;
use Modules\PublicService\Services\ApparatusService;

new class extends Component {
    public ?Apparatus $apparatus = null;
    
    public string $nama = '';
    public string $jabatan = '';
    public string $nip = '';
    public string $status = 'aktif';
    // skip nullable user_id binding for now unless provided by user-picker picker list

    public function mount(?Apparatus $apparatus = null): void
    {
        if ($apparatus && $apparatus->exists) {
            $this->apparatus = $apparatus;
            $this->nama = $apparatus->nama;
            $this->jabatan = $apparatus->jabatan;
            $this->nip = $apparatus->nip ?? '';
            $this->status = $apparatus->status;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        if ($this->apparatus) {
            app(ApparatusService::class)->update($this->apparatus->id, $data);
            $message = __('Data aparatur berhasil diperbarui.');
        } else {
            app(ApparatusService::class)->create($data);
            $message = __('Aparatur berhasil ditambahkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('public-service.apparatus.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ $apparatus ? __('Edit Aparatur') : __('Tambah Aparatur Baru') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Isi informasi lengkap tentang perangkat desa ini.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="nama" label="{{ __('Nama Lengkap') }}" placeholder="John Doe" required />

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <flux:input wire:model="jabatan" label="{{ __('Jabatan') }}" placeholder="e.g. Kepala Desa, Bendahara"
                    required />
                <flux:input wire:model="nip" label="{{ __('NIP / NIK') }}"
                    placeholder="Nomor Induk Pegawai (opsional)" />
            </div>

            <flux:radio.group wire:model="status" label="{{ __('Status') }}" class="flex gap-4">
                <flux:radio value="aktif" label="{{ __('Aktif') }}" />
                <flux:radio value="tidak_aktif" label="{{ __('Tidak Aktif') }}" />
            </flux:radio.group>

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('public-service.apparatus.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>