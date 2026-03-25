<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\DusunServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data Dusun')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, DusunServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('dusun-deleted');
    }

    
    public function export(DusunServiceInterface $service)
    {
        $data = $service->export(search: $this->search);
        
        if ($data->isEmpty()) {
            $this->dispatch('notify', message: __('Tidak ada data untuk diekspor.'));
            return;
        }

        // Extract column names dynamically from the first item's array representation
        $firstItem = collect($data->first()->toArray())->except(['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'])->toArray();
        $columns = [];
        foreach (array_keys($firstItem) as $key) {
            if (!is_array($firstItem[$key])) {
                $columns[$key] = ucwords(str_replace('_', ' ', $key));
            }
        }

        return \App\Core\Support\Exporter::csv($data, $columns, 'export-' . now()->format('Y-m-d') . '.csv');
    }


    #[Computed]
    public function dusuns()
    {
        return app(DusunServiceInterface::class)->getPaginated(10, search: $this->search);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data Dusun') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data wilayah tingkat dusun.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.dusun.create') }}" variant="primary" icon="plus">
            {{ __('Tambah Dusun') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari dusun...') }}" icon="magnifying-glass" />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        
        </div>

        <flux:table :paginate="$this->dusuns">
            <flux:table.columns>
                <flux:table.column>{{ __('Kode') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Dusun') }}</flux:table.column>
                <flux:table.column>{{ __('Ketua Dusun') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->dusuns as $dusun)
                    <flux:table.row :key="$dusun->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $dusun->kode }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $dusun->nama }}</flux:table.cell>

                        <flux:table.cell>{{ $dusun->ketua ?? '-' }}</flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('population.dusun.edit', $dusun->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $dusun->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus dusun ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
