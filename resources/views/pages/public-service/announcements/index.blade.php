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

    public function export(AnnouncementService $service)
    {
        $data = $service->export(search: $this->search, with: ['author']);

        return \App\Core\Support\Exporter::csv($data, [
            'created_at' => 'Tanggal',
            'title' => 'Judul',
            'author.name' => 'Penulis',
            'is_published' => 'Dipublikasikan (1/0)',
            'published_at' => 'Waktu Publikasi',
        ], 'data-pengumuman-' . now()->format('Y-m-d') . '.csv');
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

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Pengumuman & Berita') }}</flux:heading>
            <flux:subheading>{{ __('Kelola artikel berita dan pengumuman warga.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('public-service.announcements.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Publikasi Baru') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari judul...') }}" icon="magnifying-glass" />
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->announcements">
            <flux:table.columns>
                <flux:table.column>{{ __('Judul') }}</flux:table.column>
                <flux:table.column>{{ __('Penulis') }}</flux:table.column>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->announcements as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $item->title }}
                        </flux:table.cell>

                        <flux:table.cell>{{ $item->author->name ?? '-' }}</flux:table.cell>

                        <flux:table.cell class="text-xs">{{ $item->created_at->format('d M Y') }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:button wire:click="togglePublish('{{ $item->id }}', {{ $item->is_published ? 'true' : 'false' }})" variant="ghost" size="sm" inset="top bottom">
                                @if($item->is_published)
                                    <flux:badge color="emerald" size="sm" icon="check-circle">{{ __('Published') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="eye-slash">{{ __('Draft') }}</flux:badge>
                                @endif
                            </flux:button>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('public-service.announcements.edit', $item->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $item->id }}')" wire:confirm="{{ __('Yakin menghapus publikasi ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
