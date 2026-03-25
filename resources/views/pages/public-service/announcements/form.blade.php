<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\PublicService\Models\Announcement;
use Modules\PublicService\Services\AnnouncementService;
use Illuminate\Support\Str;

new class extends Component {
    public ?Announcement $announcement = null;
    
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public bool $is_published = true;

    public function mount(?Announcement $announcement = null): void
    {
        if ($announcement && $announcement->exists) {
            $this->announcement = $announcement;
            $this->title = $announcement->title;
            $this->slug = $announcement->slug;
            $this->content = $announcement->content;
            $this->is_published = $announcement->is_published;
        }
    }

    public function updatedTitle($value): void
    {
        if (!$this->announcement) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ];

        if ($this->announcement) {
            $rules['slug'] = 'required|string|max:255|unique:announcements,slug,' . $this->announcement->id;
        } else {
            $rules['slug'] = 'required|string|max:255|unique:announcements,slug';
        }

        $data = $this->validate($rules);
        $data['author_id'] = auth()->id();

        if ($this->announcement) {
            app(AnnouncementService::class)->update($this->announcement->id, $data);
            $message = __('Pengumuman diperbarui.');
        } else {
            app(AnnouncementService::class)->create($data);
            $message = __('Pengumuman diterbitkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('public-service.announcements.index'), navigate: true);
    }
}; ?>

    <div class="w-full">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
                {{ $announcement ? __('Edit Pengumuman') : __('Tulis Pengumuman Baru') }}
            </h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Bagikan informasi penting ke seluruh warga desa.') }}
            </p>
        </div>
        <flux:card>

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <flux:input wire:model.live="title" label="{{ __('Judul') }}" placeholder="e.g. Rapat Desa Bulan Ini" required />
                    <flux:input wire:model="slug" label="{{ __('Slug (Otomatis)') }}" placeholder="rapat-desa-bulan-ini" required />
                </div>
                
                <flux:textarea wire:model="content" label="{{ __('Konten Berita') }}" placeholder="{{ __('Tulis isi pengumuman/berita di sini...') }}" rows="10" required />
                
                <flux:switch wire:model="is_published" label="{{ __('Tandai sebagai Publik (Published)') }}" />

                <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                    <flux:button href="{{ route('public-service.announcements.index') }}" variant="ghost" wire:navigate>
                        {{ __('Batal') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Simpan Publikasi') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
