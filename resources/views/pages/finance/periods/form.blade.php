<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Services\FinancePeriodService;

new class extends Component {
    public ?FinancePeriod $period = null;
    
    public string $year = '';
    public string $description = '';
    public bool $is_active = false;

    public function mount(?FinancePeriod $period = null): void
    {
        if ($period && $period->exists) {
            $this->period = $period;
            $this->year = (string) $period->year;
            $this->description = $period->description ?? '';
            $this->is_active = $period->is_active;
        } else {
            $this->year = (string) date('Y');
        }
    }

    public function save(): void
    {
        $rules = [
            'year' => 'required|integer|min:2000|max:2100|unique:finance_periods,year' . ($this->period ? ',' . $this->period->id : ''),
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        $data = $this->validate($rules);

        if ($this->period) {
            app(FinancePeriodService::class)->update($this->period->id, $data);
            $message = __('Tahun anggaran diperbarui.');
        } else {
            app(FinancePeriodService::class)->create($data);
            $message = __('Tahun anggaran ditambahkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('finance.periods.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ $period ? __('Edit Tahun Anggaran') : __('Tambah Tahun Anggaran') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Pastikan hanya ada satu tahun anggaran yang aktif dalam satu waktu.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="year" type="number" step="1" label="{{ __('Tahun') }}" placeholder="e.g. 2026"
                required />

            <flux:input wire:model="description" label="{{ __('Keterangan (Opsional)') }}"
                placeholder="APBD Desa Tahun 2026" />

            <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/30">
                <flux:switch wire:model="is_active" label="{{ __('Set sebagai Tahun Berjalan Aktif') }}"
                    description="{{ __('Mengaktifkan tahun ini akan otomatis menonaktifkan tahun lainnya.') }}" />
            </div>

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('finance.periods.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>