<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data Kartu Keluarga')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, KartuKeluargaServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('kk-deleted');
    }

    public function export(KartuKeluargaServiceInterface $service)
    {
        $data = $service->export(search: $this->search, with: ['rt.rw.dusun']);

        return \App\Core\Support\Exporter::csv($data, [
            'nomor_kk' => 'Nomor KK',
            'kepala_keluarga' => 'Kepala Keluarga',
            'alamat' => 'Alamat',
            'rt.nomor' => 'RT',
            'rt.rw.nomor' => 'RW',
            'rt.rw.dusun.nama' => 'Dusun',
        ], 'data-kk-' . now()->format('Y-m-d') . '.csv');
    }

    #[Computed]
    public function kartuKeluargas()
    {
        return app(KartuKeluargaServiceInterface::class)->getPaginated(10, search: $this->search, with: ['rt.rw.dusun']);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data Kartu Keluarga') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data kartu keluarga desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.kartu-keluarga.create') }}" variant="primary" icon="plus">
            {{ __('Tambah KK') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari No. KK / Kepala Keluarga...') }}" icon="magnifying-glass" />
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->kartuKeluargas">
            <flux:table.columns>
                <flux:table.column>{{ __('Nomor KK') }}</flux:table.column>
                <flux:table.column>{{ __('Kepala Keluarga') }}</flux:table.column>
                <flux:table.column>{{ __('Alamat') }}</flux:table.column>
                <flux:table.column>{{ __('Wilayah') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->kartuKeluargas as $kk)
                    <flux:table.row :key="$kk->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $kk->nomor_kk }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $kk->kepala_keluarga }}</flux:table.cell>

                        <flux:table.cell class="max-w-xs truncate">{{ $kk->alamat }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="text-xs">
                                RT {{ $kk->rt->nomor ?? '' }} / RW {{ $kk->rt->rw->nomor ?? '' }}<br>
                                {{ $kk->rt->rw->dusun->nama ?? '' }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('population.kartu-keluarga.edit', $kk->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $kk->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus KK ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
