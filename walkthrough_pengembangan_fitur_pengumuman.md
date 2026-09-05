# Walkthrough: Pengumuman Gampong/Desa Dinamis & Modul CRUD Operator

Fitur **Pengumuman Resmi** pada dashboard warga dan sistem **CRUD Pengumuman** untuk Operator Desa telah berhasil diimplementasikan, diuji, dan berfungsi penuh dengan data dinamis dari database.

---

## 🚀 Ringkasan Perubahan

### 1. Database & Model Multi-Tenant
- **Migration** ([`2026-09-03-000001_CreateInformasiDesaTable.php`](file:///d:/vibe-coding/sipelayan-desa/app/Database/Migrations/2026-09-03-000001_CreateInformasiDesaTable.php)):
  - Membuat tabel `informasi_desa` sesuai arsitektur [`database/sipd_schema.sql`](file:///d:/vibe-coding/sipelayan-desa/database/sipd_schema.sql).
  - Dilengkapi atribut administrasi resmi: `nomor_pengumuman`, `sifat` (`Biasa`, `Penting`, `Segera`), `lampiran_path`, `lampiran_nama`, `ringkasan`, `views`, dan status publikasi.
  - Mengisi data awal (*seed sample*) pengumuman kedinasan resmi untuk desa aktif.
- **Model** ([`PengumumanModel.php`](file:///d:/vibe-coding/sipelayan-desa/app/Models/PengumumanModel.php)):
  - Mewarisi [`BaseTenantModel`](file:///d:/vibe-coding/sipelayan-desa/app/Models/BaseTenantModel.php) yang secara otomatis mengisolasi query per `village_id` sehingga data antar desa tidak bocor.

### 2. Modul CRUD Operator Desa
- **Controller** ([`Operator\PengumumanController.php`](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Operator/PengumumanController.php)):
  - `index()`: Rekap statistik kartu (Total, Terbit, Draft, Penting/Segera) dan tabel pencarian/filter.
  - `create()` & `store()`: Pembuatan pengumuman baru dengan saran format nomor surat resmi otomatis, upload berkas lampiran (PDF/gambar maks 5MB ke `uploads/pengumuman/`), auto-slug, dan pilihan publikasi.
  - `edit()` & `update()`: Pembaruan data pengumuman dengan kemampuan hapus/ganti berkas lampiran.
  - `togglePublish()`: Tombol aksi cepat untuk mempublikasikan atau menarik ke status draft.
  - `delete()`: Penghapusan pengumuman dan berkas fisik lampiran.
  - `detail()`: Pratinjau administratif dokumen pengumuman.
- **Views**:
  - [`app/Views/operator/pengumuman/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/pengumuman/index.php)
  - [`app/Views/operator/pengumuman/create.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/pengumuman/create.php)
  - [`app/Views/operator/pengumuman/edit.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/pengumuman/edit.php)
  - [`app/Views/operator/pengumuman/detail.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/operator/pengumuman/detail.php)

### 3. Tampilan Warga (Resmi, Profesional & Elegan)
- **Dashboard Warga** ([`app/Views/warga/dashboard/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/dashboard/index.php)):
  - Menggantikan card statis lama dengan card dinamis **Pengumuman Resmi**.
  - Badge tingkat urgensi (*Penting*, *Segera*, *Biasa*), nomor pengumuman, tanggal terbit berformat Indonesia, dan ringkasan isi.
  - Tombol **"Baca Selengkapnya"** langsung memicu modal Quick View berformat **Kop Surat Resmi Gampong/Desa**.
  - Tombol **"Lihat Semua Pengumuman"** dan **"Arsip"** menuju papan pengumuman.
- **Modal Dokumen Resmi** ([`app/Views/warga/dashboard/partials/modal_pengumuman.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/dashboard/partials/modal_pengumuman.php)):
  - Menampilkan Kop Surat Resmi (Pemerintah Kabupaten, Kecamatan, Gampong/Desa, Alamat & Logo).
  - Garis ganda resmi kop surat, nomor dokumen, isi lengkap yang rapi.
  - Kartu lampiran berkas resmi jika tersedia berkas PDF/gambar yang dapat diunduh langsung.
  - Kotak pengesahan/tanda tangan Keuchik / Kepala Desa.
  - Tombol aksi: **Cetak / Simpan PDF** dan **Bagikan ke WhatsApp**.
- **Halaman Arsip & Detail Warga**:
  - [`app/Controllers/Warga/PengumumanController.php`](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Warga/PengumumanController.php)
  - [`app/Views/warga/pengumuman/index.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/pengumuman/index.php)
  - [`app/Views/warga/pengumuman/detail.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/warga/pengumuman/detail.php) (*print-friendly*)

### 4. Navigasi & Routing
- Menambahkan rute CRUD di grup `operator`, `admin-desa`, dan rute arsip/detail di grup `warga` pada [`app/Config/Routes.php`](file:///d:/vibe-coding/sipelayan-desa/app/Config/Routes.php).
- Menambahkan menu **"Pengumuman <?= sebutan_desa() ?>"** pada sidebar Operator Desa di [`app/Views/layouts/admin.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/layouts/admin.php).
- Menambahkan menu **"Pengumuman <?= sebutan_desa() ?>"** pada sidebar Warga di [`app/Views/layouts/warga.php`](file:///d:/vibe-coding/sipelayan-desa/app/Views/layouts/warga.php).

---

## 🧪 Hasil Verifikasi & Pengujian

Seluruh alur telah divalidasi dan diuji:

| Test Case | Hasil | Keterangan |
| :--- | :---: | :--- |
| **Migrasi Database** | ✅ PASSED | Batch 7: `CreateInformasiDesaTable` sukses diterapkan. |
| **Linting Sintaks PHP** | ✅ PASSED | Bebas syntax error di semua controller, model, view, migration. |
| **Login Warga** | ✅ PASSED | Masuk dengan akun `masri@gmail.com` berhasil ke `/warga/dashboard`. |
| **Card Pengumuman Warga** | ✅ PASSED | Card tampil dinamis dengan badge sifat, tanggal, dan nomor surat. |
| **Modal Quick View** | ✅ PASSED | Tombol *Baca Selengkapnya* memicu modal `#modalPengumuman` berformat Kop Surat resmi. |
| **Arsip Pengumuman Warga** | ✅ PASSED | Halaman `/warga/pengumuman` menampilkan daftar dengan filter & pencarian. |
| **Login Operator Desa** | ✅ PASSED | Masuk dengan akun `yusmadi23@gmail.com` ke panel operator. |
| **CRUD Operator Index & Form**| ✅ PASSED | Stat cards, filter status, form buat pengumuman baru berfungsi. |
| **Entri Pengumuman Baru** | ✅ PASSED | Operator berhasil menyimpan pengumuman baru (BLT Triwulan III), dan data **seketika tampil dinamis di dashboard Warga**. |

---

## 📌 Cara Mencoba Langsung

Server development telah aktif di: `http://localhost:8080`

1. **Akses Sebagai Warga**:
   - URL: `http://localhost:8080/auth/login`
   - Akun: `masri@gmail.com` / `12345678`
   - Buka menu **Dashboard Warga** (`/warga/dashboard`).
   - Perhatikan card **"Pengumuman Resmi"** di bagian bawah dan klik tombol **"Baca Selengkapnya"**.
   - Coba juga menu sidebar **"Pengumuman Gampong"** untuk melihat arsip lengkap.

2. **Akses Sebagai Operator Desa (CRUD)**:
   - URL: `http://localhost:8080/auth/login`
   - Akun: `yusmadi23@gmail.com` / `12345678`
   - Buka menu sidebar **"Pengumuman Gampong"** (`/operator/pengumuman`).
   - Coba tombol **"Buat Pengumuman Baru"**, ubah data, ganti status terbit/draft, atau pratinjau dokumen.
