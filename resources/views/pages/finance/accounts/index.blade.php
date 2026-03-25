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

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Bagan Akun (Kode Rekening)') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Daftar mata anggaran standar APBDes.') }}</p>
                </div>
                <flux:button href="{{ route('finance.accounts.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Tambah Akun') }}
                </flux:button>
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari kode/nama akun...') }}" icon="magnifying-glass" />
                </div>
                <div class="w-1/4">
                    <flux:select wire:model.live="typeFilter" placeholder="{{ __('Semua Tipe Akun') }}">
                        <flux:select.option value="">{{ __('Semua Tipe Akun') }}</flux:select.option>
                        <flux:select.option value="pemasukan">{{ __('Pemasukan (Pendapatan)') }}</flux:select.option>
                        <flux:select.option value="pengeluaran">{{ __('Pengeluaran (Belanja)') }}</flux:select.option>
                        <flux:select.option value="pembiayaan">{{ __('Pembiayaan') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Kode Rekening') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Uraian / Nama Akun') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Tipe Akun') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->accounts as $account)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 font-mono text-zinc-900 dark:text-white">
                                    {{ $account->code }}
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $account->name }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeColors = [
                                            'pemasukan' => 'emerald',
                                            'pengeluaran' => 'amber',
                                            'pembiayaan' => 'blue',
                                        ];
                                        $color = $typeColors[$account->type] ?? 'zinc';
                                    @endphp
                                    <flux:badge color="{{ $color }}" size="sm" class="uppercase">
                                        {{ $account->type }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge color="{{ $account->is_active ? 'emerald' : 'zinc' }}" size="sm">
                                        {{ $account->is_active ? __('Aktif') : __('Non-Aktif') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('finance.accounts.edit', $account->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $account->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus akun ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Data tidak ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->accounts->links() }}
            </div>
        </flux:card>
    </div>
</div>
