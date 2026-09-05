<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $this->call('ProvinsiSeeder');
        $this->call('KabupatenSeeder');
        $this->call('KecamatanSeeder');
        $this->call('DesaSeeder');

        $db = \Config\Database::connect();

        // 2. DESA
        $db->table('desa')->ignore(true)->insert([
            'id'               => 1,
            'kabupaten_id'     => 1,
            'kode_kemendagri'  => '33.02.01.2001',
            'nama_desa'        => 'Sukamaju',
            'nama_kecamatan'   => 'Purwokerto Selatan',
            'nama_kepala_desa' => 'Bambang Sudarsono, S.Sos',
            'nip_kepala_desa'  => '197508122000031002',
            'alamat_kantor'    => 'Jl. Raya Sukamaju No. 45, Banyumas',
            'telepon'          => '0281-635123',
            'email'            => 'desa@sukamaju.go.id',
            'whatsapp_kades'   => '6281234567890',
            'is_active'        => 1,
            'tenant_slug'      => 'sukamaju',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // 3. ROLES
        $roles = [
            ['id' => 1, 'name' => 'Super Administrator', 'slug' => 'super_admin', 'level' => 1, 'description' => 'Administrator pusat/kabupaten'],
            ['id' => 2, 'name' => 'Admin Desa',          'slug' => 'admin_desa',  'level' => 2, 'description' => 'Administrator desa & approval'],
            ['id' => 3, 'name' => 'Operator Desa',       'slug' => 'operator',    'level' => 3, 'description' => 'Petugas operasional harian desa'],
            ['id' => 4, 'name' => 'Warga',               'slug' => 'warga',       'level' => 4, 'description' => 'Masyarakat pengguna layanan desa'],
        ];

        foreach ($roles as $role) {
            $existing = $db->table('roles')->where('id', $role['id'])->get()->getRow();
            if (! $existing) {
                $db->table('roles')->insert($role);
            }
        }

        // 4. USERS
        $defaultPassword = password_hash('Admin123!', PASSWORD_BCRYPT);
        $wargaPassword   = password_hash('Warga123!', PASSWORD_BCRYPT);
        $operatorPassword = password_hash('Operator123!', PASSWORD_BCRYPT);

        $users = [
            [
                'id'              => 1,
                'village_id'      => null, // Super Admin lintas desa
                'role_id'         => 1,
                'nik'             => null,
                'nama_lengkap'    => 'Super Administrator Utama',
                'email'           => 'superadmin@sipelayan.desa',
                'no_hp'           => '081100000001',
                'password_hash'   => $defaultPassword,
                'is_verified'     => 1,
                'is_nik_verified' => 1,
                'is_active'       => 1,
                'is_banned'       => 0,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 2,
                'village_id'      => 1,
                'role_id'         => 2,
                'nik'             => '3302010101750001',
                'nama_lengkap'    => 'Admin Desa Sukamaju',
                'email'           => 'admindesa@sukamaju.desa',
                'no_hp'           => '081100000002',
                'password_hash'   => $defaultPassword,
                'is_verified'     => 1,
                'is_nik_verified' => 1,
                'is_active'       => 1,
                'is_banned'       => 0,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 3,
                'village_id'      => 1,
                'role_id'         => 3,
                'nik'             => '3302010101880002',
                'nama_lengkap'    => 'Operator Desa Sukamaju',
                'email'           => 'operator@sukamaju.desa',
                'no_hp'           => '081100000003',
                'password_hash'   => $operatorPassword,
                'is_verified'     => 1,
                'is_nik_verified' => 1,
                'is_active'       => 1,
                'is_banned'       => 0,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 4,
                'village_id'      => 1,
                'role_id'         => 4,
                'nik'             => '3302010101900001',
                'nama_lengkap'    => 'Budi Santoso (Warga)',
                'email'           => 'warga@sukamaju.desa',
                'no_hp'           => '081234567891',
                'password_hash'   => $wargaPassword,
                'is_verified'     => 1,
                'is_nik_verified' => 1,
                'is_active'       => 1,
                'is_banned'       => 0,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $user) {
            $existing = $db->table('users')->where('email', $user['email'])->get()->getRow();
            if ($existing) {
                $updateData = $user;
                unset($updateData['id']);
                $db->table('users')->where('email', $user['email'])->update($updateData);
            } else {
                $db->table('users')->insert($user);
            }
        }

        // 5. DATA PENDUDUK WARGA
        $penduduk = [
            'id'                 => 1,
            'village_id'         => 1,
            'user_id'            => 4,
            'nik'                => '3302010101900001',
            'no_kk'              => '3302010101000001',
            'nama_lengkap'       => 'Budi Santoso',
            'tempat_lahir'       => 'Banyumas',
            'tanggal_lahir'      => '1990-01-01',
            'jenis_kelamin'      => 'L',
            'golongan_darah'     => 'O',
            'agama'              => 'Islam',
            'status_perkawinan'  => 'Kawin',
            'pekerjaan'          => 'Wiraswasta',
            'pendidikan'         => 'S1',
            'kewarganegaraan'    => 'WNI',
            'status_hubungan_kk' => 'Kepala Keluarga',
            'rt'                 => '001',
            'rw'                 => '002',
            'dusun'              => 'Dusun Krajan',
            'alamat_lengkap'     => 'Jl. Melati No. 12 RT 01 RW 02, Desa Sukamaju',
            'status_penduduk'    => 'Tetap',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $existingPenduduk = $db->table('penduduk')->where('nik', $penduduk['nik'])->get()->getRow();
        if ($existingPenduduk) {
            $updatePenduduk = $penduduk;
            unset($updatePenduduk['id']);
            $db->table('penduduk')->where('nik', $penduduk['nik'])->update($updatePenduduk);
        } else {
            $db->table('penduduk')->insert($penduduk);
        }

        // 6. JENIS SURAT
        $jenisSurat = [
            [
                'id'             => 1,
                'kode'           => 'SKTM',
                'nama'           => 'Surat Keterangan Tidak Mampu',
                'deskripsi'      => 'Surat keterangan untuk keperluan beasiswa, keringanan biaya berobat, atau bantuan sosial.',
                'schema_form'    => json_encode([
                    ['name' => 'keperluan', 'label' => 'Keperluan Pengajuan', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Pengajuan Beasiswa KIP Kuliah'],
                    ['name' => 'penghasilan_perbulan', 'label' => 'Estimasi Penghasilan Bulanan (Rp)', 'type' => 'number', 'required' => true, 'placeholder' => '1000000'],
                    ['name' => 'jumlah_tanggungan', 'label' => 'Jumlah Tanggungan Keluarga', 'type' => 'number', 'required' => true, 'placeholder' => '3'],
                ]),
                'syarat_dokumen' => json_encode(['KTP Asli', 'Kartu Keluarga', 'Surat Pengantar RT/RW']),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 2,
                'kode'           => 'SKD',
                'nama'           => 'Surat Keterangan Domisili',
                'deskripsi'      => 'Surat keterangan domisili kependudukan di wilayah desa.',
                'schema_form'    => json_encode([
                    ['name' => 'alamat_asal', 'label' => 'Alamat Asal (KTP)', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Alamat sesuai KTP atau tulis Sesuai Domisili'],
                    ['name' => 'keperluan', 'label' => 'Keperluan Surat', 'type' => 'text', 'required' => false, 'placeholder' => 'Contoh: pengurusan administrasi atau keperluan penting lainnya'],
                ]),
                'syarat_dokumen' => json_encode(['KTP Asli', 'Kartu Keluarga', 'Surat Pengantar RT/RW']),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 3,
                'kode'           => 'SKU',
                'nama'           => 'Surat Keterangan Usaha',
                'deskripsi'      => 'Surat keterangan kepemilikan usaha mikro/kecil di desa.',
                'schema_form'    => json_encode([
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha / Toko', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Warung Berkah Santoso'],
                    ['name' => 'bidang_usaha', 'label' => 'Bidang Usaha', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Perdagangan Sembako'],
                    ['name' => 'lama_usaha', 'label' => 'Lama Berdiri Usaha', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: 3 Tahun'],
                    ['name' => 'alamat_usaha', 'label' => 'Lokasi Tempat Usaha', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Alamat lokasi usaha'],
                ]),
                'syarat_dokumen' => json_encode(['KTP Asli', 'Kartu Keluarga', 'Foto Tempat Usaha']),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 4,
                'kode'           => 'SKCK',
                'nama'           => 'Surat Pengantar SKCK',
                'deskripsi'      => 'Surat pengantar kelakuan baik untuk permohonan SKCK di Polsek/Polres.',
                'schema_form'    => json_encode([
                    ['name' => 'keperluan', 'label' => 'Keperluan SKCK', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Melamar Pekerjaan di PT XYZ'],
                ]),
                'syarat_dokumen' => json_encode(['KTP Asli', 'Kartu Keluarga', 'Pas Foto 4x6']),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 5,
                'kode'           => 'KERAMAIAN',
                'nama'           => 'Surat Izin Keramaian',
                'deskripsi'      => 'Surat permohonan izin penyelenggaraan keramaian/acara warga.',
                'schema_form'    => json_encode([
                    ['name' => 'dalam_rangka', 'label' => 'Dalam Rangka Kegiatan', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Resepsi Pernikahan / Khitanan'],
                    ['name' => 'tanggal_pelaksanaan', 'label' => 'Hari / Tanggal Pelaksanaan', 'type' => 'date', 'required' => true, 'placeholder' => 'Pilih tanggal pelaksanaan'],
                    ['name' => 'waktu_pelaksanaan', 'label' => 'Pukul / Waktu', 'type' => 'text', 'required' => true, 'placeholder' => '08.00 WIB s.d. selesai'],
                    ['name' => 'lokasi_kegiatan', 'label' => 'Tempat / Lokasi', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Alamat atau lokasi spesifik kegiatan'],
                    ['name' => 'jenis_hiburan', 'label' => 'Acara / Hiburan', 'type' => 'text', 'required' => true, 'placeholder' => 'Contoh: Orgen Tunggal / Wayang Kulit / Lainnya'],
                ]),
                'syarat_dokumen' => json_encode(['KTP Asli', 'Kartu Keluarga', 'Surat Pengantar RT/RW']),
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($jenisSurat as $js) {
            $existing = $db->table('jenis_surat')->where('kode', $js['kode'])->get()->getRow();
            if ($existing) {
                $updateJs = $js;
                unset($updateJs['id']);
                $db->table('jenis_surat')->where('kode', $js['kode'])->update($updateJs);
            } else {
                $db->table('jenis_surat')->ignore(true)->insert($js);
            }
        }

        // 7. PERMOHONAN SURAT
        $permohonanList = [
            [
                'id'                => 1,
                'village_id'        => 1,
                'jenis_surat_id'    => 2, // SKD
                'pemohon_id'        => 4, // Budi Santoso
                'no_permohonan'     => 'SPD-DS01-2026-00001',
                'data_form'         => json_encode(['tujuan' => 'Pembukaan rekening bank BCA', 'alamat_sekarang' => 'Jl. Melati No. 12 RT 01 RW 02, Desa Sukamaju']),
                'dokumen_syarat'    => json_encode(['ktp_budi.jpg', 'kk_budi.jpg']),
                'status'            => 'submitted',
                'catatan_operator'  => null,
                'catatan_admin'     => null,
                'priority'          => 'normal',
                'channel'           => 'online',
                'created_at'        => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at'        => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'id'                => 2,
                'village_id'        => 1,
                'jenis_surat_id'    => 1, // SKTM
                'pemohon_id'        => 4, // Budi Santoso
                'no_permohonan'     => 'SPD-DS01-2026-00002',
                'data_form'         => json_encode(['keperluan' => 'Pengajuan Beasiswa KIP Kuliah Anak', 'penghasilan_perbulan' => '1200000', 'jumlah_tanggungan' => '3']),
                'dokumen_syarat'    => json_encode(['ktp_budi.jpg', 'kk_budi.jpg', 'surat_rt.pdf']),
                'status'            => 'verified_operator',
                'catatan_operator'  => 'Berkas lengkap dan sesuai.',
                'catatan_admin'     => null,
                'operator_id'       => 3,
                'verified_at'       => date('Y-m-d H:i:s', strtotime('-2 days')),
                'priority'          => 'normal',
                'channel'           => 'online',
                'created_at'        => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'        => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'id'                => 3,
                'village_id'        => 1,
                'jenis_surat_id'    => 3, // SKU
                'pemohon_id'        => 4, // Budi Santoso
                'no_permohonan'     => 'SPD-DS01-2026-00003',
                'data_form'         => json_encode(['nama_usaha' => 'Warung Sembako Berkah', 'bidang_usaha' => 'Perdagangan Sembako', 'lama_usaha' => '2 Tahun', 'alamat_usaha' => 'Jl. Melati No. 12 RT 01 RW 02']),
                'dokumen_syarat'    => json_encode(['ktp_budi.jpg', 'kk_budi.jpg', 'foto_warung.jpg']),
                'status'            => 'completed',
                'catatan_operator'  => 'Berkas valid.',
                'catatan_admin'     => 'Disetujui dan ditandatangani secara digital.',
                'operator_id'       => 3,
                'admin_id'          => 2,
                'verified_at'       => date('Y-m-d H:i:s', strtotime('-1 day')),
                'approved_at'       => date('Y-m-d H:i:s', strtotime('-1 day')),
                'completed_at'      => date('Y-m-d H:i:s', strtotime('-1 day')),
                'no_surat_keluar'   => '470/025/DS-SKU/VIII/2026',
                'priority'          => 'normal',
                'channel'           => 'online',
                'created_at'        => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at'        => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
        ];

        foreach ($permohonanList as $p) {
            $existing = $db->table('permohonan_surat')->where('no_permohonan', $p['no_permohonan'])->get()->getRow();
            if ($existing) {
                $updateP = $p;
                unset($updateP['id']);
                $db->table('permohonan_surat')->where('no_permohonan', $p['no_permohonan'])->update($updateP);
            } else {
                $db->table('permohonan_surat')->ignore(true)->insert($p);
            }
        }
    }
}
