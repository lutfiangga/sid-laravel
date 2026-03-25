<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Modules\System\Contracts\Services\RoleServiceInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

return new class extends Component {
    public ?Role $role = null;
    
    public string $name = '';
    public array $selectedPermissions = [];

    public function mount(?Role $role = null)
    {
        if ($role && $role->exists) {
            Gate::authorize('update', Role::class);
            $this->role = $role;
            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        } else {
            Gate::authorize('create', Role::class);
        }
    }

    public function save()
    {
        if ($this->role && $this->role->exists) {
            Gate::authorize('update', Role::class);
        } else {
            Gate::authorize('create', Role::class);
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->role?->id)],
            'selectedPermissions' => ['array']
        ]);

        $service = app(RoleServiceInterface::class);

        if ($this->role && $this->role->exists) {
            // Prevent changing SuperAdmin name
            if ($this->role->name === 'SuperAdmin' && $this->name !== 'SuperAdmin') {
                $this->addError('name', 'Role SuperAdmin adalah role sistem inti dan namanya tidak boleh diubah.');
                return;
            }

            $role = $service->update($this->role->id, ['name' => $this->name]);
            $service->syncPermissions($role, $this->selectedPermissions);
            
            session()->flash('status', 'Role berhasil diperbarui.');
        } else {
            $role = $service->create(['name' => $this->name]);
            $service->syncPermissions($role, $this->selectedPermissions);
            
            session()->flash('status', 'Role berhasil ditambahkan.');
        }

        $this->redirect(route('rbac.index'), navigate: true);
    }

    #[Computed]
    public function groupedPermissions(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'Lainnya';
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }
}; ?>

<div class="w-full">
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('rbac.index') }}" wire:navigate />
        <div>
            <flux:heading size="xl" level="1">{{ $role && $role->exists ? __('Konfigurasi Role') : __('Role Baru') }}</flux:heading>
            <flux:subheading size="lg">{{ $role && $role->exists ? __('Tekan simpan setelah mengubah nama atau daftar izin.') : __('Buat grup wewenang baru dalam sistem.') }}</flux:subheading>
        </div>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <form wire:submit="save" class="space-y-8">
        <div class="max-w-2xl">
            <flux:card>
                <flux:input 
                    wire:model="name" 
                    :label="__('Nama Role')" 
                    :placeholder="__('Contoh: Sekretaris Desa')" 
                    :disabled="$role && $role->name === 'SuperAdmin'"
                    required 
                />
                @if($role && $role->name === 'SuperAdmin')
                    <flux:text size="sm" class="text-amber-600 dark:text-amber-400 mt-2 font-medium italic">
                        {{ __('Catatan: Nama role sistem ini tidak dapat diubah.') }}
                    </flux:text>
                @endif
            </flux:card>
        </div>

        <div>
            <flux:heading size="lg" class="mb-2">{{ __('Daftar Hak Akses (Permissions)') }}</flux:heading>
            <flux:subheading class="mb-6">{{ __('Pilih izin yang ingin diberikan kepada role ini. Perubahan akan langsung berdampak pada seluruh pengguna dengan role terkait.') }}</flux:subheading>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($this->groupedPermissions as $module => $permissions)
                    <flux:card class="flex flex-col">
                        <flux:heading size="md" class="capitalize mb-4 flex items-center gap-2">
                            <flux:icon.rectangle-group variant="mini" class="text-zinc-400" />
                            {{ str_replace('-', ' ', $module) }}
                        </flux:heading>
                        
                        <flux:separator variant="subtle" class="mb-4" />

                        <div class="space-y-3 flex-1">
                            @foreach($permissions as $permission)
                                @php
                                    $action = explode('.', $permission->name)[1] ?? $permission->name;
                                @endphp
                                <flux:checkbox 
                                    wire:model="selectedPermissions" 
                                    :value="$permission->name" 
                                    :label="ucfirst($action)" 
                                    class="text-sm"
                                />
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </div>

        <div class="pt-6 flex justify-end gap-3 sticky bottom-0 bg-white dark:bg-zinc-800 py-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button variant="ghost" href="{{ route('rbac.index') }}" wire:navigate>{{ __('Batal') }}</flux:button>
            <flux:button variant="primary" type="submit">{{ __('Simpan Konfigurasi Role') }}</flux:button>
        </div>
    </form>
</div>
