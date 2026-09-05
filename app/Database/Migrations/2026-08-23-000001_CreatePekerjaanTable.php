<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePekerjaanTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('pekerjaan')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'unique'     => true,
                ],
                'keterangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('pekerjaan', true);

            // Default standard list of jobs (Kemendagri / BPS)
            $defaultJobs = [
                'Belum / Tidak Bekerja',
                'Mengurus Rumah Tangga',
                'Pelajar / Mahasiswa',
                'Pensiunan',
                'Pegawai Negeri Sipil (PNS)',
                'Tentara Nasional Indonesia (TNI)',
                'Kepolisian RI (POLRI)',
                'Petani / Pekebun',
                'Peternak',
                'Nelayan / Perikanan',
                'Industri',
                'Konstruksi',
                'Perdagangan',
                'Karyawan Swasta',
                'Karyawan BUMN',
                'Karyawan BUMD',
                'Karyawan Honorer',
                'Buruh Harian Lepas',
                'Buruh Tani / Perkebunan',
                'Buruh Nelayan / Perikanan',
                'Buruh Peternakan',
                'Pembantu Rumah Tangga',
                'Tukang Cukur',
                'Tukang Listrik',
                'Tukang Batu',
                'Tukang Kayu',
                'Tukang Sol Sepatu',
                'Tukang Las / Pandai Besi',
                'Tukang Jahit',
                'Penata Rias',
                'Penata Busana',
                'Penata Rambut',
                'Mekanik',
                'Seniman',
                'Tabib',
                'Paraji',
                'Perancang Busana',
                'Penterjemah',
                'Imam Masjid',
                'Pendeta',
                'Pastor',
                'Wartawan',
                'Ustadz / Mubaligh',
                'Juru Masak',
                'Dosen',
                'Guru',
                'Pilot',
                'Pengacara',
                'Notaris',
                'Arsitek',
                'Akuntan',
                'Konsultan',
                'Dokter',
                'Bidan',
                'Perawat',
                'Apoteker',
                'Psikiater / Psikolog',
                'Pelaut',
                'Sopir',
                'Pedagang',
                'Perangkat Desa',
                'Kepala Desa',
                'Biarawati',
                'Wiraswasta',
                'Lainnya',
            ];

            $now = date('Y-m-d H:i:s');
            $insertData = [];
            foreach ($defaultJobs as $job) {
                $insertData[] = [
                    'nama'       => $job,
                    'keterangan' => 'Standar Klasifikasi Pekerjaan',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($insertData)) {
                $this->db->table('pekerjaan')->insertBatch($insertData);
            }

            // Sync any existing occupation from penduduk table
            if ($this->db->tableExists('penduduk')) {
                $existingInPenduduk = $this->db->table('penduduk')
                    ->select('pekerjaan')
                    ->where('pekerjaan !=', '')
                    ->where('pekerjaan IS NOT NULL')
                    ->groupBy('pekerjaan')
                    ->get()
                    ->getResultArray();

                foreach ($existingInPenduduk as $row) {
                    $jobName = trim($row['pekerjaan']);
                    if ($jobName !== '') {
                        $check = $this->db->table('pekerjaan')->where('nama', $jobName)->countAllResults();
                        if ($check === 0) {
                            $this->db->table('pekerjaan')->insert([
                                'nama'       => $jobName,
                                'keterangan' => 'Dari Data Penduduk',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('pekerjaan', true);
    }
}
