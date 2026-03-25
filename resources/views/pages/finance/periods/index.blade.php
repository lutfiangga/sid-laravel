<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Finance\Services\FinancePeriodService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    
    public function export(FinancePeriodService $service)
    {
        $data = $service->export(search: $this->search);
        
        if ($data->isEmpty()) {
            $this->dispatch('notify', message: __('Tidak ada data untuk diekspor.'));
            return;
        }

        $firstItem = collect($data->first()->toArray())->except(['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'password', 'remember_token'])->toArray();
        $columns = [];
        foreach (array_keys($firstItem) as $key) {
            if (!is_array($firstItem[$key])) {
                $columns[$key] = ucwords(str_replace('_', ' ', $key));
            }
        }

        return \App\Core\Support\Exporter::csv($data, $columns, 'export-' . now()->format('Y-m-d') . '.csv');
    }


    #[Computed]
    public function periods()
    {
        return app(FinancePeriodService::class)->getPaginated(
            search: $this->search,
            perPage: 10
        );
    }

    public function delete(string $id): void
    {
        app(FinancePeriodService::class)->delete($id);
        $this->dispatch('notify', message: __('Tahun anggaran berhasil dihapus.'));
    }

    public function toggleActive(string $id): void
    {
        app(FinancePeriodService::class)->update($id, ['is_active' => true]);
        $this->dispatch('notify', message: __('Tahun anggaran diaktifkan. Tahun lainnya otomatis dinonaktifkan.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Tahun Anggaran') }}</flux:heading>
            <flux:subheading>{{ __('Kelola periode fiskal APBDes.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('finance.periods.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Tambah Periode') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari tahun/deskripsi...') }}" icon="magnifying-glass" />
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->periods">
            <flux:table.columns>
                <flux:table.column>{{ __('Tahun') }}</flux:table.column>
                <flux:table.column>{{ __('Deskripsi') }}</flux:table.column>
                <flux:table.column>{{ __('Status Aktif') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->periods as $period)
                    <flux:table.row :key="$period->id">
                        <flux:table.cell class="font-bold text-zinc-900 dark:text-white">
                            {{ $period->year }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $period->description ?? '-' }}</flux:table.cell>

                        <flux:table.cell>
                            @if($period->is_active)
                                <flux:badge color="emerald" size="sm" icon="check-circle">{{ __('Aktif (Berjalan)') }}</flux:badge>
                            @else
                                <button wire:click="toggleActive('{{ $period->id }}')" class="hover:opacity-75">
                                    <flux:badge color="zinc" size="sm">{{ __('Set Aktif') }}</flux:badge>
                                </button>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('finance.periods.edit', $period->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $period->id }}')" wire:confirm="{{ __('Yakin menghapus periode ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
