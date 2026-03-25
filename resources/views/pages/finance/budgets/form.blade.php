<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Finance\Models\FinanceBudget;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceBudgetService;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?FinanceBudget $budget = null;
    
    public string $finance_period_id = '';
    public string $finance_account_id = '';
    public string $amount = '';
    public string $notes = '';

    public function mount(?FinanceBudget $budget = null): void
    {
        if ($budget && $budget->exists) {
            $this->budget = $budget;
            $this->finance_period_id = $budget->finance_period_id;
            $this->finance_account_id = $budget->finance_account_id;
            $this->amount = (string) $budget->amount;
            $this->notes = $budget->notes ?? '';
        } else {
            $activePeriod = FinancePeriod::where('is_active', true)->first();
            if ($activePeriod) {
                $this->finance_period_id = $activePeriod->id;
            }
        }
    }

    #[Computed]
    public function periods()
    {
        return FinancePeriod::orderBy('year', 'desc')->get();
    }

    #[Computed]
    public function accounts()
    {
        return FinanceAccount::where('is_active', true)->orderBy('code')->get();
    }

    public function save(): void
    {
        $rules = [
            'finance_period_id' => 'required|exists:finance_periods,id',
            'finance_account_id' => 'required|exists:finance_accounts,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];

        $data = $this->validate($rules);

        // Optional: Ensure unique period & account if storing new
        if (!$this->budget) {
            $exists = FinanceBudget::where('finance_period_id', $data['finance_period_id'])
                ->where('finance_account_id', $data['finance_account_id'])
                ->exists();
            if ($exists) {
                $this->addError('finance_account_id', __('Akun ini sudah memiliki RAB di tahun anggaran yang dipilih.'));
                return;
            }
        }

        if ($this->budget) {
            app(FinanceBudgetService::class)->update($this->budget->id, $data);
            $message = __('RAB diperbarui.');
        } else {
            app(FinanceBudgetService::class)->create($data);
            $message = __('RAB ditambahkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('finance.budgets.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ $budget ? __('Edit Pagu Anggaran') : __('Buat Pagu Anggaran Baru') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Tetapkan nilai RAB untuk suatu akun pada tahun anggaran tertentu.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">
            <flux:select wire:model="finance_period_id" label="{{ __('Tahun Anggaran') }}" required>
                <flux:select.option value="" disabled>{{ __('Pilih Tahun') }}</flux:select.option>
                @foreach($this->periods as $period)
                <flux:select.option value="{{ $period->id }}">
                    {{ $period->year }} {{ $period->is_active ? '(Aktif)' : '' }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="finance_account_id" label="{{ __('Akun (Kode Rekening)') }}" required search>
                <flux:select.option value="" disabled>{{ __('Pilih Akun') }}</flux:select.option>
                @foreach($this->accounts as $account)
                <flux:select.option value="{{ $account->id }}">
                    {{ $account->code }} - {{ $account->name }} ({{ strtoupper($account->type) }})
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="amount" type="number" step="0.01" label="{{ __('Jumlah (Rp)') }}" placeholder="0.00"
                required />

            <flux:textarea wire:model="notes" label="{{ __('Catatan (Opsional)') }}" rows="3" />

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('finance.budgets.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>