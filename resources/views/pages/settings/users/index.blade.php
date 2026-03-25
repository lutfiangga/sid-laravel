<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Modules\System\Contracts\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

return new class extends Component {
    use WithPagination;

    public string $search = '';

    public function mount()
    {
        Gate::authorize('viewAny', User::class);
    }

    public function delete(int $userId)
    {
        Gate::authorize('delete', User::class);
        
        // Prevent deleting self
        if (auth()->id() === $userId) {
            $this->addError('general', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        app(UserServiceInterface::class)->delete($userId);
        session()->flash('status', 'Pengguna berhasil dihapus.');
    }

    
    public function export(UserServiceInterface $service)
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
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->with('roles')
            ->latest()
            ->paginate(10);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Manajemen Pengguna') }}</flux:heading>
            <flux:subheading>{{ __('Kelola akun administrator dan tetapkan peran akses.') }}</flux:subheading>
        </div>
        @can('create', App\Models\User::class)
        <flux:button variant="primary" icon="plus" href="{{ route('users.create') }}" wire:navigate>
            {{ __('Pengguna Baru') }}
        </flux:button>
        @endcan
    </div>

    @if (session('status'))
    <flux:card
        class="mb-6 border-green-200 bg-green-50 text-green-600 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
        <div class="flex items-center gap-3">
            <flux:icon.check-circle class="size-5" />
            <flux:text class="font-medium">{{ session('status') }}</flux:text>
        </div>
    </flux:card>
    @endif

    @error('general')
    <flux:card
        class="mb-6 border-red-200 bg-red-50 text-red-600 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
        <div class="flex items-center gap-3">
            <flux:icon.exclamation-triangle class="size-5" />
            <flux:text class="font-medium">{{ $message }}</flux:text>
        </div>
    </flux:card>
    @enderror

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full md:w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama atau email..." clearable />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->users">
            <flux:table.columns>
                <flux:table.column>Pengguna</flux:table.column>
                <flux:table.column>Peran (Role)</flux:table.column>
                <flux:table.column>Bergabung</flux:table.column>
                <flux:table.column align="right">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="$user->name" size="sm" />
                            <div>
                                <flux:heading size="sm" class="font-medium">{{ $user->name }}</flux:heading>
                                <flux:text size="sm">{{ $user->email }}</flux:text>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @forelse($user->roles as $role)
                        <flux:badge size="sm" color="zinc" class="mr-1" inset="top bottom">{{ $role->name }}
                        </flux:badge>
                        @empty
                        <flux:badge size="sm" color="red" inset="top bottom">Tanpa Akses</flux:badge>
                        @endforelse
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $user->created_at->format('d M Y') }}
                    </flux:table.cell>
                    <flux:table.cell align="right">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" inset="top bottom" />

                            <flux:menu>
                                @can('update', App\Models\User::class)
                                <flux:menu.item icon="pencil-square" href="{{ route('users.edit', $user) }}"
                                    wire:navigate>
                                    {{ __('Edit') }}
                                </flux:menu.item>
                                @endcan
                                @can('delete', App\Models\User::class)
                                <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $user->id }})"
                                    wire:confirm="Hapus {{ $user->name }} dari sistem secara permanen?">
                                    {{ __('Hapus') }}
                                </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>