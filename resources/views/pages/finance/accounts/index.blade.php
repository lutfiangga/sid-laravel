<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Finance\Services\FinanceAccountService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    
    public function export(FinanceAccountService $service)
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
    public function accounts()
    {
        $filters = [];
        if ($this->typeFilter) {
            $filters['type'] = $this->typeFilter;
        }

        return app(FinanceAccountService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 15
        );
    }

    public function delete(string $id): void
    {
        app(FinanceAccountService::class)->delete($id);
        $this->dispatch('notify', message: __('Mata anggaran dihapus.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Bagan Akun (Kode Rekening)') }}</flux:heading>
            <flux:subheading>{{ __('Daftar mata anggaran standar APBDes.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('finance.accounts.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Tambah Akun') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex gap-4">
            <div class="w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari kode/nama akun...') }}" icon="magnifying-glass" />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        
            <div class="w-1/4">
                <flux:select wire:model.live="typeFilter" placeholder="{{ __('Semua Tipe Akun') }}">
                    <flux:select.option value="">{{ __('Semua Tipe Akun') }}</flux:select.option>
                    <flux:select.option value="pemasukan">{{ __('Pemasukan (Pendapatan)') }}</flux:select.option>
                    <flux:select.option value="pengeluaran">{{ __('Pengeluaran (Belanja)') }}</flux:select.option>
                    <flux:select.option value="pembiayaan">{{ __('Pembiayaan') }}</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$this->accounts">
            <flux:table.columns>
                <flux:table.column>{{ __('Kode Rekening') }}</flux:table.column>
                <flux:table.column>{{ __('Uraian / Nama Akun') }}</flux:table.column>
                <flux:table.column>{{ __('Tipe Akun') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->accounts as $account)
                    <flux:table.row :key="$account->id">
                        <flux:table.cell class="font-mono text-zinc-900 dark:text-white">
                            {{ $account->code }}
                        </flux:table.cell>

                        <flux:table.cell class="font-medium">{{ $account->name }}</flux:table.cell>

                        <flux:table.cell>
                            @php
                                $typeColors = [
                                    'pemasukan' => 'emerald',
                                    'pengeluaran' => 'amber',
                                    'pembiayaan' => 'blue',
                                ];
                                $color = $typeColors[$account->type] ?? 'zinc';
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm" class="uppercase" inset="top bottom">
                                {{ $account->type }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="{{ $account->is_active ? 'emerald' : 'zinc' }}" size="sm" inset="top bottom">
                                {{ $account->is_active ? __('Aktif') : __('Non-Aktif') }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('finance.accounts.edit', $account->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $account->id }}')" wire:confirm="{{ __('Yakin menghapus akun ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
