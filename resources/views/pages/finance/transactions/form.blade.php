<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Finance\Models\FinanceTransaction;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceTransactionService;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?FinanceTransaction $transaction = null;
    
    public string $finance_period_id = '';
    public string $finance_account_id = '';
    public string $type = 'pengeluaran';
    public string $transaction_date = '';
    public string $amount = '';
    public string $description = '';

    public function mount(?FinanceTransaction $transaction = null): void
    {
        $this->transaction_date = date('Y-m-d');
        
        if ($transaction && $transaction->exists) {
            $this->transaction = $transaction;
            $this->finance_period_id = $transaction->finance_period_id;
            $this->finance_account_id = $transaction->finance_account_id;
            $this->type = $transaction->type;
            $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
            $this->amount = (string) $transaction->amount;
            $this->description = $transaction->description;
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
        return FinanceAccount::where('type', $this->type)->where('is_active', true)->orderBy('code')->get();
    }

    public function updatedType()
    {
        // Reset account when type changes
        $this->finance_account_id = '';
    }

    public function save(): void
    {
        $rules = [
            'finance_period_id' => 'required|exists:finance_periods,id',
            'finance_account_id' => 'required|exists:finance_accounts,id',
            'type' => 'required|in:pemasukan,pengeluaran,pembiayaan',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];

        $data = $this->validate($rules);

        if ($this->transaction) {
            app(FinanceTransactionService::class)->update($this->transaction->id, $data);
            $message = __('Transaksi diperbarui.');
        } else {
            app(FinanceTransactionService::class)->create($data);
            $message = __('Transaksi dicatat.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('finance.transactions.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ $transaction ? __('Edit Transaksi') : __('Catat Transaksi Baru') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Pencatatan realisasi penerimaan, belanja, atau pembiayaan desa.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="finance_period_id" label="{{ __('Tahun Anggaran') }}" required>
                    <flux:select.option value="" disabled>{{ __('Pilih Tahun') }}</flux:select.option>
                    @foreach($this->periods as $period)
                    <flux:select.option value="{{ $period->id }}">
                        {{ $period->year }} {{ $period->is_active ? '(Aktif)' : '' }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="transaction_date" type="date" label="{{ __('Tanggal Transaksi') }}" required />
            </div>

            <flux:radio.group wire:model.live="type" label="{{ __('Jenis Transaksi') }}" class="flex gap-4">
                <flux:radio value="pemasukan" label="{{ __('Pemasukan') }}" />
                <flux:radio value="pengeluaran" label="{{ __('Pengeluaran') }}" />
                <flux:radio value="pembiayaan" label="{{ __('Pembiayaan') }}" />
            </flux:radio.group>

            <flux:select wire:model="finance_account_id" label="{{ __('Akun (Kode Rekening)') }}" required search>
                <flux:select.option value="" disabled>{{ __('Pilih Akun') }}</flux:select.option>
                @foreach($this->accounts as $account)
                <flux:select.option value="{{ $account->id }}">
                    {{ $account->code }} - {{ $account->name }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="amount" type="number" step="0.01" label="{{ __('Nilai Transaksi (Rp)') }}"
                placeholder="0.00" required />

            <flux:textarea wire:model="description" label="{{ __('Uraian / Keterangan Pembayaran') }}" rows="3"
                required />

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('finance.transactions.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>