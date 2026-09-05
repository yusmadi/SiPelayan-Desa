<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('provinsi');

        // Daftar 38 Provinsi di Indonesia beserta kode resmi Kemendagri
        $dataProvinsi = [
            // Pulau Sumatera
            ['kode_kemendagri' => '11', 'nama' => 'Aceh'],
            ['kode_kemendagri' => '12', 'nama' => 'Sumatera Utara'],
            ['kode_kemendagri' => '13', 'nama' => 'Sumatera Barat'],
            ['kode_kemendagri' => '14', 'nama' => 'Riau'],
            ['kode_kemendagri' => '15', 'nama' => 'Jambi'],
            ['kode_kemendagri' => '16', 'nama' => 'Sumatera Selatan'],
            ['kode_kemendagri' => '17', 'nama' => 'Bengkulu'],
            ['kode_kemendagri' => '18', 'nama' => 'Lampung'],
            ['kode_kemendagri' => '19', 'nama' => 'Kepulauan Bangka Belitung'],
            ['kode_kemendagri' => '21', 'nama' => 'Kepulauan Riau'],

            // Pulau Jawa
            ['kode_kemendagri' => '31', 'nama' => 'DKI Jakarta'],
            ['kode_kemendagri' => '32', 'nama' => 'Jawa Barat'],
            ['kode_kemendagri' => '33', 'nama' => 'Jawa Tengah'],
            ['kode_kemendagri' => '34', 'nama' => 'Daerah Istimewa Yogyakarta'],
            ['kode_kemendagri' => '35', 'nama' => 'Jawa Timur'],
            ['kode_kemendagri' => '36', 'nama' => 'Banten'],

            // Pulau Bali & Nusa Tenggara
            ['kode_kemendagri' => '51', 'nama' => 'Bali'],
            ['kode_kemendagri' => '52', 'nama' => 'Nusa Tenggara Barat'],
            ['kode_kemendagri' => '53', 'nama' => 'Nusa Tenggara Timur'],

            // Pulau Kalimantan
            ['kode_kemendagri' => '61', 'nama' => 'Kalimantan Barat'],
            ['kode_kemendagri' => '62', 'nama' => 'Kalimantan Tengah'],
            ['kode_kemendagri' => '63', 'nama' => 'Kalimantan Selatan'],
            ['kode_kemendagri' => '64', 'nama' => 'Kalimantan Timur'],
            ['kode_kemendagri' => '65', 'nama' => 'Kalimantan Utara'],

            // Pulau Sulawesi
            ['kode_kemendagri' => '71', 'nama' => 'Sulawesi Utara'],
            ['kode_kemendagri' => '72', 'nama' => 'Sulawesi Tengah'],
            ['kode_kemendagri' => '73', 'nama' => 'Sulawesi Selatan'],
            ['kode_kemendagri' => '74', 'nama' => 'Sulawesi Tenggara'],
            ['kode_kemendagri' => '75', 'nama' => 'Gorontalo'],
            ['kode_kemendagri' => '76', 'nama' => 'Sulawesi Barat'],

            // Kepulauan Maluku
            ['kode_kemendagri' => '81', 'nama' => 'Maluku'],
            ['kode_kemendagri' => '82', 'nama' => 'Maluku Utara'],

            // Pulau Papua (termasuk pemekaran DOB Papua)
            ['kode_kemendagri' => '91', 'nama' => 'Papua'],
            ['kode_kemendagri' => '92', 'nama' => 'Papua Barat'],
            ['kode_kemendagri' => '93', 'nama' => 'Papua Selatan'],
            ['kode_kemendagri' => '94', 'nama' => 'Papua Tengah'],
            ['kode_kemendagri' => '95', 'nama' => 'Papua Pegunungan'],
            ['kode_kemendagri' => '96', 'nama' => 'Papua Barat Daya'],
        ];

        $now = date('Y-m-d H:i:s');
        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($dataProvinsi as $item) {
            $existing = $builder->where('kode_kemendagri', $item['kode_kemendagri'])->get()->getRowArray();

            if ($existing) {
                $builder->where('id', $existing['id'])->update([
                    'nama'       => $item['nama'],
                    'is_active'  => 1,
                    'updated_at' => $now,
                ]);
                $updatedCount++;
            } else {
                $builder->insert([
                    'kode_kemendagri' => $item['kode_kemendagri'],
                    'nama'            => $item['nama'],
                    'is_active'       => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
                $insertedCount++;
            }
        }

        echo "Provinsi Seeder selesai: {$insertedCount} data baru ditambahkan, {$updatedCount} data diperbarui. Total: " . count($dataProvinsi) . " provinsi.\n";
    }
}
