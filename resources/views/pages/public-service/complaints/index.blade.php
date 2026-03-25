<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PublicService\Services\ComplaintService;
use Modules\Population\Models\Penduduk;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    
    public function export(ComplaintService $service)
    {
        $data = $service->export(search: $this->search);
        
        if ($data->isEmpty()) {
            $this->dispatch('notify', message: __('Tidak ada data untuk diekspor.'));
            return;
        }

        $firstItem = collect($data->first()->toArray())->except(['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'password', 'remember_token'])->toArray();
        $columns = [];
        foreach (array_keys($firstItem) as $key) {
            if (!is_array($firstItem[$key])) {
                $columns[$key] = ucwords(str_replace('_', ' ', $key));
            }
        }

        return \App\Core\Support\Exporter::csv($data, $columns, 'export-' . now()->format('Y-m-d') . '.csv');
    }


    #[Computed]
    public function complaints()
    {
        $filters = [];
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }

        // Scope to own complaints if the user has the Warga role
        if (Auth::user()->hasRole('Warga')) {
            $penduduk = Penduduk::where('user_id', Auth::id())->first();
            if ($penduduk) {
                $filters['penduduk_id'] = $penduduk->id;
            }
        }

        return app(ComplaintService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 10
        );
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Pengaduan Warga') }}</flux:heading>
            <flux:subheading>{{ __('Daftar aspirasi dan keluhan dari masyarakat.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('public-service.complaints.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Buat Pengaduan') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex gap-4">
            <div class="w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari pengaduan...') }}" icon="magnifying-glass" />
            </div>
            @unless(auth()->user()->hasRole('Warga'))
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
            @endunless
        
            <div class="w-1/4">
                <flux:select wire:model.live="statusFilter" placeholder="{{ __('Semua Status') }}">
                    <flux:select.option value="">{{ __('Semua Status') }}</flux:select.option>
                    <flux:select.option value="pending">{{ __('Menunggu (Pending)') }}</flux:select.option>
                    <flux:select.option value="in_progress">{{ __('Diproses') }}</flux:select.option>
                    <flux:select.option value="resolved">{{ __('Selesai (Resolved)') }}</flux:select.option>
                    <flux:select.option value="rejected">{{ __('Ditolak (Rejected)') }}</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$this->complaints">
            <flux:table.columns>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Pelapor') }}</flux:table.column>
                <flux:table.column>{{ __('Judul Pengaduan') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->complaints as $complaint)
                    <flux:table.row :key="$complaint->id">
                        <flux:table.cell class="text-xs">{{ $complaint->created_at->format('d/m/Y H:i') }}</flux:table.cell>

                        <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                            {{ $complaint->penduduk->nama ?? __('Anonim/Dihapus') }}
                        </flux:table.cell>

                        <flux:table.cell>{{ Str::limit($complaint->title, 40) }}</flux:table.cell>

                        <flux:table.cell>
                            @php
                                $statusColors = [
                                    'pending' => 'zinc',
                                    'in_progress' => 'blue',
                                    'resolved' => 'emerald',
                                    'rejected' => 'red',
                                ];
                                $color = $statusColors[$complaint->status] ?? 'zinc';
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm" class="uppercase" inset="top bottom">
                                {{ str_replace('_', ' ', $complaint->status) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:button href="{{ route('public-service.complaints.detail', $complaint->id) }}" size="sm" variant="ghost" icon="eye" wire:navigate inset="top bottom" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
