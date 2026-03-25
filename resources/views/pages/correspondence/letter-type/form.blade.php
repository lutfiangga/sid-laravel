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
    public array $requirement_list = [];
    public string $new_requirement = '';

    public function mount(?LetterType $letterType = null): void
    {
        if ($letterType && $letterType->exists) {
            $this->letterType = $letterType;
            $this->nama = $letterType->nama;
            $this->kode = $letterType->kode;
            $this->template = $letterType->template;
            $this->requirement_list = $letterType->requirement_list ?? [];
        }
    }

    public function addRequirement(): void
    {
        if ($this->new_requirement) {
            $this->requirement_list[] = $this->new_requirement;
            $this->new_requirement = '';
        }
    }

    public function removeRequirement(int $index): void
    {
        unset($this->requirement_list[$index]);
        $this->requirement_list = array_values($this->requirement_list);
    }

    public function save(): void
    {
        $data = [
            'nama' => $this->nama,
            'kode' => $this->kode,
            'template' => $this->template,
            'requirement_list' => $this->requirement_list,
        ];

        // Custom validation for update to exclude self from unique check
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

<div class="py-12">
    <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
                    {{ $letterType ? __('Edit Kategori Surat') : __('Tambah Kategori Surat') }}
                </h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Isi detail kategori dan template isi surat.') }}
                </p>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <flux:input wire:model="nama" label="{{ __('Nama Kategori') }}" placeholder="e.g. Surat Keterangan Usaha" required />
                    <flux:input wire:model="kode" label="{{ __('Kode Surat') }}" placeholder="e.g. SKU" required />
                </div>

                <flux:textarea wire:model="template" label="{{ __('Template Isi Surat') }}" 
                    placeholder="{{ __('Gunakan placeholder seperti {nama}, {nik}, {alamat}...') }}" 
                    rows="10" required />

                <div class="space-y-4">
                    <flux:label>{{ __('Persyaratan Dokumen') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:input wire:model="new_requirement" placeholder="{{ __('Tambah persyaratan e.g. Fotokopi KTP') }}" wire:keydown.enter.prevent="addRequirement" />
                        <flux:button wire:click.prevent="addRequirement" variant="secondary" icon="plus" />
                    </div>

                    @if($requirement_list)
                        <div class="flex flex-wrap gap-2">
                            @foreach($requirement_list as $index => $req)
                                <flux:badge variant="neutral" class="pr-1">
                                    {{ $req }}
                                    <button type="button" wire:click="removeRequirement({{ $index }})" class="ml-1 text-zinc-400 hover:text-red-500">
                                        <flux:icon.x-mark size="sm" />
                                    </button>
                                </flux:badge>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                    <flux:button href="{{ route('correspondence.letter-type.index') }}" variant="ghost">
                        {{ __('Batal') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Simpan') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
