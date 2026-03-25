<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Services\FinanceTransactionService;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $periodFilter = '';

    public function mount()
    {
        $activePeriod = FinancePeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->periodFilter = $activePeriod->id;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedPeriodFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function periods()
    {
        return FinancePeriod::orderBy('year', 'desc')->get();
    }

    #[Computed]
    public function transactions()
    {
        $filters = [];
        if ($this->periodFilter) {
            $filters['finance_period_id'] = $this->periodFilter;
        }

        return app(FinanceTransactionService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 15,
            with: ['period', 'account']
        );
    }

    public function delete(string $id): void
    {
        app(FinanceTransactionService::class)->delete($id);
        $this->dispatch('notify', message: __('Transaksi dihapus.'));
    }
}; ?>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Realisasi / Transaksi') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Jurnal pencatatan penerimaan dan pengeluaran dana desa.') }}</p>
                </div>
                <flux:button href="{{ route('finance.transactions.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Catat Transaksi') }}
                </flux:button>
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari deskripsi transaksi...') }}" icon="magnifying-glass" />
                </div>
                <div class="w-1/4">
                    <flux:select wire:model.live="periodFilter" placeholder="{{ __('Pilih Tahun Anggaran') }}">
                        @foreach ($this->periods as $period)
                            <flux:select.option value="{{ $period->id }}">
                                {{ $period->year }} {{ $period->is_active ? '(Aktif)' : '' }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 text-xs uppercase text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Jenis & Rekening') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Uraian / Deskripsi') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Nilai Transaksi (Rp)') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->transactions as $trx)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->transaction_date->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($trx->type === 'pemasukan')
                                            <flux:icon.arrow-down-circle variant="micro" class="text-emerald-500" />
                                        @elseif($trx->type === 'pengeluaran')
                                            <flux:icon.arrow-up-circle variant="micro" class="text-red-500" />
                                        @else
                                            <flux:icon.arrow-path-rounded-square variant="micro" class="text-blue-500" />
                                        @endif
                                        <div class="font-mono text-xs text-zinc-500">{{ $trx->account->code ?? '-' }}</div>
                                    </div>
                                    <div class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $trx->account->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $trx->description }}</td>
                                <td class="px-6 py-4 font-bold {{ $trx->type === 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : ($trx->type === 'pengeluaran' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') }}">
                                    {{ $trx->type === 'pengeluaran' ? '-' : '' }}{{ number_format((float) $trx->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('finance.transactions.edit', $trx->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $trx->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus transaksi ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Riwayat transaksi tidak ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->transactions->links() }}
            </div>
        </flux:card>
    </div>
</div>
