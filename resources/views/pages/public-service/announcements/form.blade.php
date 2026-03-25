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
        <flux:heading size="xl">{{ $announcement ? __('Edit Pengumuman') : __('Tulis Pengumuman Baru') }}</flux:heading>
        <flux:subheading>{{ __('Bagikan informasi penting ke seluruh warga desa.') }}</flux:subheading>
    </div>
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <flux:input wire:model.blur="title" label="{{ __('Judul') }}" placeholder="e.g. Rapat Desa Bulan Ini" required />
                <flux:input wire:model="slug" label="{{ __('Slug (Otomatis)') }}" placeholder="rapat-desa-bulan-ini" required />
            </div>

            {{-- Quill.js WYSIWYG Editor --}}
            <div wire:ignore>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ __('Konten Berita') }} <span class="text-red-500">*</span>
                </label>

                {{-- Hidden input that Livewire reads --}}
                <input type="hidden" wire:model="content" id="quill-content" />

                {{-- Quill toolbar + editor container --}}
                <div class="overflow-hidden rounded-lg border border-zinc-300 dark:border-zinc-600">
                    <div id="quill-toolbar">
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option value="1">Heading 1</option>
                                <option value="2">Heading 2</option>
                                <option value="3">Heading 3</option>
                                <option selected>Normal</option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>
                    <div id="quill-editor"
                         class="min-h-[350px] bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                         style="font-size: 15px;"
                    ></div>
                </div>
            </div>

            <flux:switch wire:model="is_published" label="{{ __('Tandai sebagai Publik (Published)') }}" />

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('public-service.announcements.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary" id="announce-submit-btn">
                    {{ __('Simpan Publikasi') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Quill.js from CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
    document.addEventListener('livewire:navigated', () => initQuill());
    document.addEventListener('DOMContentLoaded', () => initQuill());

    function initQuill() {
        const editorEl = document.getElementById('quill-editor');
        const hiddenInput = document.getElementById('quill-content');

        if (!editorEl || editorEl.dataset.quillReady) return;
        editorEl.dataset.quillReady = '1';

        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: '#quill-toolbar',
                    handlers: {
                        image: imageUploadHandler,
                    }
                }
            },
            placeholder: 'Tulis konten berita di sini...',
        });

        // Pre-fill content for edit mode
        const initial = hiddenInput.value;
        if (initial) {
            quill.clipboard.dangerouslyPasteHTML(initial);
        }

        // Sync to hidden input on change (Livewire reads this)
        quill.on('text-change', () => {
            const html = quill.getSemanticHTML();
            hiddenInput.value = html;
            hiddenInput.dispatchEvent(new Event('input'));
        });

        function imageUploadHandler() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch('{{ route('public-service.announcements.upload-image') }}', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();
                if (data.url) {
                    const range = quill.getSelection();
                    quill.insertEmbed(range ? range.index : 0, 'image', data.url);
                }
            };
        }
    }
    </script>

    <style>
    #quill-editor {
        border-top: 1px solid #e4e4e7;
    }
    .dark #quill-editor {
        background-color: rgb(24 24 27);
        color: white;
    }
    .dark #quill-toolbar {
        background-color: rgb(39 39 42);
        color: white;
        border-bottom: 1px solid rgb(63 63 70);
    }
    .dark .ql-stroke {
        stroke: #d1d5db !important;
    }
    .dark .ql-fill {
        fill: #d1d5db !important;
    }
    .dark .ql-picker-label,
    .dark .ql-picker-item {
        color: #d1d5db !important;
    }
    </style>
</div>
