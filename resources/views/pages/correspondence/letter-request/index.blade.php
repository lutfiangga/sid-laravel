<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterRequestService;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';

    #[Computed]
    public function letterRequests()
    {
        // Simple logic: residents only see their own
        // Officials see all (simplified for now)
        return app(LetterRequestService::class)->getPaginated(
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
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Permohonan Surat') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Daftar permohonan surat layanan warga.') }}</p>
                </div>
                <flux:button href="{{ route('correspondence.letter-request.create') }}" variant="primary" icon="plus">
                    {{ __('Buat Permohonan') }}
                </flux:button>
            </div>

            <div class="mb-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nomor surat/status...') }}" icon="magnifying-glass" />
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('No. Surat') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Pemohon') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Jenis Surat') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->letterRequests as $request)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $request->nomor_surat ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $request->penduduk->nama ?? '-' }}<br>
                                    <span class="text-xs text-zinc-400">{{ $request->penduduk->nik ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $request->type->nama }}</td>
                                <td class="px-6 py-4 text-xs font-semibold">
                                    @php
                                        $statusColors = [
                                            'draft' => 'text-zinc-500',
                                            'submitted' => 'text-blue-500',
                                            'rt_review' => 'text-indigo-500',
                                            'rw_review' => 'text-purple-500',
                                            'admin_review' => 'text-amber-500',
                                            'approved' => 'text-emerald-500',
                                            'rejected' => 'text-red-500',
                                        ];
                                        $color = $statusColors[$request->workflow_status] ?? 'text-zinc-500';
                                    @endphp
                                    <span class="{{ $color }} uppercase">
                                        {{ str_replace('_', ' ', $request->workflow_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <flux:button href="{{ route('correspondence.letter-request.detail', $request->id) }}" size="sm" variant="ghost" icon="eye" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Tidak ada data ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->letterRequests->links() }}
            </div>
        </flux:card>
    </div>
</div>
