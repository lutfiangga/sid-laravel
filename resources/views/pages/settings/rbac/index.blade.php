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

    
    public function export(RoleServiceInterface $service)
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

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Role & Hak Akses') }}</flux:heading>
            <flux:subheading>{{ __('Kelola grup pengguna dan tentukan tingkat akses tiap peran.') }}</flux:subheading>
        </div>
        @can('create', Spatie\Permission\Models\Role::class)
        <flux:button variant="primary" icon="plus" href="{{ route('rbac.create') }}" wire:navigate>
            {{ __('Role Baru') }}
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
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari role..."
                    clearable />
            </div>
            <flux:button wire:click="export" icon="arrow-down-tray" class="ml-auto">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

        <flux:table :paginate="$this->roles">
            <flux:table.columns>
                <flux:table.column>Nama Role</flux:table.column>
                <flux:table.column>Total Izin (Permissions)</flux:table.column>
                <flux:table.column>Anggota</flux:table.column>
                <flux:table.column align="right">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->roles as $role)
                <flux:table.row :key="$role->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:icon.shield-check class="size-5 text-zinc-400" />
                            <span class="font-medium">{{ $role->name }}</span>
                            @if($role->name === 'SuperAdmin')
                            <flux:badge size="sm" color="amber" class="ml-2" inset="top bottom">System Core</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="blue" inset="top bottom">{{ $role->permissions_count ?? 0 }} Akses
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $role->users_count ?? 0 }} Pengguna
                    </flux:table.cell>
                    <flux:table.cell align="right">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" inset="top bottom" />

                            <flux:menu>
                                @can('update', Spatie\Permission\Models\Role::class)
                                <flux:menu.item icon="pencil-square" href="{{ route('rbac.edit', $role) }}"
                                    wire:navigate>
                                    {{ __('Edit Akses') }}
                                </flux:menu.item>
                                @endcan
                                @if($role->name !== 'SuperAdmin')
                                @can('delete', Spatie\Permission\Models\Role::class)
                                <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $role->id }})"
                                    wire:confirm="Hapus role {{ $role->name }} permanen? Semua user di role ini akan kehilangan hak akses!">
                                    {{ __('Hapus') }}
                                </flux:menu.item>
                                @endcan
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>