<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PublicService\Services\ApparatusService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function export(ApparatusService $service)
    {
        $filters = [];
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }

        $data = $service->export(filters: $filters, search: $this->search);

        return \App\Core\Support\Exporter::csv($data, [
            'nama' => 'Nama',
            'nip' => 'NIP',
            'jabatan' => 'Jabatan',
            'status' => 'Status',
        ], 'data-aparatur-' . now()->format('Y-m-d') . '.csv');
    }

    #[Computed]
    public function apparatus()
    {
        $filters = [];
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }

        return app(ApparatusService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 10
        );
    }

    public function delete(string $id): void
    {
        app(ApparatusService::class)->delete($id);
        $this->dispatch('notify', message: __('Data aparatur dihapus.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Aparatur Desa') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data perangkat dan staf desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('public-service.apparatus.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Tambah Aparatur') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="flex flex-1 gap-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nama/jabatan/NIP...') }}" icon="magnifying-glass" />
                </div>
                <div class="w-1/4">
                    <flux:select wire:model.live="statusFilter" placeholder="{{ __('Semua Status') }}">
                        <flux:select.option value="">{{ __('Semua Status') }}</flux:select.option>
                        <flux:select.option value="aktif">{{ __('Aktif') }}</flux:select.option>
                        <flux:select.option value="tidak_aktif">{{ __('Tidak Aktif') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->apparatus">
            <flux:table.columns>
                <flux:table.column>{{ __('Nama & NIP') }}</flux:table.column>
                <flux:table.column>{{ __('Jabatan') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->apparatus as $person)
                    <flux:table.row :key="$person->id">
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $person->nama }}</span>
                                <span class="text-xs text-zinc-400">NIP: {{ $person->nip ?? '-' }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $person->jabatan }}</flux:table.cell>

                        <flux:table.cell>
                            @if($person->status === 'aktif')
                                <flux:badge color="emerald" size="sm" inset="top bottom">{{ __('Aktif') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Tidak Aktif') }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('public-service.apparatus.edit', $person->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $person->id }}')" wire:confirm="{{ __('Yakin menghapus aparatur ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
