<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Modules\System\Contracts\Services\RoleServiceInterface;
use Illuminate\Support\Facades\Gate;

return new class extends Component {
    use WithPagination;

    public string $search = '';

    public function mount()
    {
        Gate::authorize('viewAny', Role::class);
    }

    public function delete(int $roleId)
    {
        Gate::authorize('delete', Role::class);
        
        $role = Role::findOrFail($roleId);
        
        if ($role->name === 'SuperAdmin') {
            $this->addError('general', 'Role SuperAdmin adalah role sistem inti dan tidak boleh dihapus.');
            return;
        }

        app(RoleServiceInterface::class)->delete($roleId);
        session()->flash('status', 'Role berhasil dihapus.');
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->withCount(['permissions', 'users'])
            ->latest()
            ->paginate(10);
    }
}; ?>

<div class="w-full">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Role & Hak Akses') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Kelola grup pengguna dan tentukan tingkat akses tiap peran.') }}</flux:subheading>
        </div>
        @can('create', Spatie\Permission\Models\Role::class)
        <flux:button variant="primary" icon="plus" href="{{ route('rbac.create') }}" wire:navigate>
            {{ __('Role Baru') }}
        </flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <div class="mb-6 mt-4 w-full md:w-1/3">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari role..." clearable />
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
                    <flux:table.column>Nama Role</flux:table.column>
                    <flux:table.column>Total Izin (Permissions)</flux:table.column>
                    <flux:table.column>Anggota</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                
                <flux:table.rows>
                    @forelse($this->roles as $role)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:icon.shield-check class="size-5 text-zinc-400" />
                                    <span class="font-medium">{{ $role->name }}</span>
                                    @if($role->name === 'SuperAdmin')
                                        <flux:badge size="sm" color="amber" class="ml-2">System Core</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="blue">{{ $role->permissions_count ?? 0 }} Akses</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $role->users_count ?? 0 }} Pengguna
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" inset="top bottom" />

                                    <flux:menu>
                                        @can('update', Spatie\Permission\Models\Role::class)
                                        <flux:menu.item icon="pencil-square" href="{{ route('rbac.edit', $role) }}" wire:navigate>
                                            {{ __('Edit Akses') }}
                                        </flux:menu.item>
                                        @endcan
                                        @if($role->name !== 'SuperAdmin')
                                        @can('delete', Spatie\Permission\Models\Role::class)
                                        <flux:menu.item icon="trash" wire:click="delete({{ $role->id }})" wire:confirm="Hapus role {{ $role->name }} permanen? Semua user di role ini akan kehilangan hak akses!">
                                            {{ __('Hapus') }}
                                        </flux:menu.item>
                                        @endcan
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center text-zinc-500 py-8">
                                Tidak ada role ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="px-6 py-4">
            {{ $this->roles->links() }}
        </div>
    </flux:card>
</div>