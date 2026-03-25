# Arsitektur Aplikasi

Platform SID menggunakan pendekatan **Modular Architecture** untuk memastikan kode tetap terorganisir, mudah diuji, dan dapat dikembangkan secara independen.

## 1. app/Core (The Foundation)

Direktori `app/Core` berisi logika dasar yang digunakan oleh seluruh modul. Ini mencakup:
- **Base Components**: `BaseModel`, `BaseRepository`, `BaseService`.
- **Contracts**: Interface global untuk standarisasi fungsionalitas.
- **Traits**: Utility yang bersifat lintas-modul (seperti `HasSearch`, `HasExport`).
- **Support**: Helper khusus seperti `Exporter`.

## 2. Modules/ (The Business Logic)

Setiap fitur besar dipisahkan ke dalam modulnya masing-masing di direktori `Modules/`. Struktur tipikal sebuah modul adalah:

```text
Modules/Population/
├── Contracts/       # Interface khusus modul (Repositories/Services)
├── Models/          # Eloquent Models
├── Policies/        # Aturan otorisas (Spatie/Laravel Policies)
├── Repositories/    # Implementasi akses data (Entity-specific)
├── Services/        # Business logic dan workflow
└── Providers/       # Binding Service Provider (Interface to Implementation)
```

## 3. Workflow Data (Data Flow)

Aplikasi mengikuti alur tanggung jawab yang ketat:
1. **Livewire Component (View/Volt)**: Menangani interaksi UI dan input pengguna.
2. **Service Layer**: Menjalankan logika bisnis, validasi tambahan, dan memanggil repository.
3. **Repository Layer**: Berinteraksi langsung dengan database melalui Eloquent.

Pola ini memudahkan penggantian implementasi (Testing via Mocking Interface) dan menjaga komponen UI tetap bersih dari logika database yang kompleks.

## 4. Frontend Standar

Seluruh UI administratif diwajibkan menggunakan **Flux UI Table** dengan layout yang seragam (`<section class="w-full">` -> `<flux:card>` -> `<flux:table>`).
Paginasi menggunakan kustomisasi di `resources/views/flux/pagination.blade.php` untuk gaya yang lebih ringkas.
