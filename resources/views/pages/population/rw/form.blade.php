<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\RwServiceInterface;
use Modules\Population\Contracts\Services\DusunServiceInterface;
use Modules\Population\Models\Rw;
use Modules\Population\Http\Requests\Rw\StoreRwRequest;
use Modules\Population\Http\Requests\Rw\UpdateRwRequest;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;

new #[Title('Form RW')] class extends Component {
    public ?Rw $rw = null;

    public string $dusun_id = '';
    public string $nomor = '';
    public ?string $ketua = '';

    public function mount(?Rw $rw = null): void
    {
        if ($rw && $rw->exists) {
            $this->rw = $rw;
            $this->dusun_id = $rw->dusun_id;
            $this->nomor = $rw->nomor;
            $this->ketua = $rw->ketua;
        }
    }

    #[Computed]
    public function dusuns()
    {
        return app(DusunServiceInterface::class)->getAll();
    }

    public function save(RwServiceInterface $service)
    {
        $request = $this->rw ? new UpdateRwRequest() : new StoreRwRequest();
        
        $validated = $this->validate($request->rules());

        if ($this->rw) {
            $service->update($this->rw->id, $validated);
        } else {
            $service->create($validated);
        }

        return redirect()->route('population.rw.index');
    }
}; ?>

<section class="max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">{{ $rw ? __('Edit RW') : __('Tambah RW') }}</flux:heading>
        <flux:subheading>{{ __('Lengkapi informasi detail RW di bawah ini.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Pilih Dusun') }}</flux:label>
                <flux:select wire:model="dusun_id" placeholder="{{ __('Pilih dusun...') }}">
                    @foreach ($this->dusuns as $dusun)
                        <flux:select.option value="{{ $dusun->id }}">{{ $dusun->nama }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="dusun_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Nomor RW') }}</flux:label>
                <flux:input wire:model="nomor" placeholder="Contoh: 001" />
                <flux:error name="nomor" />
                <flux:description>{{ __('Nomor RW unik dalam satu dusun.') }}</flux:description>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Ketua RW') }}</flux:label>
                <flux:input wire:model="ketua" placeholder="Nama lengkap ketua RW" />
                <flux:error name="ketua" />
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan Data') }}
                </flux:button>
                <flux:button :href="route('population.rw.index')" variant="ghost">
                    {{ __('Batal') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</section>
