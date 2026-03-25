<?php

use Livewire\Component;
use Modules\Population\Models\Penduduk;
use Modules\Population\Models\KartuKeluarga;
use Modules\PublicService\Models\Announcement;
use Modules\PublicService\Models\Apparatus;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function stats()
    {
        return [
            'total_penduduk' => Penduduk::count(),
            'total_kk' => KartuKeluarga::count(),
            'males' => Penduduk::where('jenis_kelamin', 'Laki-laki')->count(),
            'females' => Penduduk::where('jenis_kelamin', 'Perempuan')->count(),
        ];
    }

    #[Computed]
    public function announcements()
    {
        return Announcement::with('author')
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();
    }

    #[Computed]
    public function apparatus()
    {
        return Apparatus::where('status', 'aktif')
            ->orderBy('id', 'asc')
            ->take(4)
            ->get();
    }
};
?>

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 font-sans">
    {{-- Header / Navbar --}}
    <header class="fixed top-0 inset-x-0 z-50 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-600/20">A</div>
                <div>
                    <span class="block text-lg font-bold text-zinc-900 dark:text-white leading-tight">Desa <span class="text-emerald-600 dark:text-emerald-400">Arjosari</span></span>
                    <span class="block text-xs font-medium text-zinc-500 uppercase tracking-wider">Website Resmi</span>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <a href="#" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ __('Beranda') }}</a>
                <a href="#profil" class="text-sm font-medium text-zinc-600 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 transition-colors">{{ __('Profil Desa') }}</a>
                <a href="#stats" class="text-sm font-medium text-zinc-600 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 transition-colors">{{ __('Statistik') }}</a>
                <a href="#pamong" class="text-sm font-medium text-zinc-600 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 transition-colors">{{ __('Pemerintahan') }}</a>
            </nav>
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('login') }}" variant="primary" class="rounded-full px-6 shadow-sm shadow-emerald-600/20 hover:shadow-emerald-600/40">
                    {{ __('Portal Warga') }}
                </flux:button>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative pt-20 lg:pt-0 lg:h-[90vh] flex items-center overflow-hidden bg-zinc-900">
        <img src="/brain/fc4ddf09-9a37-4767-bf36-31ab38543547/village_hero_illustration_1774407533400.png" 
             alt="Village Hero" 
             class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay">
        <div class="absolute inset-0 bg-gradient-to-b from-zinc-900/60 via-zinc-900/40 to-zinc-900/90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-50 dark:from-zinc-950 h-32 bottom-0 top-auto"></div>
        
        <div class="relative mx-auto max-w-7xl px-6 w-full text-center py-32 lg:py-0">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-sm font-medium border border-emerald-500/20 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Portal Informasi & Pelayanan Publik
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 text-white drop-shadow-md">
                Selamat Datang di <br/> Desa <span class="text-emerald-400">Arjosari</span>
            </h1>
            <p class="text-lg md:text-xl max-w-2xl mx-auto text-zinc-300 mb-10 leading-relaxed font-light">
                Mewujudkan desa yang mandiri, sejahtera, dan religius melalui tata kelola pemerintahan yang transparan, inovatif, dan partisipatif.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <flux:button href="#layanan" variant="primary" icon-trailing="arrow-right" class="rounded-full px-8 py-3 text-base shadow-lg shadow-emerald-500/30">
                    {{ __('Layanan Desa') }}
                </flux:button>
                <flux:button href="#berita" variant="filled" class="rounded-full px-8 py-3 text-base bg-white/10 text-white hover:bg-white/20 border-0 backdrop-blur-md">
                    {{ __('Kabar Jelajah') }}
                </flux:button>
            </div>
        </div>
    </section>

    {{-- Quick Links / Layanan At A Glance --}}
    <section id="layanan" class="relative z-10 -mt-20 mb-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-xl shadow-zinc-200/50 dark:shadow-none border border-zinc-100 dark:border-zinc-800 transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-6">
                        <flux:icon name="document-text" class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">Layanan Surat</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mb-6 text-sm leading-relaxed">Ajukan permohonan surat pengantar, keterangan, dan perizinan langsung dari rumah Anda.</p>
                    <a href="{{ route('login') }}" class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm flex items-center gap-1 hover:gap-2 transition-all">Mulai Pengajuan <flux:icon.chevron-right variant="micro" class="w-4 h-4"/></a>
                </div>
                <!-- Card 2 -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-xl shadow-zinc-200/50 dark:shadow-none border border-zinc-100 dark:border-zinc-800 transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6">
                        <flux:icon name="megaphone" class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">Pengaduan Warga</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mb-6 text-sm leading-relaxed">Sampaikan aspirasi, kritik, maupun laporan kejadian disekitar Anda kepada pemerintah desa.</p>
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 font-semibold text-sm flex items-center gap-1 hover:gap-2 transition-all">Tulis Laporan <flux:icon.chevron-right variant="micro" class="w-4 h-4"/></a>
                </div>
                <!-- Card 3 -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-xl shadow-zinc-200/50 dark:shadow-none border border-zinc-100 dark:border-zinc-800 transition-transform hover:-translate-y-2">
                    <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-6">
                        <flux:icon name="chart-pie" class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">Transparansi Dana</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mb-6 text-sm leading-relaxed">Pantau realisasi Anggaran Pendapatan dan Belanja Desa (APBDes) secara terbuka dan akuntabel.</p>
                    <a href="#stats" class="text-amber-600 dark:text-amber-400 font-semibold text-sm flex items-center gap-1 hover:gap-2 transition-all">Lihat Data <flux:icon.chevron-right variant="micro" class="w-4 h-4"/></a>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    <section id="stats" class="py-24 bg-white dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-center group hover:border-emerald-500 transition-colors">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">{{ number_format($this->stats['total_penduduk'] ?? 0) }}</div>
                    <div class="text-zinc-500 uppercase tracking-widest text-xs font-semibold">{{ __('Total Penduduk') }}</div>
                </div>
                <div class="p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-center group hover:border-emerald-500 transition-colors">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">{{ number_format($this->stats['total_kk'] ?? 0) }}</div>
                    <div class="text-zinc-500 uppercase tracking-widest text-xs font-semibold">{{ __('Kepala Keluarga') }}</div>
                </div>
                <div class="p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-center group hover:border-emerald-500 transition-colors">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">{{ number_format($this->stats['males'] ?? 0) }}</div>
                    <div class="text-zinc-500 uppercase tracking-widest text-xs font-semibold">{{ __('Laki-laki') }}</div>
                </div>
                <div class="p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 text-center group hover:border-emerald-500 transition-colors">
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">{{ number_format($this->stats['females'] ?? 0) }}</div>
                    <div class="text-zinc-500 uppercase tracking-widest text-xs font-semibold">{{ __('Perempuan') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Announcements Section --}}
    <section class="py-24">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">{{ __('Warta Desa') }}</h2>
                    <p class="text-zinc-500">{{ __('Informasi dan kabar terbaru seputar desa kami.') }}</p>
                </div>
                <flux:button variant="subtle" class="text-emerald-600">{{ __('Lihat Semua Berita') }}</flux:button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($this->announcements as $news)
                    <article class="flex flex-col bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden shadow-sm border border-zinc-100 dark:border-zinc-800 hover:shadow-xl transition-shadow group">
                        @if($news->image_path)
                            <img src="{{ Storage::url($news->image_path) }}" class="h-48 w-full object-cover">
                        @else
                            <div class="h-48 w-full bg-emerald-100 dark:bg-emerald-900/20 flex items-center justify-center">
                                <flux:icon name="newspaper" class="w-12 h-12 text-emerald-500" />
                            </div>
                        @endif
                        <div class="p-8 flex-1 flex flex-col">
                            <time class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-3">{{ $news->published_at?->format('d M Y') }}</time>
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-4 line-clamp-2 group-hover:text-emerald-600 transition-colors">{{ $news->title }}</h3>
                            <p class="text-zinc-500 text-sm line-clamp-3 mb-6">{{ Str::limit(strip_tags($news->content), 120) }}</p>
                            <div class="mt-auto pt-6 border-t border-zinc-50 dark:border-zinc-800 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold">
                                    {{ substr($news->author->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $news->author->name ?? 'Admin' }}</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-20 text-zinc-400 italic">
                        {{ __('Belum ada warta yang diterbitkan.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Apparatus Section --}}
    <section class="py-24 bg-zinc-900 dark:bg-black text-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">{{ __('Pamong Desa') }}</h2>
                <p class="text-zinc-400 max-w-xl mx-auto">{{ __('Mengenal lebih dekat para abdi masyarakat yang berdedikasi untuk kemajuan desa.') }}</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($this->apparatus as $person)
                    <div class="group text-center">
                        <div class="relative mb-6 mx-auto w-48 h-48 rounded-3xl overflow-hidden border-2 border-transparent group-hover:border-emerald-500 transition-all">
                            @if($person->foto_path)
                                <img src="{{ Storage::url($person->foto_path) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                            @else
                                <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                    <flux:icon name="user" class="w-16 h-16 text-zinc-600" />
                                </div>
                            @endif
                        </div>
                        <h4 class="text-lg font-bold mb-1">{{ $person->nama }}</h4>
                        <p class="text-emerald-400 text-sm font-medium">{{ $person->jabatan }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-zinc-950 text-zinc-500 py-12 border-t border-zinc-900">
        <div class="mx-auto max-w-7xl px-6 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">A</div>
                <span class="text-xl font-bold text-white tracking-tight">SID <span class="text-emerald-500">Arjosari</span></span>
            </div>
            <div class="text-sm">
                &copy; {{ date('Y') }} SID Platform. All rights reserved.
            </div>
            <div class="flex gap-6">
                <a href="#" class="hover:text-emerald-500 transition-colors">Privacy</a>
                <a href="#" class="hover:text-emerald-500 transition-colors">Terms</a>
                <a href="#" class="hover:text-emerald-500 transition-colors">Contact</a>
            </div>
        </div>
    </footer>
</div>