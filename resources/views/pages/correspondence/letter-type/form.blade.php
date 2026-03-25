<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Services\LetterTypeService;
use Modules\Correspondence\Http\Requests\StoreLetterTypeRequest;

new class extends Component {
    public ?LetterType $letterType = null;
    public string $nama = '';
    public string $kode = '';
    public string $template = '';
    public ?string $letter_template_id = null;
    public array $requirement_list = [];
    public array $approval_levels = [];
    public array $templates = [];

    // For adding new requirement with type
    public string $new_requirement_label = '';
    public string $new_requirement_type = 'text'; // text | file | link

    public function mount(?LetterType $letterType = null): void
    {
        $this->templates = \Modules\LetterTemplate\Models\LetterTemplate::all()->toArray();

        if ($letterType && $letterType->exists) {
            $this->letterType = $letterType;
            $this->nama = $letterType->nama;
            $this->kode = $letterType->kode;
            $this->template = $letterType->template;
            $this->letter_template_id = $letterType->letter_template_id;
            $this->requirement_list = $letterType->requirement_list ?? [];
            $this->approval_levels = $letterType->approval_levels ?? [];
        }
    }

    public function updatedLetterTemplateId($value): void
    {
        if (!$value) {
            // Give Livewire time to render the textarea before re-init TinyMCE
            $this->dispatch('manual-editor-visible');
        }
    }

    public function addRequirement(): void
    {
        $label = trim($this->new_requirement_label);
        if ($label) {
            $this->requirement_list[] = [
                'label' => $label,
                'type' => $this->new_requirement_type,
            ];
            $this->new_requirement_label = '';
            $this->new_requirement_type = 'text';
        }
    }

    public function removeRequirement(int $index): void
    {
        unset($this->requirement_list[$index]);
        $this->requirement_list = array_values($this->requirement_list);
    }

    public function toggleApprovalLevel(string $level): void
    {
        if (in_array($level, $this->approval_levels)) {
            $this->approval_levels = array_values(array_filter(
                $this->approval_levels,
                fn ($l) => $l !== $level
            ));
        } else {
            $this->approval_levels[] = $level;
        }
    }

    public function save(): void
    {
        $data = [
            'nama' => $this->nama,
            'kode' => $this->kode,
            'template' => $this->template,
            'letter_template_id' => $this->letter_template_id,
            'requirement_list' => $this->requirement_list,
            'approval_levels' => $this->approval_levels,
        ];

        $rules = (new StoreLetterTypeRequest())->rules();
        if ($this->letterType) {
            $rules['kode'] = ['required', 'string', 'unique:letter_types,kode,' . $this->letterType->id . ',id'];
        }
        $this->validate($rules);

        if ($this->letterType) {
            app(LetterTypeService::class)->update($this->letterType->id, $data);
            $message = __('Kategori surat berhasil diperbarui.');
        } else {
            app(LetterTypeService::class)->create($data);
            $message = __('Kategori surat berhasil ditambahkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('correspondence.letter-type.index'), navigate: true);
    }
}; ?>

<div class="w-full" x-data="letterTypeEditor()">
    <div class="mb-6">
        <flux:heading size="xl">{{ $letterType ? __('Edit Kategori Surat') : __('Tambah Kategori Surat') }}</flux:heading>
        <flux:subheading>{{ __('Isi detail kategori, persyaratan berkas, alur persetujuan, dan template isi surat.') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Basic Info --}}
        <flux:card>
            <flux:heading size="sm" class="mb-4">{{ __('Informasi Dasar') }}</flux:heading>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <flux:input wire:model="nama" label="{{ __('Nama Kategori') }}" placeholder="e.g. Surat Keterangan Usaha" required />
                <flux:input wire:model="kode" label="{{ __('Kode Surat') }}" placeholder="e.g. SKU" required />
            </div>
        </flux:card>

        {{-- Approval Levels --}}
        <flux:card>
            <flux:heading size="sm" class="mb-2">{{ __('Tingkat Persetujuan') }}</flux:heading>
            <flux:subheading class="mb-4">{{ __('Pilih tingkat yang harus menyetujui sebelum surat diterbitkan (berurutan dari atas).') }}</flux:subheading>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach(['rt' => 'RT', 'rw' => 'RW', 'lurah' => 'Lurah/Sekdes', 'admin' => 'Admin'] as $level => $label)
                    <button type="button" wire:click="toggleApprovalLevel('{{ $level }}')"
                        class="flex items-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium transition-colors
                            {{ in_array($level, $approval_levels)
                                ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                : 'border-zinc-200 text-zinc-600 hover:border-zinc-400 dark:border-zinc-700 dark:text-zinc-400' }}">
                        <flux:icon.check-circle class="size-4 {{ in_array($level, $approval_levels) ? 'text-emerald-500' : 'text-zinc-300' }}" />
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if(count($approval_levels) > 0)
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-zinc-500">{{ __('Alur:') }}</span>
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Pemohon → {{ implode(' → ', array_map('strtoupper', $approval_levels)) }} → Selesai
                    </span>
                </div>
            @endif
        </flux:card>

        {{-- Requirements --}}
        <flux:card>
            <flux:heading size="sm" class="mb-2">{{ __('Persyaratan Dokumen') }}</flux:heading>
            <flux:subheading class="mb-4">{{ __('Tentukan dokumen yang harus dilampirkan oleh pemohon.') }}</flux:subheading>

            <div class="flex gap-2">
                <div class="flex-1">
                    <flux:input wire:model="new_requirement_label"
                        placeholder="{{ __('Nama persyaratan, e.g. Fotokopi KTP') }}"
                        wire:keydown.enter.prevent="addRequirement" />
                </div>
                <flux:select wire:model="new_requirement_type" class="w-36">
                    <flux:select.option value="text">Teks/Isian</flux:select.option>
                    <flux:select.option value="file">Upload File</flux:select.option>
                    <flux:select.option value="link">Link Drive</flux:select.option>
                </flux:select>
                <flux:button wire:click.prevent="addRequirement" variant="subtle" icon="plus" />
            </div>

            @if(count($requirement_list) > 0)
                <div class="mt-4 space-y-2">
                    @foreach($requirement_list as $index => $req)
                        @php
                            $reqLabel = is_array($req) ? ($req['label'] ?? $req) : $req;
                            $reqType = is_array($req) ? ($req['type'] ?? 'text') : 'text';
                            $typeColor = ['file' => 'blue', 'link' => 'amber', 'text' => 'zinc'][$reqType] ?? 'zinc';
                            $typeLabel = ['file' => 'File', 'link' => 'Link', 'text' => 'Teks'][$reqType] ?? 'Teks';
                        @endphp
                        <div class="flex items-center justify-between rounded-lg border border-zinc-100 bg-zinc-50 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                            <div class="flex items-center gap-2">
                                <flux:badge :color="$typeColor" size="sm">{{ $typeLabel }}</flux:badge>
                                <span class="text-sm font-medium">{{ $reqLabel }}</span>
                            </div>
                            <button type="button" wire:click="removeRequirement({{ $index }})" class="text-zinc-400 hover:text-red-500">
                                <flux:icon.x-mark class="size-4" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        {{-- Template Surat --}}
        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <flux:heading size="sm" class="mb-1">{{ __('Desain Template Surat') }}</flux:heading>
                    <flux:subheading>{{ __('Pilih templat profesional yang sudah dibuat di modul Templat Surat.') }}</flux:subheading>
                </div>
                <flux:button href="{{ route('letter-template.index') }}" size="sm" variant="ghost" icon="document-text" wire:navigate>
                    {{ __('Kelola Templat') }}
                </flux:button>
            </div>

            <flux:select wire:model.live="letter_template_id" label="{{ __('Pilih Templat Desain') }}" placeholder="{{ __('Pilih templat...') }}">
                <flux:select.option value="">{{ __('--- Gunaan Konten Manual (Legacy) ---') }}</flux:select.option>
                @foreach($templates as $tpl)
                    <flux:select.option value="{{ $tpl['id'] }}">{{ $tpl['nama'] }} ({{ $tpl['kode'] }})</flux:select.option>
                @endforeach
            </flux:select>
            
            @if(!$letter_template_id)
                <div class="mt-6 border-t pt-6 dark:border-zinc-700" wire:key="manual-editor">
                    <flux:heading size="sm" class="mb-2">{{ __('Konten Manual (Legacy)') }}</flux:heading>
                    <flux:subheading class="mb-3">{{ __('Gunakan ini jika belum membuat templat di modul Templat Surat.') }}</flux:subheading>
                    
                    <div class="mb-3 flex flex-wrap gap-1.5">
                        @foreach(['{nama}', '{nik}', '{tempat_lahir}', '{tanggal_lahir}', '{alamat}', '{pekerjaan}', '{nomor_surat}', '{tanggal_cetak}'] as $ph)
                            <code class="cursor-pointer rounded bg-zinc-100 px-2 py-0.5 text-xs text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                                  @click="insertPlaceholder('{{ $ph }}')">{{ $ph }}</code>
                        @endforeach
                    </div>

                    <div wire:ignore>
                        <textarea id="legacy-template-editor">{{ $template }}</textarea>
                    </div>
                </div>
            @else
                <div class="mt-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50" wire:key="professional-info">
                    <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:icon.information-circle class="size-4" />
                        <span>{{ __('Surat akan dicetak menggunakan desain profesional dari templat yang dipilih.') }}</span>
                    </div>
                </div>
            @endif
        </flux:card>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('correspondence.letter-type.index') }}" variant="ghost">
                {{ __('Batal') }}
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ __('Simpan Kategori') }}
            </flux:button>
        </div>
    </form>

    {{-- TinyMCE --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.2.1/tinymce.min.js"></script>
    <script>
        function letterTypeEditor() {
            return {
                init() {
                    this.initTinyMCE();
                    
                    document.addEventListener('livewire:navigated', () => {
                        this.initTinyMCE();
                    });

                    // Re-init when manual editor area appears
                    this.$watch('$wire.letter_template_id', (value) => {
                        if (!value) {
                            setTimeout(() => this.initTinyMCE(), 100);
                        }
                    });
                },
                initTinyMCE() {
                    const selector = '#legacy-template-editor';
                    if (!document.querySelector(selector)) return;

                    if (tinymce.get('legacy-template-editor')) {
                        tinymce.get('legacy-template-editor').remove();
                    }

                    const isDark = document.documentElement.classList.contains('dark');

                    tinymce.init({
                        selector: selector,
                        height: 400,
                        license_key: 'gpl',
                        skin: isDark ? 'oxide-dark' : 'oxide',
                        content_css: isDark ? 'dark' : 'default',
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
                        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                        setup: (editor) => {
                            editor.on('change', () => {
                                @this.set('template', editor.getContent());
                            });
                        }
                    });
                },
                insertPlaceholder(ph) {
                    const editor = tinymce.get('legacy-template-editor');
                    if (editor) {
                        editor.insertContent(ph);
                    }
                }
            }
        }
    </script>

    <style>
        .tox-tinymce {
            border-radius: 0.5rem !important;
            border: 1px solid #e4e4e7 !important;
        }
        .dark .tox-tinymce {
            border: 1px solid #3f3f46 !important;
        }
    </style>
</div>
