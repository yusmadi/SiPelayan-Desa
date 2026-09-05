-- ============================================================
-- SiPelayan-Desa Database Schema v1.0
-- Encoding: UTF8MB4 | Engine: InnoDB
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- TABEL WILAYAH
-- ------------------------------------------------------------

CREATE TABLE `provinsi` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `kode_kemendagri` VARCHAR(10)     NOT NULL UNIQUE COMMENT 'Kode resmi Kemendagri',
  `nama`            VARCHAR(100)    NOT NULL,
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_kode` (`kode_kemendagri`),
  INDEX `idx_provinsi_nama` (`nama`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master data provinsi';

-- ------------------------------------------------------------

CREATE TABLE `kabupaten` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `provinsi_id`     INT UNSIGNED    NULL,
  `kode_kemendagri` VARCHAR(10)     NOT NULL UNIQUE COMMENT 'Kode resmi Kemendagri',
  `nama`            VARCHAR(150)    NOT NULL,
  `provinsi`        VARCHAR(100)    NOT NULL,
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_kode` (`kode_kemendagri`),
  INDEX `idx_nama` (`nama`),
  INDEX `idx_kabupaten_provinsi_id` (`provinsi_id`),
  INDEX `idx_kabupaten_prov_nama` (`provinsi_id`, `nama`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master data kabupaten/kota';

-- ------------------------------------------------------------

CREATE TABLE `kecamatan` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `kabupaten_id`    INT UNSIGNED    NOT NULL,
  `kode_kemendagri` VARCHAR(15)     NOT NULL UNIQUE COMMENT 'Kode resmi Kemendagri',
  `nama`            VARCHAR(150)    NOT NULL,
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_kecamatan_kabupaten` FOREIGN KEY (`kabupaten_id`) REFERENCES `kabupaten`(`id`) ON UPDATE CASCADE,
  INDEX `idx_kabupaten` (`kabupaten_id`),
  INDEX `idx_kecamatan_nama` (`nama`),
  INDEX `idx_kecamatan_kab_nama` (`kabupaten_id`, `nama`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master data kecamatan';

-- ------------------------------------------------------------

CREATE TABLE `desa` (
  `id`                 INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `kabupaten_id`       INT UNSIGNED  NOT NULL,
  `kode_kemendagri`    VARCHAR(15)   NOT NULL UNIQUE,
  `nama_desa`          VARCHAR(150)  NOT NULL,
  `nama_kecamatan`     VARCHAR(150)  NOT NULL,
  `nama_kepala_desa`   VARCHAR(150)  NULL,
  `nip_kepala_desa`    VARCHAR(30)   NULL,
  `alamat_kantor`      TEXT          NULL,
  `telepon`            VARCHAR(20)   NULL,
  `email`              VARCHAR(100)  NULL,
  `whatsapp_kades`     VARCHAR(20)   NULL COMMENT 'Nomor WA Kepala Desa untuk fitur Tanya Kades',
  `website`            VARCHAR(255)  NULL,
  `logo_path`          VARCHAR(255)  NULL,
  `kode_pos`           VARCHAR(10)   NULL,
  `latitude`           DECIMAL(10,8) NULL,
  `longitude`          DECIMAL(11,8) NULL,
  `visi`               TEXT          NULL,
  `misi`               TEXT          NULL,
  `is_active`          TINYINT(1)    NOT NULL DEFAULT 1,
  `tenant_slug`        VARCHAR(100)  NOT NULL UNIQUE COMMENT 'Slug unik untuk subdomain/URL desa',
  `created_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_desa_kabupaten` FOREIGN KEY (`kabupaten_id`) REFERENCES `kabupaten`(`id`) ON UPDATE CASCADE,
  INDEX `idx_kabupaten` (`kabupaten_id`),
  INDEX `idx_desa_nama` (`nama_desa`),
  INDEX `idx_desa_kab_nama` (`kabupaten_id`, `nama_desa`),
  INDEX `idx_desa_kecamatan` (`nama_kecamatan`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_slug` (`tenant_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master data desa (setiap baris = satu tenant)';

-- ------------------------------------------------------------
-- TABEL RBAC
-- ------------------------------------------------------------

CREATE TABLE `roles` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)      NOT NULL UNIQUE,
  `slug`        VARCHAR(50)      NOT NULL UNIQUE,
  `description` VARCHAR(255)     NULL,
  `level`       TINYINT UNSIGNED NOT NULL COMMENT '1=SuperAdmin, 2=AdminDesa, 3=Operator, 4=Warga',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`name`, `slug`, `level`, `description`) VALUES
  ('Super Administrator', 'super_admin', 1, 'Administrator pusat/kabupaten, akses penuh lintas tenant'),
  ('Admin Desa',          'admin_desa',  2, 'Administrator pada satu desa, verifikasi dan approval'),
  ('Operator Desa',       'operator',    3, 'Petugas layanan harian kantor desa'),
  ('Warga',               'warga',       4, 'Masyarakat pengguna layanan desa');

-- ------------------------------------------------------------

CREATE TABLE `permissions` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module`      VARCHAR(60)       NOT NULL COMMENT 'Nama modul/fitur (e.g. surat, penduduk)',
  `action`      VARCHAR(60)       NOT NULL COMMENT 'Aksi (e.g. create, read, update, delete, approve)',
  `description` VARCHAR(255)      NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_module_action` (`module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------

CREATE TABLE `role_permissions` (
  `role_id`       TINYINT UNSIGNED  NOT NULL,
  `permission_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABEL PENGGUNA
-- ------------------------------------------------------------

CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `village_id`        INT UNSIGNED     NULL     COMMENT 'NULL = Super Admin (lintas desa)',
  `role_id`           TINYINT UNSIGNED NOT NULL,
  `nik`               VARCHAR(16)      NULL     COMMENT 'NIK KTP (khusus warga)',
  `nama_lengkap`      VARCHAR(150)     NOT NULL,
  `email`             VARCHAR(150)     NOT NULL UNIQUE,
  `no_hp`             VARCHAR(20)      NULL,
  `password_hash`     VARCHAR(255)     NOT NULL COMMENT 'Argon2id hash',
  `avatar_path`       VARCHAR(255)     NULL,
  `is_verified`       TINYINT(1)       NOT NULL DEFAULT 0  COMMENT 'Verifikasi email',
  `is_nik_verified`   TINYINT(1)       NOT NULL DEFAULT 0  COMMENT 'Verifikasi NIK oleh operator',
  `is_active`         TINYINT(1)       NOT NULL DEFAULT 1,
  `is_banned`         TINYINT(1)       NOT NULL DEFAULT 0,
  `ban_reason`        VARCHAR(255)     NULL,
  `email_verify_token` VARCHAR(64)     NULL,
  `password_reset_token` VARCHAR(64)   NULL,
  `password_reset_expires` DATETIME    NULL,
  `last_login_at`     DATETIME         NULL,
  `last_login_ip`     VARCHAR(45)      NULL     COMMENT 'IPv4/IPv6',
  `failed_login_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until`      DATETIME         NULL     COMMENT 'Akun terkunci sementara',
  `created_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`        DATETIME         NULL     COMMENT 'Soft delete',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_users_village` FOREIGN KEY (`village_id`) REFERENCES `desa`(`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role`    FOREIGN KEY (`role_id`)    REFERENCES `roles`(`id`),
  INDEX `idx_village_role`  (`village_id`, `role_id`),
  INDEX `idx_nik`           (`nik`),
  INDEX `idx_email`         (`email`),
  INDEX `idx_active`        (`is_active`, `deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master pengguna semua level (multi-tenant via village_id)';

-- ------------------------------------------------------------
-- TABEL PENDUDUK (Data Kependudukan)
-- ------------------------------------------------------------

CREATE TABLE `penduduk` (
  `id`               BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `village_id`       INT UNSIGNED     NOT NULL,
  `user_id`          BIGINT UNSIGNED  NULL     COMMENT 'Link ke akun warga jika sudah daftar',
  `nik`              VARCHAR(16)      NOT NULL,
  `no_kk`            VARCHAR(16)      NOT NULL,
  `nama_lengkap`     VARCHAR(150)     NOT NULL,
  `tempat_lahir`     VARCHAR(100)     NOT NULL,
  `tanggal_lahir`    DATE             NOT NULL,
  `jenis_kelamin`    ENUM('L','P')    NOT NULL,
  `golongan_darah`   ENUM('A','B','AB','O','A+','A-','B+','B-','AB+','AB-','O+','O-','Tidak Diketahui') NULL,
  `agama`            ENUM('Islam','Kristen Protestan','Kristen Katolik','Hindu','Buddha','Konghucu','Lainnya') NOT NULL,
  `status_perkawinan` ENUM('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NOT NULL DEFAULT 'Belum Kawin',
  `pekerjaan`        VARCHAR(100)     NULL,
  `pendidikan`       ENUM('Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3') NULL,
  `kewarganegaraan`  VARCHAR(5)       NOT NULL DEFAULT 'WNI',
  `status_hubungan_kk` ENUM('Kepala Keluarga','Istri','Anak','Menantu','Cucu','Orang Tua','Mertua','Famili Lain','Pembantu','Lainnya') NULL,
  `rt`               VARCHAR(5)       NULL,
  `rw`               VARCHAR(5)       NULL,
  `dusun`            VARCHAR(100)     NULL,
  `alamat_lengkap`   TEXT             NULL,
  `status_penduduk`  ENUM('Tetap','Sementara','Pindah','Meninggal') NOT NULL DEFAULT 'Tetap',
  `foto_ktp_path`    VARCHAR(255)     NULL  COMMENT 'Path foto KTP (enkripsi nama file)',
  `foto_kk_path`     VARCHAR(255)     NULL,
  `created_by`       BIGINT UNSIGNED  NULL,
  `updated_by`       BIGINT UNSIGNED  NULL,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`       DATETIME         NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_penduduk_village` FOREIGN KEY (`village_id`) REFERENCES `desa`(`id`),
  UNIQUE KEY `uq_nik_village` (`nik`, `village_id`),
  INDEX `idx_village_nik`    (`village_id`, `nik`),
  INDEX `idx_village_nokk`   (`village_id`, `no_kk`),
  INDEX `idx_village_status` (`village_id`, `status_penduduk`),
  INDEX `idx_nama`           (`nama_lengkap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data kependudukan per desa';

-- ------------------------------------------------------------
-- TABEL JENIS SURAT
-- ------------------------------------------------------------

CREATE TABLE `jenis_surat` (
  `id`           SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode`         VARCHAR(20)       NOT NULL UNIQUE,
  `nama`         VARCHAR(200)      NOT NULL,
  `deskripsi`    TEXT              NULL,
  `template_path` VARCHAR(255)     NULL  COMMENT 'Path template DOCX/PDF',
  `schema_form`  JSON              NOT NULL COMMENT 'Definisi form dinamis (field name, type, required, label)',
  `syarat_dokumen` JSON            NULL  COMMENT 'Daftar dokumen syarat yang perlu diupload',
  `is_active`    TINYINT(1)        NOT NULL DEFAULT 1,
  `created_at`   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master jenis surat (form dinamis via JSON schema)';

-- Seed data jenis surat
INSERT INTO `jenis_surat` (`kode`, `nama`, `schema_form`) VALUES
('DOMISILI',    'Surat Keterangan Domisili',              '{"fields":[{"name":"keperluan","type":"text","label":"Keperluan","required":true}]}'),
('SKTM',        'Surat Keterangan Tidak Mampu',           '{"fields":[{"name":"keperluan","type":"text","label":"Keperluan","required":true},{"name":"penghasilan","type":"number","label":"Penghasilan per Bulan","required":true}]}'),
('NIKAH',       'Surat Pengantar Nikah',                  '{"fields":[{"name":"nama_calon_pasangan","type":"text","label":"Nama Calon Pasangan","required":true},{"name":"tempat_menikah","type":"text","label":"Tempat Menikah","required":true}]}'),
('KEHILANGAN_KTP','Surat Keterangan Kehilangan KTP',      '{"fields":[{"name":"kronologi","type":"textarea","label":"Kronologi Kehilangan","required":true}]}'),
('KERAMAIAN',   'Surat Izin Keramaian',                   '{"fields":[{"name":"nama_acara","type":"text","label":"Nama Acara","required":true},{"name":"tanggal_acara","type":"date","label":"Tanggal Acara","required":true},{"name":"jumlah_peserta","type":"number","label":"Estimasi Peserta","required":true}]}'),
('KEMATIAN',    'Surat Keterangan Kematian',              '{"fields":[{"name":"nama_almarhum","type":"text","label":"Nama Almarhum","required":true},{"name":"tanggal_meninggal","type":"date","label":"Tanggal Meninggal","required":true}]}'),
('KEH_BUKU_BANK','Surat Keterangan Kehilangan Buku Bank', '{"fields":[{"name":"nama_bank","type":"text","label":"Nama Bank","required":true},{"name":"no_rekening","type":"text","label":"Nomor Rekening (samarkan)","required":true}]}'),
('GADAI',       'Surat Keterangan Gadai',                 '{"fields":[{"name":"objek_gadai","type":"text","label":"Objek Gadai","required":true},{"name":"nilai_gadai","type":"number","label":"Nilai Gadai (Rp)","required":true}]}'),
('JUAL_BELI',   'Surat Keterangan Jual Beli',             '{"fields":[{"name":"objek","type":"text","label":"Objek Jual Beli","required":true},{"name":"nama_pembeli","type":"text","label":"Nama Pembeli","required":true},{"name":"harga","type":"number","label":"Harga (Rp)","required":true}]}'),
('SEWA_TANAH',  'Surat Keterangan Sewa Tanah',            '{"fields":[{"name":"luas_tanah","type":"text","label":"Luas Tanah","required":true},{"name":"masa_sewa","type":"text","label":"Masa Sewa","required":true}]}'),
('GANTI_KERJA', 'Surat Keterangan Ganti Status Pekerjaan','{"fields":[{"name":"pekerjaan_lama","type":"text","label":"Pekerjaan Lama","required":true},{"name":"pekerjaan_baru","type":"text","label":"Pekerjaan Baru","required":true}]}'),
('WARISAN',     'Surat Keterangan Warisan',               '{"fields":[{"name":"nama_pewaris","type":"text","label":"Nama Pewaris","required":true},{"name":"objek_warisan","type":"textarea","label":"Objek Warisan","required":true}]}');

-- ------------------------------------------------------------
-- TABEL PERMOHONAN SURAT (CORE)
-- ------------------------------------------------------------

CREATE TABLE `permohonan_surat` (
  `id`              BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `village_id`      INT UNSIGNED      NOT NULL,
  `jenis_surat_id`  SMALLINT UNSIGNED NOT NULL,
  `pemohon_id`      BIGINT UNSIGNED   NOT NULL COMMENT 'User ID warga pemohon',
  `no_permohonan`   VARCHAR(30)       NOT NULL UNIQUE COMMENT 'Format: SPD-[KODE_DESA]-[YYYY]-[NNNNN]',
  `data_form`       JSON              NOT NULL COMMENT 'Data isian form dari warga (terenkripsi sensitive fields)',
  `dokumen_syarat`  JSON              NULL  COMMENT 'Array path file dokumen yang diupload',
  `status`          ENUM(
                      'draft',
                      'submitted',
                      'verified_operator',
                      'approved_admin',
                      'rejected',
                      'completed',
                      'cancelled'
                    ) NOT NULL DEFAULT 'draft',
  `catatan_operator` TEXT             NULL,
  `catatan_admin`    TEXT             NULL,
  `catatan_penolakan` TEXT            NULL,
  `operator_id`      BIGINT UNSIGNED  NULL  COMMENT 'Operator yang memproses',
  `admin_id`         BIGINT UNSIGNED  NULL  COMMENT 'Admin desa yang approve',
  `verified_at`      DATETIME         NULL,
  `approved_at`      DATETIME         NULL,
  `rejected_at`      DATETIME         NULL,
  `completed_at`     DATETIME         NULL,
  `surat_output_path` VARCHAR(255)    NULL  COMMENT 'Path file surat yang sudah diterbitkan',
  `no_surat_keluar`  VARCHAR(100)     NULL  COMMENT 'Nomor surat resmi desa',
  `ttd_digital_hash` VARCHAR(64)      NULL  COMMENT 'Hash untuk verifikasi keaslian surat',
  `priority`         ENUM('normal','urgent') NOT NULL DEFAULT 'normal',
  `channel`          ENUM('online','offline') NOT NULL DEFAULT 'online',
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`       DATETIME         NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ps_village`   FOREIGN KEY (`village_id`)     REFERENCES `desa`(`id`),
  CONSTRAINT `fk_ps_jenis`     FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat`(`id`),
  CONSTRAINT `fk_ps_pemohon`   FOREIGN KEY (`pemohon_id`)     REFERENCES `users`(`id`),
  CONSTRAINT `fk_ps_operator`  FOREIGN KEY (`operator_id`)    REFERENCES `users`(`id`),
  CONSTRAINT `fk_ps_admin`     FOREIGN KEY (`admin_id`)       REFERENCES `users`(`id`),
  -- Composite index untuk query monitoring & dashboard desa
  INDEX `idx_village_status_created` (`village_id`, `status`, `created_at`),
  INDEX `idx_pemohon`                (`pemohon_id`),
  INDEX `idx_no_permohonan`          (`no_permohonan`),
  INDEX `idx_village_jenis_status`   (`village_id`, `jenis_surat_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Permohonan surat oleh warga — tabel inti multi-tenant';

-- ------------------------------------------------------------
-- TABEL AUDIT LOG
-- ------------------------------------------------------------

CREATE TABLE `audit_logs` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `village_id`  INT UNSIGNED     NULL,
  `user_id`     BIGINT UNSIGNED  NULL,
  `role_slug`   VARCHAR(50)      NULL,
  `action`      VARCHAR(100)     NOT NULL COMMENT 'e.g. USER_LOGIN, SURAT_APPROVE, PENDUDUK_CREATE',
  `module`      VARCHAR(60)      NOT NULL,
  `target_id`   BIGINT UNSIGNED  NULL  COMMENT 'ID record yang diubah',
  `target_type` VARCHAR(100)     NULL  COMMENT 'Nama tabel/model',
  `old_data`    JSON             NULL  COMMENT 'Snapshot sebelum perubahan',
  `new_data`    JSON             NULL  COMMENT 'Snapshot setelah perubahan',
  `ip_address`  VARCHAR(45)      NULL,
  `user_agent`  VARCHAR(500)     NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_village_module`  (`village_id`, `module`),
  INDEX `idx_user_action`     (`user_id`, `action`),
  INDEX `idx_created`         (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit log seluruh aktivitas sistem (partisi per tahun direkomendasikan)'
  PARTITION BY RANGE (YEAR(`created_at`)) (
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p2027 VALUES LESS THAN (2028),
    PARTITION pmax  VALUES LESS THAN MAXVALUE
  );

-- ------------------------------------------------------------
-- TABEL PENGADUAN PUBLIK
-- ------------------------------------------------------------

CREATE TABLE `pengaduan` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `village_id`  INT UNSIGNED     NOT NULL,
  `pelapor_id`  BIGINT UNSIGNED  NULL  COMMENT 'NULL jika anonim',
  `no_tiket`    VARCHAR(25)      NOT NULL UNIQUE,
  `kategori`    ENUM('Infrastruktur','Layanan Publik','Keamanan','Lingkungan','Lainnya') NOT NULL,
  `judul`       VARCHAR(255)     NOT NULL,
  `isi_laporan` TEXT             NOT NULL,
  `lokasi`      VARCHAR(255)     NULL,
  `latitude`    DECIMAL(10,8)    NULL,
  `longitude`   DECIMAL(11,8)    NULL,
  `foto_paths`  JSON             NULL  COMMENT 'Array path foto bukti (max 5)',
  `status`      ENUM('open','in_progress','resolved','closed','rejected') NOT NULL DEFAULT 'open',
  `is_anonymous` TINYINT(1)      NOT NULL DEFAULT 0,
  `tanggapan`   TEXT             NULL  COMMENT 'Tanggapan resmi dari desa',
  `ditangani_oleh` BIGINT UNSIGNED NULL,
  `resolved_at` DATETIME         NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pengaduan_village` FOREIGN KEY (`village_id`) REFERENCES `desa`(`id`),
  INDEX `idx_village_status` (`village_id`, `status`),
  INDEX `idx_no_tiket`       (`no_tiket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABEL INFORMASI DESA (Berita, Pengumuman, APBDes)
-- ------------------------------------------------------------

CREATE TABLE `informasi_desa` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `village_id`  INT UNSIGNED     NOT NULL,
  `author_id`   BIGINT UNSIGNED  NOT NULL,
  `kategori`    ENUM('Berita','Pengumuman','APBDes','Transparansi','Agenda') NOT NULL,
  `judul`       VARCHAR(300)     NOT NULL,
  `slug`        VARCHAR(350)     NOT NULL,
  `konten`      LONGTEXT         NOT NULL,
  `thumbnail_path` VARCHAR(255)  NULL,
  `lampiran_path`  VARCHAR(255)  NULL  COMMENT 'PDF laporan APBDes dll',
  `tahun_anggaran` YEAR          NULL  COMMENT 'Untuk kategori APBDes',
  `total_apbdes`   BIGINT        NULL  COMMENT 'Total anggaran (Rp)',
  `is_published`   TINYINT(1)    NOT NULL DEFAULT 0,
  `published_at`   DATETIME      NULL,
  `views`          INT UNSIGNED  NOT NULL DEFAULT 0,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`     DATETIME      NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_info_village` FOREIGN KEY (`village_id`) REFERENCES `desa`(`id`),
  UNIQUE KEY `uq_village_slug` (`village_id`, `slug`),
  INDEX `idx_village_kategori_published` (`village_id`, `kategori`, `is_published`),
  FULLTEXT INDEX `ft_judul_konten` (`judul`, `konten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABEL TANYA KADES
-- ------------------------------------------------------------

CREATE TABLE `tanya_kades` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `village_id`   INT UNSIGNED    NOT NULL,
  `penanya_id`   BIGINT UNSIGNED NULL,
  `nama_penanya` VARCHAR(150)    NULL  COMMENT 'Nama jika anonim',
  `pertanyaan`   TEXT            NOT NULL,
  `jawaban`      TEXT            NULL,
  `is_public`    TINYINT(1)      NOT NULL DEFAULT 0  COMMENT '1=tampil di portal publik',
  `is_anonymous` TINYINT(1)      NOT NULL DEFAULT 0,
  `wa_sent`      TINYINT(1)      NOT NULL DEFAULT 0  COMMENT 'Apakah sudah dikirim ke WA Kades',
  `wa_sent_at`   DATETIME        NULL,
  `status`       ENUM('pending','answered','archived') NOT NULL DEFAULT 'pending',
  `dijawab_oleh` BIGINT UNSIGNED NULL,
  `answered_at`  DATETIME        NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tanya_village` FOREIGN KEY (`village_id`) REFERENCES `desa`(`id`),
  INDEX `idx_village_status` (`village_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABEL NOTIFIKASI
-- ------------------------------------------------------------

CREATE TABLE `notifikasi` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `village_id`  INT UNSIGNED    NOT NULL,
  `judul`       VARCHAR(255)    NOT NULL,
  `pesan`       TEXT            NOT NULL,
  `tipe`        VARCHAR(50)     NOT NULL COMMENT 'surat_update, pengaduan_update, info_desa, etc.',
  `ref_id`      BIGINT UNSIGNED NULL  COMMENT 'ID referensi terkait',
  `ref_type`    VARCHAR(100)    NULL,
  `is_read`     TINYINT(1)      NOT NULL DEFAULT 0,
  `read_at`     DATETIME        NULL,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_read`    (`user_id`, `is_read`),
  INDEX `idx_village`      (`village_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
