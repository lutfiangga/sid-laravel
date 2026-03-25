<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;
use Modules\Population\Contracts\Services\RtServiceInterface;
use Modules\Population\Models\KartuKeluarga;
use Modules\Population\Http\Requests\KartuKeluarga\StoreKartuKeluargaRequest;
use Modules\Population\Http\Requests\KartuKeluarga\UpdateKartuKeluargaRequest;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;

new #[Title('Form Kartu Keluarga')] class extends Component {
    public ?KartuKeluarga $kartuKeluarga = null;

    public string $rt_id = '';
    public string $nomor_kk = '';
    public string $kepala_keluarga = '';
    public string $alamat = '';

    public function mount(?KartuKeluarga $kartu_keluarga = null): void
    {
        if ($kartu_keluarga && $kartu_keluarga->exists) {
            $this->kartuKeluarga = $kartu_keluarga;
            $this->rt_id = $kartu_keluarga->rt_id;
            $this->nomor_kk = $kartu_keluarga->nomor_kk;
            $this->kepala_keluarga = $kartu_keluarga->kepala_keluarga;
            $this->alamat = $kartu_keluarga->alamat;
        }
    }

    #[Computed]
    public function rts()
    {
        return app(RtServiceInterface::class)->getAll();
    }

    public function save(KartuKeluargaServiceInterface $service)
    {
        $request = $this->kartuKeluarga ? new UpdateKartuKeluargaRequest() : new StoreKartuKeluargaRequest();
        
        $validated = $this->validate($request->rules());

        if ($this->kartuKeluarga) {
            $service->update($this->kartuKeluarga->id, $validated);
        } else {
            $service->create($validated);
        }

        return redirect()->route('population.kartu-keluarga.index');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl">{{ $kartuKeluarga ? __('Edit Kartu Keluarga') : __('Tambah Kartu Keluarga') }}</flux:heading>
        <flux:subheading>{{ __('Lengkapi informasi detail kartu keluarga di bawah ini.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Pilih RT') }}</flux:label>
                <flux:select wire:model="rt_id" placeholder="{{ __('Pilih RT...') }}">
                    @foreach ($this->rts as $rt)
                        <flux:select.option value="{{ $rt->id }}">
                            RT {{ $rt->nomor }} / RW {{ $rt->rw->nomor ?? '' }} - {{ $rt->rw->dusun->nama ?? '' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="rt_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Nomor KK') }}</flux:label>
                <flux:input wire:model="nomor_kk" placeholder="16 digit nomor KK" />
                <flux:error name="nomor_kk" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Nama Kepala Keluarga') }}</flux:label>
                <flux:input wire:model="kepala_keluarga" placeholder="Nama lengkap kepala keluarga" />
                <flux:error name="kepala_keluarga" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Alamat') }}</flux:label>
                <flux:textarea wire:model="alamat" placeholder="Alamat lengkap sesuai KK" />
                <flux:error name="alamat" />
            </flux:field>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan Data') }}
                </flux:button>
                <flux:button :href="route('population.kartu-keluarga.index')" variant="ghost">
                    {{ __('Batal') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</section>
