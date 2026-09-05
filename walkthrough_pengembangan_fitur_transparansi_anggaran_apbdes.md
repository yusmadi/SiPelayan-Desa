# Walkthrough: Transparansi Anggaran (APBDes / APBG) Dinamis & CRUD Operator Desa

Fitur **Transparansi Anggaran (APBDes / APBG)** pada dashboard warga dan sistem **CRUD Transparansi Keuangan Desa** untuk Operator Desa telah berhasil diimplementasikan, diuji secara menyeluruh, dan berfungsi penuh dengan data dinamis dari database multi-tenant.

---

## 🚀 Ringkasan Implementasi

### 1. Database & Model Multi-Tenant
- **Migration** ([`2026-09-03-000002_AddTransparansiColumnsToInformasiDesa.php`](file:///d:/vibe-coding/sipelayan-desa/app/Database/Migrations/2026-09-03-000002_AddTransparansiColumnsToInformasiDesa.php)):
  - Menambahkan kolom `periode_anggaran`, `total_pendapatan`, `realisasi_pendapatan`, `total_belanja`, `realisasi_belanja`, `total_pembiayaan`, dan `rincian_anggaran` (JSON) pada tabel `informasi_desa`.
  - Mengisi data awal (*seed sample*) APBDes/APBG realistis berstandar **Permendagri 20/2018** (Rincian 5 bidang belanja: Pemerintahan, Pembangunan, Kemasyarakatan, Pemberdayaan, dan Bencana/Mendesak).
- **Model** ([`AnggaranModel.php`](file:///d:/vibe-coding/sipelayan-desa/app/Models/AnggaranModel.php)):
  - Mewarisi [`BaseTenantModel`](file:///d:/vibe-coding/sipelayan-desa/app/Models/BaseTenantModel.php) untuk isolasi data otomatis per `village_id`.
  - Menyediakan konstanta standar `PERIODE_LIST` dan `BIDANG_LIST` (5 bidang belanja desa dengan icon & warna tematik).
  - Helper method `getLatestPublished()`, `getListForOperator()`, `getDetailWithAuthor()`, dan `incrementViews()`.

### 2. Modul CRUD Operator Desa
- **Controller** ([`Operator\TransparansiController.php`](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Operator/TransparansiController.php)):
  - `index()`: Rekap kartu metrik keuangan (Pagu Belanja, Realisasi, % Capaian Realisasi, Laporan Aktif) dan tabel manajemen data anggaran.
  - `create()` & `store()`: Input target pendapatan, alokasi belanja per 5 bidang, pembiayaan (SiLPA), upload berkas resmi (PDF SK/Perdes/Infografis baliho maks 10MB ke `uploads/transparansi/`), dan auto-kalkulasi total belanja.
  - `edit()` & `update()`: Pembaruan nilai anggaran, rincian per bidang, penggantian/penghapusan berkas lampiran.
  - `togglePublish()`: Tombol toggle status publikasi (*Terbit* atau *Draft*).
  - `delete()`: Menghapus laporan anggaran beserta berkas fisik lampiran.
  - `detail()`: Pratinjau administratif infografis resmi ber-Kop Surat desa.
- **Views**:
  - [`app/Views/operator/transparansi/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/transparansi/index.php)
  - [`app/Views/operator/transparansi/create.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/transparansi/create.php) (dengan kalkulator otomatis real-time via JavaScript)
  - [`app/Views/operator/transparansi/edit.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/transparansi/edit.php)
  - [`app/Views/operator/transparansi/detail.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/transparansi/detail.php) (*print-friendly*)

### 3. Tampilan Warga (Resmi, Profesional & Elegan)
- **Dashboard Warga** ([`app/Views/warga/dashboard/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/dashboard/index.php)):
  - Menggantikan card statis alert dummy dengan card dinamis **Transparansi Anggaran**.
  - Menampilkan badge periode (*TA 2026 · Tahap I*), badge verifikasi, pagu belanja, capaian realisasi (Rp), dan visual progress bar persentase (%) yang responsif.
  - Tombol **"Lihat Rincian"** langsung membuka modal infografis resmi.
- **Modal Dokumen & Infografis Resmi** ([`app/Views/warga/dashboard/partials/modal_transparansi.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/dashboard/partials/modal_transparansi.php)):
  - Kop Surat Resmi Pemerintah Kabupaten, Kecamatan, Gampong/Desa dengan garis ganda resmi.
  - 3 Kartu Metrik Utama: **Pendapatan Desa**, **Belanja Desa**, dan **Pembiayaan (SiLPA)**.
  - Visual Progress Bar Rincian Belanja pada **5 Bidang Standar Permendagri**.
  - Bagian unduh berkas lampiran resmi (Peraturan Desa / Dokumen Pelaksanaan Anggaran PDF).
  - Tanda tangan pengesahan Keuchik / Kepala Desa.
  - Tombol aksi: **Cetak Dokumen**, **Bagikan ke WhatsApp**, dan **Arsip Keuangan**.
- **Halaman Arsip & Detail Warga**:
  - [`app/Controllers/Warga/TransparansiController.php`](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Warga/TransparansiController.php)
  - [`app/Views/warga/transparansi/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/transparansi/index.php)
  - [`app/Views/warga/transparansi/detail.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/transparansi/detail.php)

### 4. Navigasi & Routing
- Ditambahkan rute CRUD di grup `operator`, `admin-desa`, dan rute arsip/detail di grup `warga` pada [`app/Config/Routes.php`](file:///d:/vibe-coding/sipelayan-desa/app/Config/Routes.php).
- Ditambahkan menu **"Transparansi <?= is_aceh() ? 'APBG' : 'APBDes' ?>"** pada sidebar Operator & Admin Desa di [`app/Views/layouts/admin.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/layouts/admin.php).
- Ditambahkan menu **"Transparansi <?= is_aceh() ? 'APBG' : 'APBDes' ?>"** pada sidebar Warga di [`app/Views/layouts/warga.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/layouts/warga.php).

---

## 🧪 Hasil Verifikasi & Pengujian

Pengujian integrasi otomatis telah dijalankan via HTTP request:

| Test Case | Hasil | Keterangan |
| :--- | :---: | :--- |
| **Migrasi Database** | ✅ PASSED | Batch 8: `AddTransparansiColumnsToInformasiDesa` berhasil dijalankan. |
| **Linting Sintaks PHP** | ✅ PASSED | Bebas syntax error di semua controller, model, migration, dan routes. |
| **Dashboard Warga** | ✅ PASSED | Card Transparansi Anggaran tampil dinamis dengan metrik realisasi dan progress bar. |
| **Modal Infografis Quick View** | ✅ PASSED | Tombol *Lihat Rincian* memicu modal `#modalTransparansi` dengan Kop Surat resmi, metrik 3 pos, dan 5 bidang belanja. |
| **Papan Transparansi Warga** | ✅ PASSED | Halaman `/warga/transparansi` menampilkan arsip laporan keuangan. |
| **Index CRUD Operator** | ✅ PASSED | Stat cards (Pagu, Realisasi, Persen, Terbit) dan tabel data berfungsi. |
| **Formulir Tambah Anggaran** | ✅ PASSED | Form entri dengan kalkulator otomatis real-time berfungsi. |
| **Sinkronisasi Data Baru** | ✅ PASSED | Laporan baru yang disimpan operator seketika **langsung tampil dinamis di dashboard Warga**. |

---

## 📌 Panduan Mencoba Langsung di Browser

Server lokal aktif: `http://localhost:8080/`

1. **Sebagai Warga**:
   - Login di `http://localhost:8080/auth/login` (email: **`masri@gmail.com`** | password: **`12345678`**).
   - Di Dashboard, perhatikan card **Transparansi Anggaran (APBG)** di samping card Pengumuman.
   - Klik tombol **"Lihat Rincian"** untuk melihat infografis resmi ber-Kop Surat desa dan rincian belanja per 5 bidang.
   - Coba juga menu sidebar **"Transparansi APBG"** untuk melihat arsip lengkap.

2. **Sebagai Operator Desa**:
   - Login di `http://localhost:8080/auth/login` (email: **`yusmadi23@gmail.com`** | password: **`12345678`**).
   - Klik menu sidebar **"Transparansi APBG"** (`/operator/transparansi`).
   - Coba buat laporan anggaran baru, ubah nominal per bidang (perhatikan kalkulator otomatis yang langsung menjumlahkan total), unggah berkas, dan ubah status terbit/draft.
