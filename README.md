# SID - Sistem Informasi Desa

SID adalah platform manajemen informasi desa modern yang dibangun dengan Laravel 13, Flux UI, dan Livewire 4. Platform ini dirancang untuk mendigitalisasi layanan administrasi desa, manajemen kependudukan, transparansi keuangan, dan komunikasi publik.

## Fitur Utama

- **Manajemen Kependudukan**: Pengelolaan data penduduk, Kartu Keluarga, dan struktur wilayah (Dusun, RW, RT).
- **Layanan Surat**: Administrasi permohonan surat warga secara digital dengan alur persetujuan yang terintegrasi.
- **Transparansi Keuangan**: Pengelolaan APBDes, anggaran (RAB), dan pencatatan transaksi realisasi.
- **Layanan Publik**: Pengaduan warga, pengumuman desa, dan profil aparatur desa.
- **Sistem Keamanan**: Role-Based Access Control (RBAC) menggunakan Spatie Laravel Permission.

## Tech Stack

- **Backend**: PHP 8.5+, Laravel 13
- **Frontend**: Livewire 4, Flux UI, Volt, Tailwind CSS 4
- **Database**: MySQL/MariaDB
- **Tools**: Pest (Testing), Laravel Pint (Formatting)

## Instalasi

1. Clone repositori ini.
2. Instal dependensi PHP: `composer install`.
3. Instal dependensi Node: `npm install`.
4. Salin `.env.example` ke `.env` dan sesuaikan konfigurasi database.
5. Generate key: `php artisan key:generate`.
6. Jalankan migrasi dan seeder: `php artisan migrate --seed`.
7. Jalankan server development: `php artisan serve` dan `npm run dev` atau `composer run dev`.

## Dokumentasi Teknis

Untuk detail lebih lanjut mengenai arsitektur dan modul-modul di dalam aplikasi ini, silakan merujuk ke direktori [docs/](./docs/README.md).

- [Arsitektur Modular](./docs/architecture.md)
- [Modul Kependudukan](./docs/modules/population.md)
- [Modul Keuangan](./docs/modules/finance.md)
- [Modul Korespondensi](./docs/modules/correspondence.md)
- [Modul Layanan Publik](./docs/modules/public-service.md)
- [Modul Sistem](./docs/modules/system.md)
