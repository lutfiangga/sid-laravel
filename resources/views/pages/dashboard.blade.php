<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Modules\Population\Models\Penduduk;
use Modules\Population\Models\KartuKeluarga;
use Modules\Correspondence\Models\LetterRequest;
use Modules\PublicService\Models\Announcement;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
    #[Computed]
    public function stats(): array
    {
        return [
            [
                'label' => 'Total Penduduk',
                'value' => Penduduk::count(),
                'icon' => 'users',
                'description' => 'Jiwa terdaftar',
                'color' => 'blue',
            ],
            [
                'label' => 'Total Keluarga',
                'value' => KartuKeluarga::count(),
                'icon' => 'home',
                'description' => 'Kepala Keluarga',
                'color' => 'indigo',
            ],
            [
                'label' => 'Permohonan Surat',
                'value' => LetterRequest::where('workflow_status', 'submitted')->count(),
                'icon' => 'document-text',
                'description' => 'Menunggu verifikasi',
                'color' => 'orange',
            ],
        ];
    }

    #[Computed]
    public function latestAnnouncements()
    {
        return Announcement::latest()->take(5)->get();
    }

    #[Computed]
    public function recentRequests()
    {
        return LetterRequest::with(['type', 'penduduk'])->latest()->take(5)->get();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-1">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach ($this->stats as $stat)
            <flux:card class="flex items-center gap-4 bg-white dark:bg-neutral-800 shadow-sm border-none">
                <div class="flex size-12 items-center justify-center rounded-lg bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 dark:bg-{{ $stat['color'] }}-900/20 dark:text-{{ $stat['color'] }}-400">
                    <flux:icon :name="$stat['icon']" class="size-6" />
                </div>
                <div>
                    <flux:heading size="sm" class="text-neutral-500 dark:text-neutral-400 uppercase tracking-tight">{{ $stat['label'] }}</flux:heading>
                    <flux:subheading size="xl" class="font-bold text-neutral-900 dark:text-white">{{ number_format($stat['value']) }}</flux:subheading>
                    <p class="text-xs text-neutral-400 mt-1">{{ $stat['description'] }}</p>
                </div>
            </flux:card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Requests -->
        <flux:card class="bg-white dark:bg-neutral-800 shadow-sm border-none">
            <div class="flex items-center justify-between mb-6">
                <flux:heading level="2" size="lg">Permohonan Terbaru</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('correspondence.letter-request.index')" wire:navigate>Lihat Semua</flux:button>
            </div>
            
            <div class="space-y-5">
                @forelse ($this->recentRequests as $request)
                    <div class="flex items-center gap-4 py-1">
                        <div class="size-10 rounded-full bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center text-neutral-500 font-medium">
                            {{ substr($request->penduduk?->nama ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $request->penduduk?->nama ?? 'Unknown' }}</p>
                            <p class="text-xs text-neutral-500">{{ $request->type?->nama ?? 'General' }}</p>
                        </div>
                        <flux:badge size="sm" color="neutral">{{ $request->workflow_status }}</flux:badge>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-neutral-400">
                        <flux:icon name="document-text" class="size-10 mb-2 opacity-20" />
                        <p class="text-sm italic">Belum ada permohonan surat.</p>
                    </div>
                @endforelse
            </div>
        </flux:card>

        <!-- Latest Announcements -->
        <flux:card class="bg-white dark:bg-neutral-800 shadow-sm border-none">
            <div class="flex items-center justify-between mb-6">
                <flux:heading level="2" size="lg">Pengumuman Terbaru</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('public-service.announcements.index')" wire:navigate>Lihat Semua</flux:button>
            </div>

            <div class="space-y-6">
                @forelse ($this->latestAnnouncements as $announcement)
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white hover:text-blue-600 transition-colors cursor-pointer">{{ $announcement->title }}</p>
                            <p class="text-xs text-neutral-500 line-clamp-1 mt-1 leading-relaxed">{{ strip_tags($announcement->content) }}</p>
                            <p class="text-[10px] text-neutral-400 mt-2 uppercase tracking-widest font-semibold">{{ $announcement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-neutral-400">
                        <flux:icon name="megaphone" class="size-10 mb-2 opacity-20" />
                        <p class="text-sm italic">Belum ada pengumuman.</p>
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>
</div>
