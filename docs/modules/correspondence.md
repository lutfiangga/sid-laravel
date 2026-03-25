# Modul Korespondensi (Correspondence)

Modul ini mendigitalisasi proses administrasi surat-menyurat di desa.

## Alur Kerja (Workflow)

1. **Jenis Surat (Letter Types)**: Administrator mengatur jenis layanan surat (e.g., Surat Keterangan Tidak Mampu, Domisili) beserta persyaratan dokumennya.
2. **Permohonan (Letter Requests)**: Warga atau perangkat mengisi formulir permohonan melalui dashboard.
3. **Drafting & Approval**: Admin melakukan verifikasi data dan memberikan nomor surat sebelum disetujui.

## Status Permohonan
- `draft`: Masih dalam pengeditan.
- `submitted`: Menunggu verifikasi.
- `rt_review` / `rw_review`: Tahap verifikasi wilayah (opsional).
- `approved`: Surat selesai diproses.
- `rejected`: Permohonan ditolak dengan alasan tertentu.

## Fitur Utama
- **Template Dinamis**: Persyaratan yang dapat disesuaikan per jenis surat.
- **Pencarian Cepat**: Filter berdasarkan nomor surat atau status permohonan.
- **Integrasi Penduduk**: Data otomatis terhubung ke identitas penduduk di modul Population.
