<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Services\LetterTypeService;
use Modules\Correspondence\Services\LetterRequestService;
use Modules\Population\Models\Penduduk;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public array $letter_types = [];
    public ?string $letter_type_id = null;
    public array $data = [];
    public ?LetterType $selected_type = null;

    public function mount(): void
    {
        $this->letter_types = app(LetterTypeService::class)->all()->pluck('nama', 'id')->toArray();
    }

    public function updatedLetterTypeId($value): void
    {
        if ($value) {
            $this->selected_type = app(LetterTypeService::class)->find($value);
            // Prepare initial data based on template placeholders (simplified)
            $this->data = [];
        } else {
            $this->selected_type = null;
        }
    }

    public function save(): void
    {
        $this->validate([
            'letter_type_id' => 'required|uuid',
            'data' => 'required|array',
        ]);

        // Find resident link for current user (simplified: assume user is linked to a resident)
        // For now, let resident pick which resident they are if admin, or auto-detect if resident
        // Let's assume we pick a random one for demo if no link exists
        $penduduk = Penduduk::first();

        $request = app(LetterRequestService::class)->create([
            'penduduk_id' => $penduduk->id,
            'letter_type_id' => $this->letter_type_id,
            'data' => $this->data,
            'workflow_status' => 'submitted', // Auto submit for now
        ]);

        $this->dispatch('notify', message: __('Permohonan surat berhasil dikirim.'));
        $this->redirect(route('correspondence.letter-request.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
                    {{ __('Buat Permohonan Surat') }}
                </h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Pilih jenis layanan dan lengkapi data yang diperlukan.') }}
                </p>
            </div>

            <form wire:submit="save" class="space-y-6">
                <flux:select wire:model.live="letter_type_id" label="{{ __('Jenis Layanan/Surat') }}" placeholder="{{ __('Pilih Layanan...') }}" required>
                    @foreach($letter_types as $id => $nama)
                        <flux:select.option value="{{ $id }}">{{ $nama }}</flux:select.option>
                    @endforeach
                </flux:select>

                @if($selected_type)
                    <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <h3 class="mb-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Persyaratan:') }}</h3>
                        <ul class="list-inside list-disc text-xs text-zinc-600 dark:text-zinc-400">
                            @forelse($selected_type->requirement_list ?? [] as $req)
                                <li>{{ $req }}</li>
                            @empty
                                <li>{{ __('Tidak ada persyaratan khusus.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <flux:label>{{ __('Keterangan / Keperluan') }}</flux:label>
                    <flux:textarea wire:model="data.keperluan" placeholder="{{ __('Jelaskan keperluan Anda (e.g. Untuk persyaratan kredit bank)') }}" rows="4" required />
                </div>

                <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                    <flux:button href="{{ route('correspondence.letter-request.index') }}" variant="ghost">
                        {{ __('Batal') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Kirim Permohonan') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
