# Modul Sistem (System)

Modul ini menangani pengaturan global dan keamanan platform SID.

## Komponen Utama

### 1. User Management (Pengguna)
- Pengelolaan akun administrator dan staf desa.
- Terintegrasi dengan sistem otentikasi Laravel standar.

### 2. RBAC (Role-Based Access Control)
- Manajemen Role (Peran) dan Hak Akses (Permissions) menggunakan `spatie/laravel-permission`.
- Standar Role mencakup `SuperAdmin`, `Admin Desa`, `RT`, `RW`, dan `Warga`.
- Memastikan isolasi data antar level wilayah (masih dalam pengembangan).

### 3. Pengaturan Global
- Konfigurasi nama desa, logo, dan informasi kontak yang muncul di seluruh sistem (termasuk Landing Page).

## Implementasi Keamanan
- Pengetatan akses melalui `BasePolicy`.
- Validasi data melalui `BaseRequest` di level Core.
