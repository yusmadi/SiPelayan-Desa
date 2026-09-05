# Walkthrough: Setup SiPelayan-Desa

Platform **SiPelayan-Desa** telah berhasil dirancang dan di-*scaffold* secara penuh sesuai spesifikasi multi-tenant, dengan keamanan dan struktur CodeIgniter 4 (PHP 8.2+) yang solid.

Berikut adalah rangkuman dari apa yang telah diselesaikan:

## 1. Arsitektur Database & Proyek
* **Proyek CodeIgniter 4** telah di-install melalui Composer (`codeigniter4/appstarter`).
* **File `.env`** telah dikonfigurasi untuk environment development dan koneksi database MySQL.
* **DDL Skema Database ([sipd_schema.sql](file:///d:/vibe-coding/sipelayan-desa/database/sipd_schema.sql))** telah dibuat lengkap mencakup 14 tabel (wilayah, rbac, pengguna, layanan, informasi) dengan arsitektur **Shared DB + Shared Schema** menggunakan kolom `village_id` sebagai discriminator multi-tenant.

## 2. Isolasi Tenant (Multi-Tenant Security)
* Dibuat model abstrak **[BaseTenantModel.php](file:///d:/vibe-coding/sipelayan-desa/app/Models/BaseTenantModel.php)** yang secara otomatis meng-inject filter `WHERE village_id = ?` ke setiap query untuk mencegah kebocoran data antar desa.
* Dibuat **[TenantFilter.php](file:///d:/vibe-coding/sipelayan-desa/app/Filters/TenantFilter.php)** yang memvalidasi bahwa setiap pengguna hanya dapat mengakses *resource* milik desanya sendiri, serta mencatat jika ada upaya akses ilegal lintas-tenant.

## 3. Sistem RBAC & Keamanan (Filters)
* **[AuthFilter.php](file:///d:/vibe-coding/sipelayan-desa/app/Filters/AuthFilter.php)**: Memastikan pengguna sudah login, mengecek status ban, dan perlindungan *brute-force login*.
* **[RoleFilter.php](file:///d:/vibe-coding/sipelayan-desa/app/Filters/RoleFilter.php)**: Melindungi rute aplikasi berdasarkan 4 role: Super Admin, Admin Desa, Operator, dan Warga.
* **[RateLimitFilter.php](file:///d:/vibe-coding/sipelayan-desa/app/Filters/RateLimitFilter.php)**: Mengimplementasikan sistem *sliding-window* menggunakan Redis/Cache CI4 untuk mencegah spam dan DDOS.

## 4. Routing & Controller Inti
* **[Routes.php](file:///d:/vibe-coding/sipelayan-desa/app/Config/Routes.php)**: Konfigurasi routing telah di-group berdasarkan level akses dan dilengkapi dengan filter berantai (contoh: `['filter' => ['auth', 'role:warga', 'tenant']]`).
* Telah dibuat Controller skeleton utama seperti **[LoginController.php](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Auth/LoginController.php)**, **[RegisterController.php](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Auth/RegisterController.php)**, dan yang paling kritikal yaitu **[PermohonanController.php](file:///d:/vibe-coding/sipelayan-desa/app/Controllers/Warga/PermohonanController.php)** untuk melayani layanan pengajuan mandiri dari warga.

## 5. Layanan Pendukung (Services)
* Dibuat **[PermohonanService.php](file:///d:/vibe-coding/sipelayan-desa/app/Services/PermohonanService.php)** untuk menangani logika pembuatan permohonan surat termasuk auto-generate nomor antrean yang unik per-desa.
* Dibuat **[AuditService.php](file:///d:/vibe-coding/sipelayan-desa/app/Services/AuditService.php)** untuk merekam semua jejak perubahan data (Audit Trail).

---

> [!TIP]
> **Langkah Selanjutnya:**
> Untuk menjalankannya secara lokal, Anda bisa membuat database MySQL bernama `sipelayan_desa`, kemudian *import* skrip sql yang ada di folder `database/sipd_schema.sql`. Setelah itu, Anda bisa menjalankan *development server* dengan perintah `php spark serve`.

Semua spesifikasi arsitektur yang Anda instruksikan telah diwujudkan dalam struktur *boilerplate code* yang rapi dan siap dikembangkan lebih lanjut!
