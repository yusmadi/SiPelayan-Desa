<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransparansiColumnsToInformasiDesa extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('periode_anggaran', 'informasi_desa')) {
            $fields['periode_anggaran'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Tahap I',
                'after'      => 'nomor_pengumuman',
            ];
        }

        if (! $this->db->fieldExists('total_pendapatan', 'informasi_desa')) {
            $fields['total_pendapatan'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'total_apbdes',
            ];
        }

        if (! $this->db->fieldExists('realisasi_pendapatan', 'informasi_desa')) {
            $fields['realisasi_pendapatan'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'total_pendapatan',
            ];
        }

        if (! $this->db->fieldExists('total_belanja', 'informasi_desa')) {
            $fields['total_belanja'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'realisasi_pendapatan',
            ];
        }

        if (! $this->db->fieldExists('realisasi_belanja', 'informasi_desa')) {
            $fields['realisasi_belanja'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'total_belanja',
            ];
        }

        if (! $this->db->fieldExists('total_pembiayaan', 'informasi_desa')) {
            $fields['total_pembiayaan'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'realisasi_belanja',
            ];
        }

        if (! $this->db->fieldExists('rincian_anggaran', 'informasi_desa')) {
            $fields['rincian_anggaran'] = [
                'type'  => 'JSON',
                'null'  => true,
                'after' => 'total_pembiayaan',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('informasi_desa', $fields);
        }

        // Seed initial sample APBDes / APBG data for active villages
        $this->seedInitialAnggaran();
    }

    private function seedInitialAnggaran()
    {
        $db = \Config\Database::connect();
        
        $users = $db->table('users')
            ->whereIn('role_id', [2, 3])
            ->where('village_id IS NOT NULL')
            ->get()->getResultArray();

        $now = date('Y-m-d H:i:s');
        $tahun = (int) date('Y');

        $seededVillages = [];
        foreach ($users as $user) {
            $vId = (int) $user['village_id'];
            if (isset($seededVillages[$vId])) {
                continue;
            }
            $seededVillages[$vId] = true;

            $isAceh = function_exists('is_aceh') ? is_aceh($vId) : false;
            $istilah = $isAceh ? 'APBG' : 'APBDes';
            $sebutan = $isAceh ? 'Gampong' : 'Desa';

            // Rincian Belanja 5 Bidang Standar Permendagri 20/2018
            $rincianBelanja = [
                'bidang_1' => [
                    'nama'      => 'Penyelenggaraan Pemerintahan ' . $sebutan,
                    'anggaran'  => 325000000,
                    'realisasi' => 195000000,
                    'keterangan'=> 'Penghasilan tetap aparatur, operasional kantor, BPD/Tuha Peut, dan tata praja.',
                ],
                'bidang_2' => [
                    'nama'      => 'Pelaksanaan Pembangunan ' . $sebutan,
                    'anggaran'  => 580000000,
                    'realisasi' => 377000000,
                    'keterangan'=> 'Pembangunan jalan rabat beton, drainase pemukiman, dan sarana posyandu.',
                ],
                'bidang_3' => [
                    'nama'      => 'Pembinaan Kemasyarakatan',
                    'anggaran'  => 95000000,
                    'realisasi' => 57000000,
                    'keterangan'=> 'Kegiatan keagamaan/pengajian, kepemudaan, olahraga, dan seni budaya lokal.',
                ],
                'bidang_4' => [
                    'nama'      => 'Pemberdayaan Masyarakat',
                    'anggaran'  => 140000000,
                    'realisasi' => 84000000,
                    'keterangan'=> 'Pelatihan UMKM, ketahanan pangan hewani/nabati, dan bantuan bibit pertanian.',
                ],
                'bidang_5' => [
                    'nama'      => 'Penanggulangan Bencana & Mendesak',
                    'anggaran'  => 110000000,
                    'realisasi' => 77000000,
                    'keterangan'=> 'Penyaluran BLT Dana ' . $sebutan . ' dan antisipasi tanggap darurat bencana.',
                ],
            ];

            $rincianPendapatan = [
                'pades'          => ['nama' => 'Pendapatan Asli ' . $sebutan, 'anggaran' => 35000000, 'realisasi' => 28000000],
                'dana_desa'      => ['nama' => 'Dana ' . $sebutan . ' (DDS)', 'anggaran' => 780000000, 'realisasi' => 520000000],
                'add'            => ['nama' => 'Alokasi Dana ' . $sebutan . ' (ADD)', 'anggaran' => 380000000, 'realisasi' => 228000000],
                'bagi_hasil'     => ['nama' => 'Bagi Hasil Pajak & Retribusi', 'anggaran' => 45000000, 'realisasi' => 31500000],
                'lain_lain'      => ['nama' => 'Pendapatan Lain-lain Sah', 'anggaran' => 10000000, 'realisasi' => 5000000],
            ];

            $totalAnggaranBelanja  = 1250000000; // Rp 1,25 Miliar
            $totalRealisasiBelanja = 790000000;  // Rp 790 Juta (~63.2%)
            $totalAnggaranPendapatan  = 1250000000;
            $totalRealisasiPendapatan = 812500000;
            $totalPembiayaan = 25000000; // SILPA tahun sebelumnya

            $rincianFull = [
                'pendapatan' => $rincianPendapatan,
                'belanja'    => $rincianBelanja,
                'pembiayaan' => [
                    'penerimaan' => 25000000,
                    'pengeluaran' => 0,
                    'keterangan' => 'Sisa Lebih Perhitungan Anggaran (SiLPA) Tahun Anggaran Sebelumnya.',
                ],
            ];

            $data = [
                'village_id'           => $vId,
                'author_id'            => (int) $user['id'],
                'kategori'             => 'APBDes',
                'nomor_pengumuman'     => 'PERDES-' . $tahun . '/01/' . $vId,
                'periode_anggaran'     => 'Tahap I',
                'sifat'                => 'Biasa',
                'judul'                => 'Laporan Realisasi ' . $istilah . ' ' . $sebutan . ' Tahun Anggaran ' . $tahun . ' (Tahap I)',
                'slug'                 => 'laporan-realisasi-' . strtolower($istilah) . '-' . $tahun . '-tahap-1-' . $vId,
                'ringkasan'            => 'Publikasi transparansi dan akuntabilitas pengelolaan anggaran pendapatan serta belanja ' . strtolower($sebutan) . ' periode Tahap I Tahun Anggaran ' . $tahun . '.',
                'konten'               => "<p>Sebagai wujud komitmen keterbukaan informasi publik dan akuntabilitas tata kelola keuangan, Pemerintah {$sebutan} mempublikasikan Laporan Realisasi {$istilah} Tahun Anggaran {$tahun} periode Tahap I.</p>\n\n<p>Penggunaan anggaran difokuskan pada pemenuhan layanan dasar aparatur, pembangunan fisik infrastruktur pertanian dan pemukiman, program ketahanan pangan, serta penyaluran bantuan langsung tunai kepada warga yang berhak.</p>\n\n<p>Masyarakat dipersilakan untuk mempelajari rincian realisasi belanja pada setiap bidang demi kemajuan dan kesejahteraan bersama.</p>",
                'thumbnail_path'       => null,
                'lampiran_path'        => null,
                'lampiran_nama'        => null,
                'tahun_anggaran'       => $tahun,
                'total_apbdes'         => $totalAnggaranBelanja,
                'total_pendapatan'     => $totalAnggaranPendapatan,
                'realisasi_pendapatan' => $totalRealisasiPendapatan,
                'total_belanja'        => $totalAnggaranBelanja,
                'realisasi_belanja'    => $totalRealisasiBelanja,
                'total_pembiayaan'     => $totalPembiayaan,
                'rincian_anggaran'     => json_encode($rincianFull),
                'is_published'         => 1,
                'published_at'         => $now,
                'views'                => 45,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];

            $db->table('informasi_desa')->insert($data);
        }
    }

    public function down()
    {
        $columns = [
            'periode_anggaran',
            'total_pendapatan',
            'realisasi_pendapatan',
            'total_belanja',
            'realisasi_belanja',
            'total_pembiayaan',
            'rincian_anggaran',
        ];

        foreach ($columns as $col) {
            if ($this->db->fieldExists($col, 'informasi_desa')) {
                $this->forge->dropColumn('informasi_desa', $col);
            }
        }
    }
}
