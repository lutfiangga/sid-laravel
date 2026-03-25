<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @canany(['penduduk.view', 'kartu-keluarga.view', 'dusun.view', 'rw.view', 'rt.view'])
                <flux:sidebar.group :heading="__('Kependudukan')" class="grid">
                    @can('viewAny', \Modules\Population\Models\Penduduk::class)
                    <flux:sidebar.item icon="users" :href="route('population.penduduk.index')" :current="request()->routeIs('population.penduduk.*')" wire:navigate>
                        {{ __('Data Penduduk') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Population\Models\KartuKeluarga::class)
                    <flux:sidebar.item icon="identification" :href="route('population.kartu-keluarga.index')" :current="request()->routeIs('population.kartu-keluarga.*')" wire:navigate>
                        {{ __('Kartu Keluarga') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Population\Models\Dusun::class)
                    <flux:sidebar.item icon="map" :href="route('population.dusun.index')" :current="request()->routeIs('population.dusun.*')" wire:navigate>
                        {{ __('Dusun') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Population\Models\Rw::class)
                    <flux:sidebar.item icon="map-pin" :href="route('population.rw.index')" :current="request()->routeIs('population.rw.*')" wire:navigate>
                        {{ __('Rukun Warga (RW)') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Population\Models\Rt::class)
                    <flux:sidebar.item icon="home-modern" :href="route('population.rt.index')" :current="request()->routeIs('population.rt.*')" wire:navigate>
                        {{ __('Rukun Tetangga (RT)') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
                @endcanany

                @canany(['letter-request.view', 'letter-type.view', 'workflow.view'])
                <flux:sidebar.group :heading="__('Layanan Surat')" class="grid">
                    @can('viewAny', \Modules\Correspondence\Models\WorkflowLog::class)
                    <flux:sidebar.item icon="inbox-arrow-down" :href="route('correspondence.approval.index')" :current="request()->routeIs('correspondence.approval.*')" wire:navigate>
                        {{ __('Kotak Masuk') }}
                        @php
                            $pendingCount = \Modules\Correspondence\Models\LetterRequest::whereIn('workflow_status', ['submitted', 'rt_review', 'rw_review', 'admin_review'])->count();
                        @endphp
                        @if($pendingCount > 0)
                            <flux:badge size="sm" color="amber" inset="top bottom">{{ $pendingCount }}</flux:badge>
                        @endif
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Correspondence\Models\LetterRequest::class)
                    <flux:sidebar.item icon="document-text" :href="route('correspondence.letter-request.index')" :current="request()->routeIs('correspondence.letter-request.*')" wire:navigate>
                        {{ __('Permohonan') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Correspondence\Models\LetterType::class)
                    <flux:sidebar.item icon="squares-plus" :href="route('correspondence.letter-type.index')" :current="request()->routeIs('correspondence.letter-type.*')" wire:navigate>
                        {{ __('Kategori Surat') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\LetterTemplate\Models\LetterTemplate::class)
                    <flux:sidebar.item icon="document-duplicate" :href="route('letter-template.index')" :current="request()->routeIs('letter-template.*')" wire:navigate>
                        {{ __('Templat Surat') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
                @endcanany

                @canany(['announcement.view', 'complaint.view', 'apparatus.view'])
                <flux:sidebar.group :heading="__('Layanan Publik')" class="grid">
                    @can('viewAny', \Modules\PublicService\Models\Announcement::class)
                    <flux:sidebar.item icon="megaphone" :href="route('public-service.announcements.index')" :current="request()->routeIs('public-service.announcements.*')" wire:navigate>
                        {{ __('Pengumuman & Berita') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\PublicService\Models\Complaint::class)
                    <flux:sidebar.item icon="chat-bubble-left-ellipsis" :href="route('public-service.complaints.index')" :current="request()->routeIs('public-service.complaints.*')" wire:navigate>
                        {{ __('Pengaduan Warga') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\PublicService\Models\Apparatus::class)
                    <flux:sidebar.item icon="user-group" :href="route('public-service.apparatus.index')" :current="request()->routeIs('public-service.apparatus.*')" wire:navigate>
                        {{ __('Aparatur Desa') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
                @endcanany

                @canany(['finance-period.view', 'finance-account.view', 'finance-budget.view', 'finance-transaction.view'])
                <flux:sidebar.group :heading="__('Keuangan Desa (APBD)')" class="grid">
                    @can('viewAny', \Modules\Finance\Models\FinancePeriod::class)
                    <flux:sidebar.item icon="calendar" :href="route('finance.periods.index')" :current="request()->routeIs('finance.periods.*')" wire:navigate>
                        {{ __('Tahun Anggaran') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Finance\Models\FinanceAccount::class)
                    <flux:sidebar.item icon="cube-transparent" :href="route('finance.accounts.index')" :current="request()->routeIs('finance.accounts.*')" wire:navigate>
                        {{ __('Kode Rekening (CoA)') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Finance\Models\FinanceBudget::class)
                    <flux:sidebar.item icon="chart-bar" :href="route('finance.budgets.index')" :current="request()->routeIs('finance.budgets.*')" wire:navigate>
                        {{ __('Rencana Anggaran (RAB)') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('viewAny', \Modules\Finance\Models\FinanceTransaction::class)
                    <flux:sidebar.item icon="banknotes" :href="route('finance.transactions.index')" :current="request()->routeIs('finance.transactions.*')" wire:navigate>
                        {{ __('Realisasi / Transaksi') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
                @endcanany
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                @can('viewAny', \Spatie\Permission\Models\Role::class)
                <flux:sidebar.item icon="shield-check" :href="route('rbac.index')" :current="request()->routeIs('rbac.*')" wire:navigate>
                    {{ __('Role & Hak Akses') }}
                </flux:sidebar.item>
                @endcan
                @can('viewAny', \App\Models\User::class)
                <flux:sidebar.item icon="user" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                    {{ __('Pengguna') }}
                </flux:sidebar.item>
                @endcan

                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
