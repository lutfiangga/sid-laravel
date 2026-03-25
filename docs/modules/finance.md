# Modul Keuangan (Finance)

Modul ini menyediakan sistem pencatatan keuangan yang transparan untuk pengelolaan APBDes.

## Komponen Utama

### 1. Periode Anggaran (Periods)
- Mengatur tahun fiskal yang aktif.
- Hanya diperbolehkan satu periode aktif dalam satu waktu melalui `FinancePeriodService`.

### 2. Mata Anggaran (Accounts)
- Bagan akun (COA) yang mencakup Pemasukan, Pengeluaran, dan Pembiayaan.
- Kode rekening standar sesuai regulasi desa.

### 3. Pagu Anggaran (Budgets/RAB)
- Penetapan target anggaran untuk setiap mata akun pada periode tertentu.

### 4. Transaksi (Transactions)
- Pencatatan realisasi dana (penerimaan/pengeluaran) harian.
- Terhubung dengan Akun dan Periode aktif.

## Standar Tampilan
- Seluruh antarmuka menggunakan **Flux UI Table**.
- Fitur Ekspor CSV tersedia di setiap daftar periode, akun, dan transaksi.
