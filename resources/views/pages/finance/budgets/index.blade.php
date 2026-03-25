<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Services\FinanceBudgetService;
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

    
    public function export(FinanceBudgetService $service)
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
    public function budgets()
    {
        $filters = [];
        if ($this->periodFilter) {
            $filters['finance_period_id'] = $this->periodFilter;
        }

        return app(FinanceBudgetService::class)->getPaginated(
            filters: $filters,
            search: $this->search,
            perPage: 15,
            with: ['period', 'account']
        );
    }

    public function delete(string $id): void
    {
        app(FinanceBudgetService::class)->delete($id);
        $this->dispatch('notify', message: __('RAB dihapus.'));
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Rencana Anggaran Biaya (RAB)') }}</flux:heading>
            <flux:subheading>{{ __('Kelola target anggaran tahunan desa.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('finance.budgets.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('Pagu Anggaran Baru') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex gap-4">
            <div class="w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari catatan...') }}" icon="magnifying-glass" />
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

        <flux:table :paginate="$this->budgets">
            <flux:table.columns>
                <flux:table.column>{{ __('Tahun') }}</flux:table.column>
                <flux:table.column>{{ __('Kode & Rekening') }}</flux:table.column>
                <flux:table.column>{{ __('Jumlah (Rp)') }}</flux:table.column>
                <flux:table.column>{{ __('Catatan') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->budgets as $budget)
                    <flux:table.row :key="$budget->id">
                        <flux:table.cell>{{ $budget->period->year }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="font-mono text-xs text-zinc-500">{{ $budget->account->code }}</div>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $budget->account->name }}</div>
                            <div class="text-[0.65rem] uppercase tracking-wider text-zinc-400">{{ $budget->account->type }}</div>
                        </flux:table.cell>

                        <flux:table.cell class="font-bold text-emerald-600 dark:text-emerald-400">
                            {{ number_format((float) $budget->amount, 2, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell>{{ Str::limit($budget->notes ?? '-', 50) }}</flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('finance.budgets.edit', $budget->id) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $budget->id }}')" wire:confirm="{{ __('Yakin menghapus pagu anggaran ini?') }}">{{ __('Hapus') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
