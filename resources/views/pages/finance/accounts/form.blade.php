<?php

declare(strict_types=1);

use Livewire\Component;
use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceAccountService;

new class extends Component {
    public ?FinanceAccount $account = null;
    
    public string $code = '';
    public string $name = '';
    public string $type = 'pengeluaran';
    public bool $is_active = true;

    public function mount(?FinanceAccount $account = null): void
    {
        if ($account && $account->exists) {
            $this->account = $account;
            $this->code = $account->code;
            $this->name = $account->name;
            $this->type = $account->type;
            $this->is_active = $account->is_active;
        }
    }

    public function save(): void
    {
        $rules = [
            'code' => 'required|string|max:255|unique:finance_accounts,code' . ($this->account ? ',' . $this->account->id : ''),
            'name' => 'required|string|max:255',
            'type' => 'required|in:pemasukan,pengeluaran,pembiayaan',
            'is_active' => 'boolean',
        ];

        $data = $this->validate($rules);

        if ($this->account) {
            app(FinanceAccountService::class)->update($this->account->id, $data);
            $message = __('Mata anggaran diperbarui.');
        } else {
            app(FinanceAccountService::class)->create($data);
            $message = __('Mata anggaran ditambahkan.');
        }

        $this->dispatch('notify', message: $message);
        $this->redirect(route('finance.accounts.index'), navigate: true);
    }
}; ?>

<div class="w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">
            {{ $account ? __('Edit Mata Anggaran') : __('Tambah Mata Anggaran Baru') }}
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Kode rekening mengacu pada standar bagan akun APBDes.') }}
        </p>
    </div>
    <flux:card>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="code" label="{{ __('Kode Rekening') }}" placeholder="e.g. 4.1.1.01" required />

            <flux:input wire:model="name" label="{{ __('Uraian / Nama Rekening') }}" placeholder="e.g. Hasil Usaha Desa"
                required />

            <flux:radio.group wire:model="type" label="{{ __('Tipe Akun') }}" class="flex gap-4">
                <flux:radio value="pemasukan" label="{{ __('Pemasukan') }}" />
                <flux:radio value="pengeluaran" label="{{ __('Pengeluaran') }}" />
                <flux:radio value="pembiayaan" label="{{ __('Pembiayaan') }}" />
            </flux:radio.group>

            <flux:switch wire:model="is_active" label="{{ __('Status Aktif') }}" />

            <div class="flex justify-end gap-3 border-t pt-6 dark:border-zinc-700">
                <flux:button href="{{ route('finance.accounts.index') }}" variant="ghost" wire:navigate>
                    {{ __('Batal') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>