<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyDesa extends BaseCommand
{
    protected $group       = 'SiPelayan';
    protected $name        = 'app:verify-desa';
    protected $description = 'Verifikasi integritas data master desa se-Indonesia';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $totalProvinsi  = $db->table('provinsi')->countAllResults();
        $totalKabupaten = $db->table('kabupaten')->countAllResults();
        $totalKecamatan = $db->table('kecamatan')->countAllResults();
        $totalDesa      = $db->table('desa')->countAllResults();

        CLI::write("==================================================", 'yellow');
        CLI::write("   VERIFIKASI INTEGRITAS DATA WILAYAH INDONESIA   ", 'green');
        CLI::write("==================================================", 'yellow');
        CLI::write("Total Provinsi       : " . number_format($totalProvinsi, 0, ',', '.'));
        CLI::write("Total Kabupaten/Kota : " . number_format($totalKabupaten, 0, ',', '.'));
        CLI::write("Total Kecamatan      : " . number_format($totalKecamatan, 0, ',', '.'));
        CLI::write("Total Desa/Kelurahan : " . number_format($totalDesa, 0, ',', '.'));
        CLI::newLine();

        $provList = [
            '11' => 'Aceh',
            '12' => 'Sumatera Utara',
            '31' => 'DKI Jakarta',
            '32' => 'Jawa Barat',
            '33' => 'Jawa Tengah',
            '34' => 'DI Yogyakarta',
            '35' => 'Jawa Timur',
            '36' => 'Banten',
            '51' => 'Bali',
            '52' => 'Nusa Tenggara Barat',
            '53' => 'Nusa Tenggara Timur',
            '61' => 'Kalimantan Barat',
            '64' => 'Kalimantan Timur',
            '73' => 'Sulawesi Selatan',
            '81' => 'Maluku',
            '91' => 'Papua',
            '92' => 'Papua Barat',
            '96' => 'Papua Barat Daya',
        ];

        CLI::write("--- SAMPEL PERWAKILAN DESA/KELURAHAN PER PROVINSI ---", 'cyan');
        foreach ($provList as $kd => $provNama) {
            $row = $db->table('desa')
                ->select('desa.*, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->like('desa.kode_kemendagri', $kd . '.', 'after')
                ->get()
                ->getFirstRow('array');

            if ($row) {
                CLI::write("[$provNama] [{$row['kode_kemendagri']}] {$row['nama_desa']} | Kec. {$row['nama_kecamatan']} | Kab: {$row['nama_kabupaten']} | Slug: {$row['tenant_slug']}");
            } else {
                CLI::error("[$provNama] TIDAK DITEMUKAN");
            }
        }

        // Relational integrity checks
        $nullKab  = $db->table('desa')->where('kabupaten_id IS NULL')->countAllResults();
        $emptyKec = $db->table('desa')->where("nama_kecamatan = '' OR nama_kecamatan IS NULL")->countAllResults();

        CLI::newLine();
        CLI::write("--- INTEGRITAS RELASI DATABASE ---", 'cyan');
        if ($nullKab === 0 && $emptyKec === 0) {
            CLI::write("Status: SEMUA RELASI VALID (100% desa terhubung ke kabupaten & kecamatan)", 'green');
        } else {
            CLI::error("Terdapat {$nullKab} desa tanpa kabupaten dan {$emptyKec} desa tanpa nama_kecamatan.");
        }
        CLI::write("==================================================", 'yellow');
    }
}
