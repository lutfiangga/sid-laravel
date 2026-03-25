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

<div class="w-full">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Manajemen Pengguna') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Kelola akun administrator dan tetapkan peran akses.') }}</flux:subheading>
        </div>
        @can('create', App\Models\User::class)
        <flux:button variant="primary" icon="plus" href="{{ route('users.create') }}" wire:navigate>
            {{ __('Pengguna Baru') }}
        </flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <div class="mb-6 mt-4 w-full md:w-1/3">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau email..." clearable />
    </div>

    @if (session('status'))
        <flux:card class="mb-6 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3">
                <flux:icon.check-circle class="size-5" />
                <flux:text class="font-medium">{{ session('status') }}</flux:text>
            </div>
        </flux:card>
    @endif

    @error('general')
        <flux:card class="mb-6 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800">
            <div class="flex items-center gap-3">
                <flux:icon.exclamation-triangle class="size-5" />
                <flux:text class="font-medium">{{ $message }}</flux:text>
            </div>
        </flux:card>
    @enderror

    <flux:card class="p-0">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Pengguna</flux:table.column>
                    <flux:table.column>Peran (Role)</flux:table.column>
                    <flux:table.column>Bergabung</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                
                <flux:table.rows>
                    @forelse($this->users as $user)
                        <flux:table.row>
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
                                    <flux:badge size="sm" color="zinc" class="mr-1">{{ $role->name }}</flux:badge>
                                @empty
                                    <flux:badge size="sm" color="red">Tanpa Akses</flux:badge>
                                @endforelse
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $user->created_at->format('d M Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" inset="top bottom" />

                                    <flux:menu>
                                        @can('update', App\Models\User::class)
                                        <flux:menu.item icon="pencil-square" href="{{ route('users.edit', $user) }}" wire:navigate>
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        @endcan
                                        @can('delete', App\Models\User::class)
                                        <flux:menu.item icon="trash" wire:click="delete({{ $user->id }})" wire:confirm="Hapus {{ $user->name }} dari sistem secara permanen?">
                                            {{ __('Hapus') }}
                                        </flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center text-zinc-500 py-8">
                                Tidak ada pengguna ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="px-6 py-4">
            {{ $this->users->links() }}
        </div>
    </flux:card>
</div>
