<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\PendudukServiceInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Data Penduduk')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function delete(string $id, PendudukServiceInterface $service): void
    {
        $service->delete($id);
        $this->dispatch('penduduk-deleted');
    }

    #[Computed]
    public function penduduks()
    {
        return app(PendudukServiceInterface::class)->getPaginated(10, search: $this->search, with: ['kartuKeluarga.rt.rw.dusun']);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Data Penduduk') }}</flux:heading>
            <flux:subheading>{{ __('Kelola data penduduk / warga desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('population.penduduk.create') }}" variant="primary" icon="plus">
            {{ __('Tambah Penduduk') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari NIK / Nama...') }}" icon="magnifying-glass" />
            </div>
        </div>

        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('NIK') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Nama Lengkap') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('No. KK') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Jenis Kelamin') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Wilayah') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                        <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->penduduks as $penduduk)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $penduduk->nik }}
                            </td>
                            <td class="px-6 py-4">{{ $penduduk->nama }}</td>
                            <td class="px-6 py-4">{{ $penduduk->kartuKeluarga->nomor_kk ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $penduduk->jenis_kelamin }}</td>
                            <td class="px-6 py-4 text-xs">
                                RT {{ $penduduk->kartuKeluarga->rt->nomor ?? '' }} / RW {{ $penduduk->kartuKeluarga->rt->rw->nomor ?? '' }}<br>
                                {{ $penduduk->kartuKeluarga->rt->rw->dusun->nama ?? '' }}
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge variant="{{ $penduduk->status === 'Aktif' ? 'success' : 'neutral' }}" size="sm">
                                    {{ $penduduk->status }}
                                </flux:badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" href="{{ route('population.penduduk.edit', $penduduk->id) }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete('{{ $penduduk->id }}')" wire:confirm="{{ __('Apakah Anda yakin ingin menghapus penduduk ini?') }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data penduduk ditemukan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->penduduks->links() }}
        </div>
    </flux:card>
</section>
