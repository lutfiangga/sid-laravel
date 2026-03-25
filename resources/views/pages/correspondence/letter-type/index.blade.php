<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterTypeService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';

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

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Kategori Surat') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Kelola template surat dan persyaratan dokumen.') }}</p>
                </div>
                <flux:button href="{{ route('correspondence.letter-type.create') }}" variant="primary" icon="plus">
                    {{ __('Tambah Kategori') }}
                </flux:button>
            </div>

            <div class="mb-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari kategori/kode...') }}" icon="magnifying-glass" />
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Kode') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Nama Kategori') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Persyaratan') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->letterTypes as $type)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $type->kode }}
                                </td>
                                <td class="px-6 py-4">{{ $type->nama }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($type->requirement_list)
                                        <ul class="list-disc pl-4">
                                            @foreach($type->requirement_list as $req)
                                                <li>{{ $req }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('correspondence.letter-type.edit', $type->id) }}" size="sm" variant="ghost" icon="pencil-square" />
                                        <flux:button wire:click="delete('{{ $type->id }}')" 
                                            wire:confirm="{{ __('Apakah Anda yakin ingin menghapus kategori ini?') }}"
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
                {{ $this->letterTypes->links() }}
            </div>
        </flux:card>
    </div>
</div>
