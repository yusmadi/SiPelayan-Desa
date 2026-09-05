<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToDesaAndKabupaten extends Migration
{
    public function up()
    {
        // 1. Tambah index pada tabel desa jika belum ada
        if ($this->db->tableExists('desa')) {
            // Index nama_desa untuk akselerasi ORDER BY dan sorting pagination
            $this->db->query("CREATE INDEX `idx_desa_nama` ON `desa` (`nama_desa`)");
            
            // Composite index untuk filter kabupaten + sorting nama_desa
            $this->db->query("CREATE INDEX `idx_desa_kab_nama` ON `desa` (`kabupaten_id`, `nama_desa`)");
            
            // Index nama_kecamatan untuk pencarian
            $this->db->query("CREATE INDEX `idx_desa_kecamatan` ON `desa` (`nama_kecamatan`)");
        }

        // 2. Tambah index pada tabel kabupaten jika belum ada
        if ($this->db->tableExists('kabupaten')) {
            $this->db->query("CREATE INDEX `idx_kabupaten_nama` ON `kabupaten` (`nama`)");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('desa')) {
            $this->db->query("DROP INDEX `idx_desa_nama` ON `desa`");
            $this->db->query("DROP INDEX `idx_desa_kab_nama` ON `desa`");
            $this->db->query("DROP INDEX `idx_desa_kecamatan` ON `desa`");
        }

        if ($this->db->tableExists('kabupaten')) {
            $this->db->query("DROP INDEX `idx_kabupaten_nama` ON `kabupaten`");
        }
    }
}
