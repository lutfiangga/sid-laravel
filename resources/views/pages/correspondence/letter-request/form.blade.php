<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Services\LetterTypeService;
use Modules\Correspondence\Services\LetterRequestService;
use Modules\Population\Models\Penduduk;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithFileUploads;

    public array $letter_types = [];
    public ?string $letter_type_id = null;
    public array $data = [];
    public array $attachments = []; // Temporary storage for file uploads
    public array $links = [];       // Storage for drive links
    public ?LetterType $selected_type = null;
    public ?Penduduk $penduduk = null;

    public function mount(): void
    {
        $this->letter_types = app(LetterTypeService::class)->getAll()->pluck('nama', 'id')->toArray();
        
        // Auto-detect linked resident for Warga role
        if (Auth::user()->hasRole('Warga')) {
            $this->penduduk = Penduduk::where('user_id', Auth::id())->first();
        }
    }

    public function updatedLetterTypeId($value): void
    {
        if ($value) {
            $this->selected_type = app(LetterTypeService::class)->find($value);
            $this->data = [];
            $this->attachments = [];
            $this->links = [];
        } else {
            $this->selected_type = null;
        }
    }

    public function save(): void
    {
        $rules = [
            'letter_type_id' => 'required|uuid',
            'data.keperluan' => 'required|string',
        ];

        // Add validation for requirements if needed
        // For simplicity, we'll just check if required fields exist in save logic
        $this->validate($rules);

        if (!$this->penduduk) {
            $this->dispatch('notify', message: __('Data penduduk tidak ditemukan. Silakan hubungi admin.'), variant: 'danger');
            return;
        }

        // Handle file uploads
        $uploadedPaths = [];
        foreach ($this->attachments as $key => $file) {
            if ($file) {
                $path = $file->store('attachments/letters', 'public');
                $uploadedPaths[$key] = $path;
            }
        }

        // Combine uploads and links into a single attachments JSON
        $finalAttachments = array_merge($uploadedPaths, $this->links);

        $request = app(LetterRequestService::class)->create([
            'penduduk_id' => $this->penduduk->id,
            'letter_type_id' => $this->letter_type_id,
            'data' => $this->data,
            'attachments' => $finalAttachments,
            'workflow_status' => 'submitted',
        ]);

        $this->dispatch('notify', message: __('Permohonan surat berhasil dikirim.'));
        $this->redirect(route('correspondence.letter-request.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="mb-6">
            <flux:heading size="xl">{{ __('Buat Permohonan Surat') }}</flux:heading>
            <flux:subheading>{{ __('Pilih jenis layanan dan lengkapi data serta berkas yang diperlukan.') }}</flux:subheading>
        </div>

        @if(!$penduduk && auth()->user()->hasRole('Warga'))
            <flux:card class="mb-6 border-red-200 bg-red-50 dark:bg-red-900/10">
                <div class="flex items-center gap-3 text-red-700 dark:text-red-400">
                    <flux:icon.exclamation-triangle class="size-5" />
                    <p class="text-sm font-medium">{{ __('Akun Anda belum terhubung dengan data penduduk. Silakan hubungi operator desa.') }}</p>
                </div>
            </flux:card>
        @endif

        <flux:card>
            <form wire:submit="save" class="space-y-6">
                {{-- Resident Info (Read Only for Warga) --}}
                @if($penduduk)
                    <div class="flex items-center gap-4 rounded-lg border bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <div class="flex size-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                            <flux:icon.user class="size-5 text-zinc-500" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">{{ $penduduk->nama }}</p>
                            <p class="text-xs text-zinc-500">{{ __('NIK:') }} {{ $penduduk->nik }} | {{ $penduduk->alamat }}</p>
                        </div>
                    </div>
                @endif

                <flux:select wire:model.live="letter_type_id" label="{{ __('Jenis Layanan/Surat') }}" placeholder="{{ __('Pilih Layanan...') }}" required>
                    @foreach($letter_types as $id => $nama)
                        <flux:select.option value="{{ $id }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>

                @if($selected_type)
                    {{-- Form Fields based on Requirements --}}
                    <div class="space-y-6 border-t pt-6 dark:border-zinc-700">
                        <flux:heading size="sm">{{ __('Data & Berkas Persyaratan') }}</flux:heading>
                        
                        {{-- Common Keperluan Field --}}
                        <flux:textarea wire:model="data.keperluan" label="{{ __('Maksud & Keperluan') }}" 
                            placeholder="{{ __('e.g. Untuk persyaratan pendaftaran sekolah') }}" rows="3" required />

                        @foreach($selected_type->requirement_list ?? [] as $index => $req)
                            @php
                                $label = is_array($req) ? ($req['label'] ?? '') : $req;
                                $type = is_array($req) ? ($req['type'] ?? 'text') : 'text';
                                $key = 'req_' . $index;
                            @endphp

                            <div class="space-y-2">
                                <flux:label>{{ $label }} <span class="text-red-500">*</span></flux:label>
                                
                                @if($type === 'file')
                                    <div class="flex flex-col gap-2">
                                        <input type="file" wire:model="attachments.{{ $key }}" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300" />
                                        <div wire:loading wire:target="attachments.{{ $key }}" class="text-xs text-blue-500">{{ __('Mengunggah...') }}</div>
                                        @if(isset($attachments[$key]))
                                            <p class="text-xs text-emerald-600">{{ __('File siap diunggah.') }}</p>
                                        @endif
                                    </div>
                                @elseif($type === 'link')
                                    <flux:input wire:model="links.{{ $key }}" icon="link" placeholder="e.g. https://drive.google.com/..." />
                                @else
                                    <flux:input wire:model="data.{{ $key }}" placeholder="{{ __('Masukkan keterangan...') }}" />
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                    <flux:button href="{{ route('correspondence.letter-request.index') }}" variant="ghost">
                        {{ __('Batal') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" :disabled="!penduduk">
                        {{ __('Kirim Permohonan') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
