<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInformasiDesaTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('informasi_desa')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'village_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                ],
                'author_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                ],
                'kategori' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Berita', 'Pengumuman', 'APBDes', 'Transparansi', 'Agenda'],
                    'default'    => 'Pengumuman',
                ],
                'nomor_pengumuman' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'sifat' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Biasa', 'Penting', 'Segera'],
                    'default'    => 'Biasa',
                ],
                'judul' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '300',
                ],
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '350',
                ],
                'ringkasan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'konten' => [
                    'type' => 'LONGTEXT',
                ],
                'thumbnail_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'lampiran_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'lampiran_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'tahun_anggaran' => [
                    'type'       => 'YEAR',
                    'null'       => true,
                ],
                'total_apbdes' => [
                    'type'       => 'BIGINT',
                    'null'       => true,
                ],
                'is_published' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'published_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'views' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('village_id', 'desa', 'id', 'RESTRICT', 'CASCADE');
            $this->forge->addForeignKey('author_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addKey(['village_id', 'kategori', 'is_published']);
            $this->forge->addKey(['village_id', 'slug']);
            $this->forge->createTable('informasi_desa', true);

            // Seed initial sample announcements for existing active villages
            $this->seedInitialPengumuman();
        }
    }

    private function seedInitialPengumuman()
    {
        $db = \Config\Database::connect();
        
        // Find existing operators or admins to assign as author
        $users = $db->table('users')
            ->whereIn('role_id', [2, 3])
            ->where('village_id IS NOT NULL')
            ->get()->getResultArray();

        $now = date('Y-m-d H:i:s');

        $seededVillages = [];
        foreach ($users as $user) {
            $vId = (int) $user['village_id'];
            if (isset($seededVillages[$vId])) {
                continue;
            }
            $seededVillages[$vId] = true;

            $data = [
                [
                    'village_id'       => $vId,
                    'author_id'        => (int) $user['id'],
                    'kategori'         => 'Pengumuman',
                    'nomor_pengumuman' => '005/PG/PEM-DES/' . date('m/Y'),
                    'sifat'            => 'Penting',
                    'judul'            => 'Jadwal Pelayanan Posyandu Balita & Lansia Bulan Ini',
                    'slug'             => 'jadwal-pelayanan-posyandu-balita-dan-lansia-' . $vId . '-' . time(),
                    'ringkasan'        => 'Pemberitahuan kepada seluruh warga mengenai jadwal pelayanan kesehatan Posyandu Balita dan Lansia yang akan diselenggarakan di Balai Pertemuan.',
                    'konten'           => "<p>Diberitahukan kepada seluruh warga masyarakat bahwa kegiatan rutin pelayanan Posyandu Balita dan Lansia tingkat gampong/desa untuk periode bulan ini akan diselenggarakan dengan rincian jadwal sebagai berikut:</p>\n\n<ul>\n<li><strong>Hari/Tanggal:</strong> Kamis, 15 " . date('F Y') . "</li>\n<li><strong>Waktu:</strong> Pukul 08.30 WIB s/d 12.00 WIB</li>\n<li><strong>Tempat:</strong> Balai Pertemuan Gampong / Kantor Keuchik</li>\n<li><strong>Agenda Layanan:</strong> Penimbangan balita, imunisasi dasar lengkap, pembagian makanan tambahan (PMT) gizi balita, dan pemeriksaan kesehatan berkala lansia (tensi darah & cek gula darah).</li>\n</ul>\n\n<p>Mengingat pentingnya pemantauan tumbuh kembang balita dan kesehatan lansia, kami menghimbau para orang tua dan keluarga agar dapat hadir tepat waktu dengan membawa Buku KIA/KMS masing-masing.</p>\n\n<p>Demikian pengumuman ini disampaikan untuk diketahui dan dilaksanakan sebagaimana mestinya. Atas perhatian dan kerja sama seluruh warga, kami ucapkan terima kasih.</p>",
                    'thumbnail_path'   => null,
                    'lampiran_path'    => null,
                    'lampiran_nama'    => null,
                    'is_published'     => 1,
                    'published_at'     => $now,
                    'views'            => 12,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'village_id'       => $vId,
                    'author_id'        => (int) $user['id'],
                    'kategori'         => 'Pengumuman',
                    'nomor_pengumuman' => '006/PG/PEM-DES/' . date('m/Y'),
                    'sifat'            => 'Segera',
                    'judul'            => 'Gotong Royong Kebersihan Lingkungan & Saluran Drainase',
                    'slug'             => 'gotong-royong-kebersihan-lingkungan-' . $vId . '-' . time(),
                    'ringkasan'        => 'Gerakan bersama seluruh elemen masyarakat dalam rangka pencegahan genangan air dan menjaga keasrian lingkungan gampong.',
                    'konten'           => "<p>Dalam rangka menyambut musim penghujan serta menjaga kebersihan dan kesehatan lingkungan pemukiman warga, Pemerintah Gampong/Desa mengundang partisipasi aktif seluruh warga untuk menghadiri kegiatan gotong royong massal.</p>\n\n<ul>\n<li><strong>Hari/Tanggal:</strong> Minggu, 18 " . date('F Y') . "</li>\n<li><strong>Waktu:</strong> Pukul 07.30 WIB s/d selesai</li>\n<li><strong>Titik Kumpul:</strong> Halaman Balai Gampong / Menasah</li>\n<li><strong>Fokus Kegiatan:</strong> Pembersihan saluran drainase utama, pemotongan rumput liar pinggir jalan, dan penataan tempat pembuangan sampah.</li>\n</ul>\n\n<p>Warga diharapkan membawa peralatan kebersihan mandiri seperti cangkul, sabit, atau karung sampah. Partisipasi dan kekompakan warga sangat menentukan kenyamanan dan kesehatan gampong kita bersama.</p>",
                    'thumbnail_path'   => null,
                    'lampiran_path'    => null,
                    'lampiran_nama'    => null,
                    'is_published'     => 1,
                    'published_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'views'            => 28,
                    'created_at'       => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'updated_at'       => date('Y-m-d H:i:s', strtotime('-2 days')),
                ]
            ];

            $db->table('informasi_desa')->insertBatch($data);
        }
    }

    public function down()
    {
        $this->forge->dropTable('informasi_desa', true);
    }
}
