<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterRequestService;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    #[Computed]
    public function pendingRequests()
    {
        // Officials only see what's pending their review
        // For now, let's show all that are NOT approved/rejected/draft
        return app(LetterRequestService::class)->getPaginated(
            filters: ['workflow_status' => ['submitted', 'rt_review', 'rw_review', 'admin_review']],
            perPage: 10
        );
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Kotak Masuk Persetujuan') }}</h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Daftar permohonan surat yang memerlukan tindakan Anda.') }}</p>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Pemohon') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Jenis Surat') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Status Saat Ini') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->pendingRequests as $request)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">{{ $request->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $request->penduduk->nama }}</div>
                                    <div class="text-xs text-zinc-400">{{ $request->penduduk->nik }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $request->type->nama }}</td>
                                <td class="px-6 py-4">
                                    <flux:badge variant="neutral" size="sm" class="uppercase">
                                        {{ str_replace('_', ' ', $request->workflow_status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <flux:button href="{{ route('correspondence.letter-request.detail', $request->id) }}" variant="primary" size="sm">
                                        {{ __('Tinjau') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Hore! Tidak ada permohonan yang tertunda.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->pendingRequests->links() }}
            </div>
        </flux:card>
    </div>
</div>
