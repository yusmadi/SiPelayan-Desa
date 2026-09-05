<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Ambil mapping kabupaten_id berdasarkan kode_kemendagri kabupaten (5 char, contoh: 33.02)
        $kabList = $db->table('kabupaten')->get()->getResultArray();
        $kabMap = [];
        foreach ($kabList as $kab) {
            $kabMap[$kab['kode_kemendagri']] = (int) $kab['id'];
        }

        // 2. Baca data kecamatan dari file wilayah.sql atau JSON lokal
        $sqlPath  = WRITEPATH . 'data/wilayah.sql';
        $jsonPath = WRITEPATH . 'data/kecamatan.json';
        $rawKecamatan = [];

        if (file_exists($sqlPath)) {
            $sqlContent = file_get_contents($sqlPath);
        } else {
            $sqlContent = @file_get_contents('https://raw.githubusercontent.com/cahyadsn/wilayah/master/db/wilayah.sql');
            if ($sqlContent && ! is_dir(WRITEPATH . 'data')) {
                mkdir(WRITEPATH . 'data', 0777, true);
            }
            if ($sqlContent) {
                file_put_contents($sqlPath, $sqlContent);
            }
        }

        if ($sqlContent && preg_match_all("/\(\x27([0-9]{2}\.[0-9]{2}\.[0-9]{2})\x27\s*,\s*\x27((?:''|[^\x27])*)\x27\)/", $sqlContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $rawKecamatan[] = [
                    'kode' => $m[1],
                    'nama' => str_replace("''", "'", trim($m[2])),
                ];
            }
            file_put_contents($jsonPath, json_encode($rawKecamatan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } elseif (file_exists($jsonPath)) {
            $rawKecamatan = json_decode(file_get_contents($jsonPath), true);
        }

        if (empty($rawKecamatan)) {
            echo "Error: Data kecamatan tidak dapat dimuat.\n";
            return;
        }

        $now = date('Y-m-d H:i:s');
        $chunks = array_chunk($rawKecamatan, 500);
        $totalProcessed = 0;

        // Nonaktifkan foreign key checks sementara untuk optimasi seeding massal
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->transStart();

        foreach ($chunks as $chunk) {
            $values = [];
            foreach ($chunk as $item) {
                $kodeKec = $item['kode'];
                $namaKec = $item['nama'];
                $kodeKab = substr($kodeKec, 0, 5);
                $kabId   = $kabMap[$kodeKab] ?? 'NULL';

                $escKode = $db->escape($kodeKec);
                $escNama = $db->escape($namaKec);
                $escNow  = $db->escape($now);
                $kabVal  = is_numeric($kabId) ? (int)$kabId : 'NULL';

                $values[] = "({$kabVal}, {$escKode}, {$escNama}, 1, {$escNow}, {$escNow})";
            }

            if (! empty($values)) {
                $sql = "INSERT INTO kecamatan (kabupaten_id, kode_kemendagri, nama, is_active, created_at, updated_at)
                        VALUES " . implode(', ', $values) . "
                        ON DUPLICATE KEY UPDATE
                        kabupaten_id = VALUES(kabupaten_id),
                        nama = VALUES(nama),
                        is_active = 1,
                        updated_at = VALUES(updated_at)";
                $db->query($sql);
                $totalProcessed += count($values);
            }
        }

        $db->transComplete();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "Kecamatan Seeder selesai: {$totalProcessed} kecamatan berhasil diproses (insert / update).\n";
    }
}
