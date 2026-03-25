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

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Aparatur Desa') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Kelola data perangkat dan staf desa.') }}</p>
                </div>
                <flux:button href="{{ route('public-service.apparatus.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Tambah Aparatur') }}
                </flux:button>
            </div>

            <div class="mb-4 flex gap-4">
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

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Nama & NIP') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Jabatan') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->apparatus as $person)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $person->nama }}</div>
                                    <div class="text-xs text-zinc-400">{{ $person->nip ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $person->jabatan }}</td>
                                <td class="px-6 py-4">
                                    @if($person->status === 'aktif')
                                        <flux:badge color="emerald" size="sm">{{ __('Aktif') }}</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('Tidak Aktif') }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('public-service.apparatus.edit', $person->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $person->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus apparatur ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Tidak ada data ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->apparatus->links() }}
            </div>
        </flux:card>
    </div>
</div>
