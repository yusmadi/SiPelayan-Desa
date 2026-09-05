<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaduanTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('pengaduan')) {
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
                'pelapor_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'no_tiket' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'default'    => 'Layanan Publik',
                ],
                'judul' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'isi_laporan' => [
                    'type' => 'TEXT',
                ],
                'lokasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'latitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,8',
                    'null'       => true,
                ],
                'longitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '11,8',
                    'null'       => true,
                ],
                'foto_paths' => [
                    'type' => 'JSON',
                    'null' => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['open', 'in_progress', 'resolved', 'closed', 'rejected'],
                    'default'    => 'open',
                ],
                'is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'tanggapan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'ditangani_oleh' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'resolved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
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
            $this->forge->addForeignKey('village_id', 'desa', 'id', 'RESTRICT', 'CASCADE');
            $this->forge->addForeignKey('pelapor_id', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('ditangani_oleh', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('pengaduan', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('pengaduan', true);
    }
}
