<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\DusunServiceInterface;
use Modules\Population\Models\Dusun;
use Modules\Population\Http\Requests\Dusun\StoreDusunRequest;
use Modules\Population\Http\Requests\Dusun\UpdateDusunRequest;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Form Dusun')] class extends Component {
    public ?Dusun $dusun = null;

    public string $nama = '';
    public string $kode = '';
    public ?string $ketua = '';

    public function mount(?Dusun $dusun = null): void
    {
        if ($dusun && $dusun->exists) {
            $this->dusun = $dusun;
            $this->nama = $dusun->nama;
            $this->kode = $dusun->kode;
            $this->ketua = $dusun->ketua;
        }
    }

    public function save(DusunServiceInterface $service)
    {
        $request = $this->dusun ? new UpdateDusunRequest() : new StoreDusunRequest();
        
        $validated = $this->validate($request->rules());

        if ($this->dusun) {
            $service->update($this->dusun->id, $validated);
        } else {
            $service->create($validated);
        }

        return redirect()->route('population.dusun.index');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl">{{ $dusun ? __('Edit Dusun') : __('Tambah Dusun') }}</flux:heading>
        <flux:subheading>{{ __('Lengkapi informasi detail dusun di bawah ini.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Kode Dusun') }}</flux:label>
                <flux:input wire:model="kode" placeholder="Contoh: DSN01" />
                <flux:error name="kode" />
                <flux:description>{{ __('Kode unik untuk identifikasi dusun.') }}</flux:description>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Nama Dusun') }}</flux:label>
                <flux:input wire:model="nama" placeholder="Masukkan nama dusun" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Ketua Dusun') }}</flux:label>
                <flux:input wire:model="ketua" placeholder="Nama lengkap ketua dusun" />
                <flux:error name="ketua" />
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan Data') }}
                </flux:button>
                <flux:button :href="route('population.dusun.index')" variant="ghost">
                    {{ __('Batal') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</section>
