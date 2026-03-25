<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\PendudukServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data Penduduk')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, PendudukServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('penduduk-deleted');
    }

    public function export(PendudukServiceInterface $service)
    {
        $data = $service->export(search: $this->search, with: ['kartuKeluarga']);

        return \App\Core\Support\Exporter::csv($data, [
            'nik' => 'NIK',
            'nama' => 'Nama Lengkap',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'status_perkawinan' => 'Status Perkawinan',
            'pekerjaan' => 'Pekerjaan',
            'status' => 'Status',
            'kartuKeluarga.nomor_kk' => 'No. KK',
        ], 'data-penduduk-' . now()->format('Y-m-d') . '.csv');
    }

    #[Computed]
    public function penduduks()
    {
        return app(PendudukServiceInterface::class)->getPaginated(10, search: $this->search, with: ['kartuKeluarga.rt.rw.dusun']);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data Penduduk') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data penduduk / warga desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.penduduk.create') }}" variant="primary" icon="plus">
            {{ __('Tambah Penduduk') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari NIK / Nama...') }}" icon="magnifying-glass" />
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->penduduks">
            <flux:table.columns>
                <flux:table.column>{{ __('NIK') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Lengkap') }}</flux:table.column>
                <flux:table.column>{{ __('No. KK') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Kelamin') }}</flux:table.column>
                <flux:table.column>{{ __('Wilayah') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->penduduks as $penduduk)
                    <flux:table.row :key="$penduduk->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $penduduk->nik }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $penduduk->nama }}</flux:table.cell>

                        <flux:table.cell>{{ $penduduk->kartuKeluarga->nomor_kk ?? '-' }}</flux:table.cell>

                        <flux:table.cell>{{ $penduduk->jenis_kelamin }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="text-xs">
                                RT {{ $penduduk->kartuKeluarga->rt->nomor ?? '' }} / RW {{ $penduduk->kartuKeluarga->rt->rw->nomor ?? '' }}<br>
                                {{ $penduduk->kartuKeluarga->rt->rw->dusun->nama ?? '' }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge variant="{{ $penduduk->status === 'Aktif' ? 'success' : 'neutral' }}" size="sm" inset="top bottom">
                                {{ $penduduk->status }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('population.penduduk.edit', $penduduk->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $penduduk->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus penduduk ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
