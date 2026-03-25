<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\PublicService\Services\ComplaintService;
use Livewire\Attributes\Computed;

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

    #[Computed]
    public function complaints()
    {
        $filters = [];
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }

        return app(ComplaintService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 10
        );
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Pengaduan Warga') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Daftar aspirasi dan keluhan dari masyarakat.') }}</p>
                </div>
                <flux:button href="{{ route('public-service.complaints.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Buat Pengaduan') }}
                </flux:button>
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari pengaduan...') }}" icon="magnifying-glass" />
                </div>
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

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Pelapor') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Judul Pengaduan') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->complaints as $complaint)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 text-xs">{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $complaint->penduduk->nama ?? __('Anonim/Dihapus') }}
                                </td>
                                <td class="px-6 py-4">{{ Str::limit($complaint->title, 40) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'zinc',
                                            'in_progress' => 'blue',
                                            'resolved' => 'emerald',
                                            'rejected' => 'red',
                                        ];
                                        $color = $statusColors[$complaint->status] ?? 'zinc';
                                    @endphp
                                    <flux:badge color="{{ $color }}" size="sm" class="uppercase">
                                        {{ str_replace('_', ' ', $complaint->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <flux:button href="{{ route('public-service.complaints.detail', $complaint->id) }}" size="sm" variant="ghost" icon="eye" wire:navigate />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Tidak ada data pengaduan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->complaints->links() }}
            </div>
        </flux:card>
    </div>
</div>
