<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToKecamatanKabupatenProvinsi extends Migration
{
    public function up()
    {
        // 1. Samakan collation tabel kecamatan ke utf8mb4_unicode_ci agar index match dengan desa
        if ($this->db->tableExists('kecamatan')) {
            $this->db->query("ALTER TABLE `kecamatan` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $existing = $this->getExistingIndexes('kecamatan');
            if (!in_array('idx_kecamatan_nama', $existing, true)) {
                $this->db->query("CREATE INDEX `idx_kecamatan_nama` ON `kecamatan` (`nama`)");
            }
            if (!in_array('idx_kecamatan_kab_nama', $existing, true)) {
                $this->db->query("CREATE INDEX `idx_kecamatan_kab_nama` ON `kecamatan` (`kabupaten_id`, `nama`)");
            }
        }

        // 2. Indeks pada tabel kabupaten
        if ($this->db->tableExists('kabupaten')) {
            $this->db->query("ALTER TABLE `kabupaten` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $fields = $this->db->getFieldNames('kabupaten');
            $existing = $this->getExistingIndexes('kabupaten');

            if (in_array('provinsi_id', $fields, true)) {
                if (!in_array('idx_kabupaten_provinsi_id', $existing, true)) {
                    $this->db->query("CREATE INDEX `idx_kabupaten_provinsi_id` ON `kabupaten` (`provinsi_id`)");
                }
                if (!in_array('idx_kabupaten_prov_nama', $existing, true)) {
                    $this->db->query("CREATE INDEX `idx_kabupaten_prov_nama` ON `kabupaten` (`provinsi_id`, `nama`)");
                }
            }
        }

        // 3. Indeks pada tabel provinsi
        if ($this->db->tableExists('provinsi')) {
            $this->db->query("ALTER TABLE `provinsi` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $existing = $this->getExistingIndexes('provinsi');
            if (!in_array('idx_provinsi_nama', $existing, true)) {
                $this->db->query("CREATE INDEX `idx_provinsi_nama` ON `provinsi` (`nama`)");
            }
        }
    }

    private function getExistingIndexes(string $table): array
    {
        $indexes = [];
        $res = $this->db->query("SHOW INDEX FROM `{$table}`")->getResultArray();
        foreach ($res as $r) {
            $indexes[] = $r['Key_name'];
        }
        return $indexes;
    }

    public function down()
    {
        if ($this->db->tableExists('kecamatan')) {
            $this->db->query("DROP INDEX `idx_kecamatan_nama` ON `kecamatan`");
            $this->db->query("DROP INDEX `idx_kecamatan_kab_nama` ON `kecamatan`");
        }

        if ($this->db->tableExists('kabupaten')) {
            $this->db->query("DROP INDEX `idx_kabupaten_provinsi_id` ON `kabupaten`");
            $this->db->query("DROP INDEX `idx_kabupaten_prov_nama` ON `kabupaten`");
        }

        if ($this->db->tableExists('provinsi')) {
            $this->db->query("DROP INDEX `idx_provinsi_nama` ON `provinsi`");
        }
    }
}
