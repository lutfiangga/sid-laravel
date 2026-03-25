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
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('Kode') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Nama Dusun') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Ketua Dusun') }}</th>
                        <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->dusuns as $dusun)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $dusun->kode }}
                            </td>
                            <td class="px-6 py-4">{{ $dusun->nama }}</td>
                            <td class="px-6 py-4">{{ $dusun->ketua ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" href="{{ route('population.dusun.edit', $dusun->id) }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete('{{ $dusun->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus dusun ini?') }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data dusun ditemukan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->dusuns->links() }}
        </div>
    </flux:card>
</section>
