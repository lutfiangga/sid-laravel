<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\LetterTemplate\Services\LetterTemplateService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function templates()
    {
        return app(LetterTemplateService::class)->getPaginated(
            search: $this->search,
            perPage: 10
        );
    }

    public function delete(string $id): void
    {
        app(LetterTemplateService::class)->delete($id);
        $this->dispatch('notify', message: __('Templat surat berhasil dihapus.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Templat Surat') }}</flux:heading>
            <flux:subheading>{{ __('Kelola desain dan tata letak dokumen surat desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('letter-template.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Buat Templat Baru') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nama/kode templat...') }}" icon="magnifying-glass" />
        </div>

        <flux:table :paginate="$this->templates">
            <flux:table.columns>
                <flux:table.column>{{ __('Nama Templat') }}</flux:table.column>
                <flux:table.column>{{ __('Kode') }}</flux:table.column>
                <flux:table.column>{{ __('Layout') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->templates as $template)
                    <flux:table.row :key="$template->id">
                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $template->nama }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc" class="font-mono">{{ $template->kode }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-col text-xs text-zinc-500">
                                <span class="capitalize">{{ $template->orientation }} / A4</span>
                                <span>{{ $template->margin_left }}mm | {{ $template->margin_top }}mm | {{ $template->margin_right }}mm | {{ $template->margin_bottom }}mm</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('letter-template.edit', $template->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete('{{ $template->id }}')" 
                                    wire:confirm="{{ __('Apakah Anda yakin ingin menghapus templat ini?') }}"
                                    size="sm" variant="ghost" icon="trash" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
