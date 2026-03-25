<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\LetterTemplate\Models\LetterTemplate;
use Modules\LetterTemplate\Services\LetterTemplateService;
use Illuminate\Support\Str;

new class extends Component {
    public ?LetterTemplate $letterTemplate = null;

    public string $nama = '';
    public string $kode = '';
    public string $content = '';
    public array $placeholders = ['{nama}', '{nik}', '{tempat_lahir}', '{tanggal_lahir}', '{jenis_kelamin}', '{pekerjaan}', '{agama}', '{status_perkawinan}', '{alamat}', '{nomor_surat}', '{tanggal_sekarang}', '{keperluan}', '{nama_rt}', '{nama_rw}', '{nama_dusun}', '{nama_kades}'];
    public int $margin_top = 4;
    public int $margin_bottom = 4;
    public int $margin_left = 6;
    public int $margin_right = 6;
    public string $orientation = 'portrait';

    public function mount(?LetterTemplate $letterTemplate = null): void
    {
        if ($letterTemplate && $letterTemplate->exists) {
            $this->letterTemplate = $letterTemplate;
            $this->nama = $letterTemplate->nama;
            $this->kode = $letterTemplate->kode;
            $this->content = $letterTemplate->content;
            $this->margin_top = $letterTemplate->margin_top;
            $this->margin_bottom = $letterTemplate->margin_bottom;
            $this->margin_left = $letterTemplate->margin_left;
            $this->margin_right = $letterTemplate->margin_right;
            $this->orientation = $letterTemplate->orientation;
        }
    }

    public function updatedNama($value): void
    {
        if (!$this->letterTemplate) {
            $this->kode = 'TPL-' . Str::upper(Str::snake($value, '-'));
        }
    }

    public function save(): void
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:letter_templates,kode' . ($this->letterTemplate ? ',' . $this->letterTemplate->id : ''),
            'content' => 'required|string',
            'margin_top' => 'required|integer|min:0',
            'margin_bottom' => 'required|integer|min:0',
            'margin_left' => 'required|integer|min:0',
            'margin_right' => 'required|integer|min:0',
            'orientation' => 'required|string|in:portrait,landscape',
        ];

        $data = $this->validate($rules);
        $data['placeholders'] = $this->placeholders;

        if ($this->letterTemplate) {
            app(LetterTemplateService::class)->update($this->letterTemplate->id, $data);
            $message = __('Templat surat diperbarui.');
        } else {
            app(LetterTemplateService::class)->create($data);
            $message = __('Templat surat dibuat.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('letter-template.index'), navigate: true);
    }
}; ?>

<div class="w-full" x-data="templateEditor()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $letterTemplate ? __('Edit Templat') : __('Buat Templat Baru') }}</flux:heading>
            <flux:subheading>{{ __('Desain tata letak dokumen dengan editor Word-style.') }}</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        {{-- Settings Panel --}}
        <div class="space-y-6 lg:col-span-1">
            <flux:card>
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model.blur="nama" label="{{ __('Nama Templat') }}" placeholder="e.g. Surat Keterangan Pindah" required />
                    <flux:input wire:model="kode" label="{{ __('Kode Unique') }}" placeholder="TPL-SK-PINDAH" required />

                    <div class="grid grid-cols-2 gap-2 border-t pt-4 dark:border-zinc-700">
                        <flux:input type="number" wire:model.live="margin_top" label="{{ __('Margin Atas (mm)') }}" min="0" />
                        <flux:input type="number" wire:model.live="margin_bottom" label="{{ __('Margin Bawah (mm)') }}" min="0" />
                        <flux:input type="number" wire:model.live="margin_left" label="{{ __('Margin Kiri (mm)') }}" min="0" />
                        <flux:input type="number" wire:model.live="margin_right" label="{{ __('Margin Kanan (mm)') }}" min="0" />
                    </div>

                    <flux:select wire:model.live="orientation" label="{{ __('Orientasi') }}">
                        <flux:select.option value="portrait">{{ __('Portrait') }}</flux:select.option>
                        <flux:select.option value="landscape">{{ __('Landscape') }}</flux:select.option>
                    </flux:select>

                    <div class="border-t pt-4 dark:border-zinc-700">
                        <flux:heading size="sm" class="mb-2">{{ __('Placeholders') }}</flux:heading>
                        <p class="mb-3 text-xs text-zinc-500">{{ __('Klik untuk menyalin ke editor:') }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($placeholders as $ph)
                                <button type="button" 
                                    @click="insertPlaceholder('{{ $ph }}')"
                                    class="rounded bg-zinc-100 px-2 py-1 text-[10px] font-mono text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                    {{ $ph }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 border-t pt-4 dark:border-zinc-700">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Simpan Templat') }}
                        </flux:button>
                        <flux:button href="{{ route('letter-template.index') }}" variant="ghost" class="w-full" wire:navigate>
                            {{ __('Batal') }}
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        </div>

        {{-- Editor Panel --}}
        <div class="lg:col-span-3">
            <flux:card class="p-0 overflow-hidden">
                <div wire:ignore>
                    <textarea id="template-editor">{{ $content }}</textarea>
                </div>
            </flux:card>
        </div>
    </div>

    {{-- TinyMCE --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.2.1/tinymce.min.js"></script>
    <script>
        function templateEditor() {
            return {
                init() {
                    this.initTinyMCE();
                    
                    document.addEventListener('livewire:navigated', () => {
                        this.initTinyMCE();
                    });
                },
                initTinyMCE() {
                    if (tinymce.get('template-editor')) {
                        tinymce.get('template-editor').remove();
                    }

                    const isDark = document.documentElement.classList.contains('dark');

                    tinymce.init({
                        selector: '#template-editor',
                        height: 850,
                        license_key: 'gpl',
                        skin: isDark ? 'oxide-dark' : 'oxide',
                        content_css: isDark ? 'dark' : 'default',
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                        font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace; Akurat=akurat,sans-serif; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif;',
                        content_style: `
                            html {
                                background: ${isDark ? '#18181b' : '#f4f4f5'};
                                padding: 20px 0;
                            }
                            body {
                                background: ${isDark ? '#27272a' : 'white'};
                                color: ${isDark ? '#f4f4f5' : '#000'};
                                width: {{ $orientation === 'portrait' ? '210mm' : '297mm' }};
                                min-height: {{ $orientation === 'portrait' ? '297mm' : '210mm' }};
                                padding: {{ $margin_top }}mm {{ $margin_right }}mm {{ $margin_bottom }}mm {{ $margin_left }}mm;
                                margin: 0 auto;
                                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                                box-sizing: border-box;
                                text-align: left;
                            }
                        `,
                        setup: (editor) => {
                            editor.on('change', () => {
                                @this.set('content', editor.getContent());
                            });
                        }
                    });
                },
                insertPlaceholder(placeholder) {
                    const editor = tinymce.get('template-editor');
                    if (editor) {
                        editor.insertContent(placeholder);
                    }
                }
            }
        }
    </script>

    <style>
        .tox-tinymce {
            border: none !important;
        }
    </style>
</div>
