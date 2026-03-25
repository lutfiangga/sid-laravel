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

    
    public function export(FinanceTransactionService $service)
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

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Realisasi / Transaksi') }}</flux:heading>
            <flux:subheading>{{ __('Jurnal pencatatan penerimaan dan pengeluaran dana desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('finance.transactions.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Catat Transaksi') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex gap-4">
            <div class="w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari deskripsi transaksi...') }}" icon="magnifying-glass" />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        
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

        <flux:table :paginate="$this->transactions">
            <flux:table.columns>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis & Rekening') }}</flux:table.column>
                <flux:table.column>{{ __('Uraian / Deskripsi') }}</flux:table.column>
                <flux:table.column>{{ __('Nilai Transaksi (Rp)') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->transactions as $trx)
                    <flux:table.row :key="$trx->id">
                        <flux:table.cell class="whitespace-nowrap">{{ $trx->transaction_date->format('d M Y') }}</flux:table.cell>

                        <flux:table.cell>
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
                        </flux:table.cell>

                        <flux:table.cell>{{ Str::limit($trx->description, 50) }}</flux:table.cell>

                        <flux:table.cell class="font-bold {{ $trx->type === 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : ($trx->type === 'pengeluaran' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400') }}">
                            {{ $trx->type === 'pengeluaran' ? '-' : '' }}{{ number_format((float) $trx->amount, 2, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('finance.transactions.edit', $trx->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $trx->id }}')" wire:confirm="{{ __('Yakin menghapus transaksi ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
