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

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">{{ __('Rencana Anggaran Biaya (RAB)') }}</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Kelola target anggaran tahunan desa.') }}</p>
                </div>
                <flux:button href="{{ route('finance.budgets.create') }}" variant="primary" icon="plus" wire:navigate>
                    {{ __('Pagu Anggaran Baru') }}
                </flux:button>
            </div>

            <div class="mb-4 flex gap-4">
                <div class="w-1/3">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari catatan...') }}" icon="magnifying-glass" />
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
                            <th scope="col" class="px-6 py-3">{{ __('Tahun') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Kode & Rekening') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Jumlah (Rp)') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Catatan') }}</th>
                            <th scope="col" class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->budgets as $budget)
                            <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4">{{ $budget->period->year }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-mono text-xs text-zinc-500">{{ $budget->account->code }}</div>
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $budget->account->name }}</div>
                                    <div class="text-[0.65rem] uppercase tracking-wider text-zinc-400">{{ $budget->account->type }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format((float) $budget->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">{{ Str::limit($budget->notes ?? '-', 50) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button href="{{ route('finance.budgets.edit', $budget->id) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                                        <flux:button wire:click="delete('{{ $budget->id }}')" 
                                            wire:confirm="{{ __('Yakin menghapus pagu anggaran ini?') }}"
                                            size="sm" variant="ghost" color="danger" icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
                                    {{ __('Data RAB tidak ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $this->budgets->links() }}
            </div>
        </flux:card>
    </div>
</div>
