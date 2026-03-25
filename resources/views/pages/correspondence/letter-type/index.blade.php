<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterTypeService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';

    
    public function export(LetterTypeService $service)
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
    public function letterTypes()
    {
        return app(LetterTypeService::class)->getPaginated(
            search: $this->search,
            perPage: 10
        );
    }

    public function delete(string $id): void
    {
        app(LetterTypeService::class)->delete($id);
        $this->dispatch('notify', message: __('Kategori surat berhasil dihapus.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Kategori Surat') }}</flux:heading>
            <flux:subheading>{{ __('Kelola template surat dan persyaratan dokumen.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('correspondence.letter-type.create') }}" variant="primary" icon="plus">
            {{ __('Tambah Kategori') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari kategori/kode...') }}" icon="magnifying-glass" />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        
        </div>

        <flux:table :paginate="$this->letterTypes">
            <flux:table.columns>
                <flux:table.column>{{ __('Kode') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Kategori') }}</flux:table.column>
                <flux:table.column>{{ __('Persyaratan') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->letterTypes as $type)
                    <flux:table.row :key="$type->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $type->kode }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $type->nama }}</flux:table.cell>

                        <flux:table.cell>
                            @if($type->requirement_list)
                                <ul class="list-disc pl-4 text-xs">
                                    @foreach($type->requirement_list as $req)
                                        <li>{{ $req }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('correspondence.letter-type.edit', $type->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $type->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus kategori ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
