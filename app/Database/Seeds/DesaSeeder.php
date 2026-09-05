<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DesaSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Ambil mapping kabupaten_id berdasarkan kode_kemendagri kabupaten (5 char, misal: 33.02)
        $kabList = $db->table('kabupaten')->select('id, kode_kemendagri')->get()->getResultArray();
        $kabMap  = [];
        foreach ($kabList as $kab) {
            $kabMap[$kab['kode_kemendagri']] = (int) $kab['id'];
        }

        // 2. Pastikan file wilayah.sql tersedia di WRITEPATH . 'data/wilayah.sql'
        $sqlPath = WRITEPATH . 'data/wilayah.sql';
        if (! is_dir(WRITEPATH . 'data')) {
            mkdir(WRITEPATH . 'data', 0777, true);
        }

        if (! file_exists($sqlPath)) {
            echo "Mengunduh master data wilayah Kemendagri...\n";
            $sqlContent = @file_get_contents('https://raw.githubusercontent.com/cahyadsn/wilayah/master/db/wilayah.sql');
            if (! $sqlContent) {
                echo "Error: Gagal mengunduh data wilayah dari repositori master.\n";
                return;
            }
            file_put_contents($sqlPath, $sqlContent);
        }

        // 3. Parse data kecamatan & desa dari file wilayah.sql secara streaming baris per baris
        echo "Membaca data wilayah...\n";
        $handle   = fopen($sqlPath, 'r');
        $kecMap   = []; // [11.01.01 => Nama Kecamatan]
        $desaList = []; // Array of ['kode' => ..., 'nama' => ...]

        while (($line = fgets($handle)) !== false) {
            if (preg_match("/\(\x27([0-9\.]+)\x27\s*,\s*\x27((?:''|[^\x27])*)\x27\)/", $line, $m)) {
                $code = $m[1];
                $name = str_replace("''", "'", trim($m[2]));
                $dots = substr_count($code, '.');

                if ($dots === 2) {
                    // Level Kecamatan (XX.XX.XX)
                    $kecMap[$code] = $name;
                } elseif ($dots === 3) {
                    // Level Desa / Kelurahan (XX.XX.XX.XXXX)
                    $desaList[] = [
                        'kode' => $code,
                        'nama' => $name,
                    ];
                }
            }
        }
        fclose($handle);

        $totalDesa = count($desaList);
        echo "Ditemukan {$totalDesa} desa/kelurahan dan " . count($kecMap) . " kecamatan.\n";

        if ($totalDesa === 0) {
            echo "Error: Tidak ada data desa yang berhasil diparse.\n";
            return;
        }

        // 4. Batch insert dengan chunking 1000 baris per query
        $chunkSize      = 1000;
        $chunks         = array_chunk($desaList, $chunkSize);
        $totalProcessed = 0;
        $now            = date('Y-m-d H:i:s');

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->transStart();

        foreach ($chunks as $chunkIndex => $chunk) {
            $values = [];
            foreach ($chunk as $item) {
                $kodeDesa   = $item['kode'];
                $namaDesa   = $item['nama'];
                $parts      = explode('.', $kodeDesa);
                $kodeKab    = $parts[0] . '.' . $parts[1];
                $kodeKec    = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                $namaKec    = $kecMap[$kodeKec] ?? '';
                $kabId      = $kabMap[$kodeKab] ?? null;

                // Format tenant_slug unik
                $cleanName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($namaDesa));
                $cleanName = trim($cleanName, '-');
                if (empty($cleanName)) {
                    $cleanName = 'desa';
                }
                $codeClean = str_replace('.', '', $kodeDesa);
                $slug      = substr($cleanName . '-' . $codeClean, 0, 100);

                $escKodeDesa = $db->escape($kodeDesa);
                $escNamaDesa = $db->escape($namaDesa);
                $escNamaKec  = $db->escape($namaKec);
                $escSlug     = $db->escape($slug);
                $escNow      = $db->escape($now);
                $kabVal      = is_numeric($kabId) ? (int)$kabId : 'NULL';

                $values[] = "({$kabVal}, {$escKodeDesa}, {$escNamaDesa}, {$escNamaKec}, {$escSlug}, 1, {$escNow}, {$escNow})";
            }

            if (! empty($values)) {
                $sql = "INSERT INTO desa (kabupaten_id, kode_kemendagri, nama_desa, nama_kecamatan, tenant_slug, is_active, created_at, updated_at)
                        VALUES " . implode(', ', $values) . "
                        ON DUPLICATE KEY UPDATE
                        kabupaten_id = VALUES(kabupaten_id),
                        nama_desa = VALUES(nama_desa),
                        nama_kecamatan = VALUES(nama_kecamatan),
                        updated_at = VALUES(updated_at)";
                $db->query($sql);
                $totalProcessed += count($values);
            }

            if (($chunkIndex + 1) % 10 === 0 || $totalProcessed === $totalDesa) {
                echo "Progress: {$totalProcessed} / {$totalDesa} desa berhasil diproses...\n";
            }
        }

        $db->transComplete();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "Desa Seeder SELESAI: Total {$totalProcessed} desa/kelurahan berhasil dientry/disinkronkan.\n";
    }
}
