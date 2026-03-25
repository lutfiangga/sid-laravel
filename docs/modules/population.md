# Modul Kependudukan (Population)

Modul ini bertanggung jawab atas seluruh data kependudukan desa, termasuk struktur wilayah dan hubungan keluarga.

## Komponen Utama

### 1. Kartu Keluarga (KK)
- Penyimpanan data nomor KK, alamat, dan kepala keluarga.
- Relasi: Memiliki banyak `Penduduk`.

### 2. Penduduk
- Data detail individu (NIK, Nama, Tempat/Tanggal Lahir, Pekerjaan, Pendidikan, dll).
- Relasi: Milik satu `Kartu Keluarga`.

### 3. Wilayah (Dusun, RW, RT)
- **Dusun**: Wilayah tingkat tertinggi di desa.
- **RW (Rukun Warga)**: Berada di bawah Dusun.
- **RT (Rukun Tetangga)**: Wilayah terkecil, berada di bawah RW.

Setiap entitas wilayah saling terhubung secara hierarkis (`RT` -> `RW` -> `Dusun`).

## Fitur Unggulan
- **Standardized UI**: Tabel Flux UI yang konsisten dengan fitur pencarian dan ekspor CSV.
- **Manajemen Hubungan**: Memudahkan penelusuran anggota keluarga berdasarkan nomor KK.
- **Statistik Otomatis**: Digunakan di landing page untuk menampilkan total penduduk secara real-time.
