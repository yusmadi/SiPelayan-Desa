<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProvinsiKecamatanTable extends Migration
{
    public function up()
    {
        // 1. Tabel Provinsi
        if (! $this->db->tableExists('provinsi')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'kode_kemendagri' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'unique'     => true,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
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
            $this->forge->createTable('provinsi', true);

            // Insert initial default provinsi
            $this->db->table('provinsi')->insertBatch([
                ['kode_kemendagri' => '33', 'nama' => 'Jawa Tengah', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['kode_kemendagri' => '32', 'nama' => 'Jawa Barat', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['kode_kemendagri' => '35', 'nama' => 'Jawa Timur', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['kode_kemendagri' => '31', 'nama' => 'DKI Jakarta', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['kode_kemendagri' => '34', 'nama' => 'DI Yogyakarta', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['kode_kemendagri' => '36', 'nama' => 'Banten', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ]);
        }

        // 2. Tambah provinsi_id ke tabel kabupaten jika belum ada
        if ($this->db->tableExists('kabupaten')) {
            $fields = $this->db->getFieldNames('kabupaten');
            if (! in_array('provinsi_id', $fields, true)) {
                $this->forge->addColumn('kabupaten', [
                    'provinsi_id' => [
                        'type'       => 'INT',
                        'constraint' => 10,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'id',
                    ],
                ]);

                // Sync initial kabupaten Banyumas ke provinsi Jawa Tengah (id: 1)
                $jateng = $this->db->table('provinsi')->where('kode_kemendagri', '33')->get()->getRowArray();
                if ($jateng) {
                    $this->db->table('kabupaten')->where('id', 1)->update(['provinsi_id' => $jateng['id']]);
                }
            }
        }

        // 3. Tabel Kecamatan
        if (! $this->db->tableExists('kecamatan')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'kabupaten_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                ],
                'kode_kemendagri' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '15',
                    'unique'     => true,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
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
            $this->forge->addForeignKey('kabupaten_id', 'kabupaten', 'id', 'RESTRICT', 'CASCADE');
            $this->forge->createTable('kecamatan', true);

            // Insert initial kecamatan Purwokerto Selatan
            $this->db->table('kecamatan')->insert([
                'kabupaten_id'    => 1,
                'kode_kemendagri' => '33.02.01',
                'nama'            => 'Purwokerto Selatan',
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('kecamatan', true);
        if ($this->db->tableExists('kabupaten')) {
            $fields = $this->db->getFieldNames('kabupaten');
            if (in_array('provinsi_id', $fields, true)) {
                $this->forge->dropColumn('kabupaten', 'provinsi_id');
            }
        }
        $this->forge->dropTable('provinsi', true);
    }
}
