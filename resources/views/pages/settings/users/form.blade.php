<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\System\Contracts\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

return new class extends Component {
    public ?User $user = null;
    
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $selectedRoles = [];

    public function mount(?User $user = null)
    {
        if ($user && $user->exists) {
            Gate::authorize('update', User::class);
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
        } else {
            Gate::authorize('create', User::class);
        }
    }

    public function save()
    {
        if ($this->user && $this->user->exists) {
            Gate::authorize('update', User::class);
        } else {
            Gate::authorize('create', User::class);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user?->id)],
            'selectedRoles' => ['array']
        ];

        if (!$this->user || $this->password) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate($rules);

        $service = app(UserServiceInterface::class);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = $this->password;
        }

        if ($this->user && $this->user->exists) {
            // Prevent changing own role if self to avoid locking out
            if (auth()->id() === $this->user->id && !in_array('SuperAdmin', $this->selectedRoles) && $this->user->hasRole('SuperAdmin')) {
                $this->addError('selectedRoles', 'Anda tidak dapat menghapus akses SuperAdmin dari akun Anda sendiri.');
                return;
            }

            $user = $service->update($this->user->id, $userData);
            $service->syncRoles($user, $this->selectedRoles);
            
            session()->flash('status', 'Pengguna berhasil diperbarui.');
        } else {
            $user = $service->create($userData);
            $service->syncRoles($user, $this->selectedRoles);
            
            session()->flash('status', 'Pengguna berhasil ditambahkan.');
        }

        $this->redirect(route('users.index'), navigate: true);
    }

    #[Computed]
    public function roles()
    {
        return Role::all();
    }
}; ?>

<div class="w-full">
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('users.index') }}" wire:navigate />
        <div>
            <flux:heading size="xl" level="1">{{ $user && $user->exists ? __('Edit Pengguna') : __('Pengguna Baru') }}</flux:heading>
            <flux:subheading size="lg">{{ $user && $user->exists ? __('Ubah informasi profil dan hak akses.') : __('Buat akun akses aplikasi baru.') }}</flux:subheading>
        </div>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <div class="max-w-2xl">
        <flux:card>
            <form wire:submit="save" class="space-y-6">
                <flux:input wire:model="name" :label="__('Nama Lengkap')" :placeholder="__('Contoh: Budi Santoso')" required />
                
                <flux:input wire:model="email" type="email" :label="__('Email')" :placeholder="__('budi@example.com')" required />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="password" type="password" :label="$user && $user->exists ? __('Password Baru (opsional)') : __('Password')" :required="!$user || !$user->exists" viewable />
                    <flux:input wire:model="password_confirmation" type="password" :label="__('Konfirmasi Password')" :required="!$user || !$user->exists" viewable />
                </div>
                
                @if($user && $user->exists && !$password)
                    <flux:text size="sm" class="text-zinc-500 mt-1">{{ __('Kosongkan password jika tidak ingin mengubahnya.') }}</flux:text>
                @endif

                <flux:separator variant="subtle" />

                <div>
                    <flux:heading size="md" class="mb-3">{{ __('Akses Sistem (Role)') }}</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($this->roles as $role)
                            <flux:checkbox wire:model="selectedRoles" :value="$role->name" :label="$role->name" />
                        @endforeach
                    </div>
                    @error('selectedRoles')
                        <flux:error class="mt-2">{{ $message }}</flux:error>
                    @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <flux:button variant="ghost" href="{{ route('users.index') }}" wire:navigate>{{ __('Batal') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Simpan Pengguna') }}</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>
