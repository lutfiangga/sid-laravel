<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\RtServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data RT')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, RtServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('rt-deleted');
    }

    #[Computed]
    public function rts()
    {
        return app(RtServiceInterface::class)->getPaginated(10, search: $this->search, with: ['rw.dusun']);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data RT') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data wilayah tingkat RT.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.rt.create') }}" variant="primary" icon="plus">
            {{ __('Tambah RT') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari RT...') }}" icon="magnifying-glass" />
            </div>
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('Nomor RT') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('RW') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Dusun') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Ketua RT') }}</th>
                        <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->rts as $rt)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $rt->nomor }}
                            </td>
                            <td class="px-6 py-4">{{ $rt->rw->nomor ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $rt->rw->dusun->nama ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $rt->ketua ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" href="{{ route('population.rt.edit', $rt->id) }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete('{{ $rt->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus RT ini?') }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data RT ditemukan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->rts->links() }}
        </div>
    </flux:card>
</section>
