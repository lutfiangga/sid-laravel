<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PublicService\Services\AnnouncementService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function announcements()
    {
        return app(AnnouncementService::class)->getPaginated(
            search: $this->search,
            perPage: 10
        );
    }

    public function delete(string $id): void
    {
        app(AnnouncementService::class)->delete($id);
        $this->dispatch('notify', message: __('Pengumuman berhasil dihapus.'));
    }

    public function togglePublish(string $id, bool $currentStatus): void
    {
        app(AnnouncementService::class)->update($id, ['is_published' => !$currentStatus]);
        $this->dispatch('notify', message: __('Status publikasi diubah.'));
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Pengumuman & Berita') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Kelola artikel berita dan pengumuman warga.') }}</p>
                </div>
                <flux:button href="{{ route('public-service.announcements.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Publikasi Baru') }}
                </flux:button>
            </div>

            <div class="mb-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari judul...') }}" icon="magnifying-glass" />
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Judul') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Penulis') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Tanggal Publikasi') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status Publikasi') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->announcements as $item)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $item->title }}
                                </td>
                                <td class="px-6 py-4">{{ $item->author->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <button wire:click="togglePublish('{{ $item->id }}', {{ $item->is_published ? 'true' : 'false' }})" class="focus:outline-none focus:ring-2 focus:ring-zinc-500 rounded-sm">
                                        @if($item->is_published)
                                            <flux:badge color="emerald" size="sm" icon="check-circle">{{ __('Published') }}</flux:badge>
                                        @else
                                            <flux:badge color="zinc" size="sm" icon="eye-slash">{{ __('Draft') }}</flux:badge>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('public-service.announcements.edit', $item->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $item->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus publikasi ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Tidak ada pengumuman.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->announcements->links() }}
            </div>
        </flux:card>
    </div>
</div>
