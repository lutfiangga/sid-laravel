<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\RtServiceInterface;
use Modules\Population\Contracts\Services\RwServiceInterface;
use Modules\Population\Models\Rt;
use Modules\Population\Http\Requests\Rt\StoreRtRequest;
use Modules\Population\Http\Requests\Rt\UpdateRtRequest;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;

new #[Title('Form RT')] class extends Component {
    public ?Rt $rt = null;

    public string $rw_id = '';
    public string $nomor = '';
    public ?string $ketua = '';

    public function mount(?Rt $rt = null): void
    {
        if ($rt && $rt->exists) {
            $this->rt = $rt;
            $this->rw_id = $rt->rw_id;
            $this->nomor = $rt->nomor;
            $this->ketua = $rt->ketua;
        }
    }

    #[Computed]
    public function rws()
    {
        return app(RwServiceInterface::class)->getAll();
    }

    public function save(RtServiceInterface $service)
    {
        $request = $this->rt ? new UpdateRtRequest() : new StoreRtRequest();
        
        $validated = $this->validate($request->rules());

        if ($this->rt) {
            $service->update($this->rt->id, $validated);
        } else {
            $service->create($validated);
        }

        return redirect()->route('population.rt.index');
    }
}; ?>

<section class="max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">{{ $rt ? __('Edit RT') : __('Tambah RT') }}</flux:heading>
        <flux:subheading>{{ __('Lengkapi informasi detail RT di bawah ini.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Pilih RW') }}</flux:label>
                <flux:select wire:model="rw_id" placeholder="{{ __('Pilih RW...') }}">
                    @foreach ($this->rws as $rw)
                        <flux:select.option value="{{ $rw->id }}">RW {{ $rw->nomor }} - {{ $rw->dusun->nama ?? '' }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="rw_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Nomor RT') }}</flux:label>
                <flux:input wire:model="nomor" placeholder="Contoh: 001" />
                <flux:error name="nomor" />
                <flux:description>{{ __('Nomor RT unik dalam satu RW.') }}</flux:description>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Ketua RT') }}</flux:label>
                <flux:input wire:model="ketua" placeholder="Nama lengkap ketua RT" />
                <flux:error name="ketua" />
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan Data') }}
                </flux:button>
                <flux:button :href="route('population.rt.index')" variant="ghost">
                    {{ __('Batal') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</section>
