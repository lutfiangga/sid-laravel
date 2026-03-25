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

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Tahun Anggaran') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Kelola periode fiskal APBDes.') }}</p>
                </div>
                <flux:button href="{{ route('finance.periods.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Tambah Periode') }}
                </flux:button>
            </div>

            <div class="mb-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari tahun/deskripsi...') }}" icon="magnifying-glass" />
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Tahun') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Deskripsi') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status Aktif') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->periods as $period)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white">
                                    {{ $period->year }}
                                </td>
                                <td class="px-6 py-4">{{ $period->description ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($period->is_active)
                                        <flux:badge color="emerald" size="sm" icon="check-circle">{{ __('Aktif (Berjalan)') }}</flux:badge>
                                    @else
                                        <button wire:click="toggleActive('{{ $period->id }}')" class="hover:opacity-75">
                                            <flux:badge color="zinc" size="sm">{{ __('Set Aktif') }}</flux:badge>
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('finance.periods.edit', $period->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $period->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus periode ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Data tidak ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->periods->links() }}
            </div>
        </flux:card>
    </div>
</div>
