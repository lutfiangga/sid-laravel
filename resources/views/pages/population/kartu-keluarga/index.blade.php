<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data Kartu Keluarga')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, KartuKeluargaServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('kk-deleted');
    }

    #[Computed]
    public function kartuKeluargas()
    {
        return app(KartuKeluargaServiceInterface::class)->getPaginated(10, search: $this->search, with: ['rt.rw.dusun']);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data Kartu Keluarga') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data kartu keluarga desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.kartu-keluarga.create') }}" variant="primary" icon="plus">
            {{ __('Tambah KK') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari No. KK / Kepala Keluarga...') }}" icon="magnifying-glass" />
            </div>
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('Nomor KK') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Kepala Keluarga') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Alamat') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Wilayah') }}</th>
                        <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->kartuKeluargas as $kk)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $kk->nomor_kk }}
                            </td>
                            <td class="px-6 py-4">{{ $kk->kepala_keluarga }}</td>
                            <td class="px-6 py-4 max-w-xs truncate">{{ $kk->alamat }}</td>
                            <td class="px-6 py-4 text-xs">
                                RT {{ $kk->rt->nomor ?? '' }} / RW {{ $kk->rt->rw->nomor ?? '' }}<br>
                                {{ $kk->rt->rw->dusun->nama ?? '' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" href="{{ route('population.kartu-keluarga.edit', $kk->id) }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete('{{ $kk->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus KK ini?') }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data kartu keluarga ditemukan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->kartuKeluargas->links() }}
        </div>
    </flux:card>
</section>
