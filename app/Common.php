<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('is_aceh')) {
    /**
     * Memeriksa apakah desa saat ini (dari session) atau berdasarkan $villageId berada di wilayah Provinsi Aceh.
     */
    function is_aceh(?int $villageId = null): bool
    {
        static $acehCache = [];

        // Jika tidak dispesifikasikan, cek dari session terlebih dahulu
        if ($villageId === null) {
            if (function_exists('session') && session()->has('is_aceh')) {
                return (bool) session('is_aceh');
            }
            if (function_exists('session') && session()->has('village_id')) {
                $villageId = (int) session('village_id');
            }
        }

        if (empty($villageId)) {
            return false;
        }

        if (isset($acehCache[$villageId])) {
            return $acehCache[$villageId];
        }

        try {
            $db = \Config\Database::connect();
            $row = $db->table('desa')
                ->select('desa.id, desa.kode_kemendagri as kode_desa, kabupaten.kode_kemendagri as kode_kab, kabupaten.provinsi as prov_kab, provinsi.kode_kemendagri as kode_prov, provinsi.nama as prov_nama')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left')
                ->where('desa.id', $villageId)
                ->get()
                ->getRowArray();

            if (! $row) {
                $acehCache[$villageId] = false;
                return false;
            }

            $isAceh = false;
            if (!empty($row['kode_prov']) && trim($row['kode_prov']) === '11') {
                $isAceh = true;
            } elseif (!empty($row['kode_kab']) && str_starts_with(trim($row['kode_kab']), '11')) {
                $isAceh = true;
            } elseif (!empty($row['kode_desa']) && str_starts_with(trim($row['kode_desa']), '11')) {
                $isAceh = true;
            } elseif (!empty($row['prov_nama']) && stripos($row['prov_nama'], 'Aceh') !== false) {
                $isAceh = true;
            } elseif (!empty($row['prov_kab']) && stripos($row['prov_kab'], 'Aceh') !== false) {
                $isAceh = true;
            }

            $acehCache[$villageId] = $isAceh;
            return $isAceh;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('sebutan_desa')) {
    /**
     * Mengembalikan sebutan 'Gampong' (Aceh) atau 'Desa' (Luar Aceh)
     */
    function sebutan_desa(?int $villageId = null, string $case = 'title'): string
    {
        $isAceh = is_aceh($villageId);
        $case = strtolower($case);

        if ($isAceh) {
            if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
                return 'GAMPONG';
            }
            if (in_array($case, ['lower', 'lowercase'])) {
                return 'gampong';
            }
            return 'Gampong';
        }

        if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
            return 'DESA';
        }
        if (in_array($case, ['lower', 'lowercase'])) {
            return 'desa';
        }
        return 'Desa';
    }
}

if (!function_exists('sebutan_kades')) {
    /**
     * Mengembalikan sebutan 'Keuchik' (Aceh) atau 'Kepala Desa' (Luar Aceh)
     */
    function sebutan_kades(?int $villageId = null, string $case = 'title'): string
    {
        $isAceh = is_aceh($villageId);
        $case = strtolower($case);

        if ($isAceh) {
            if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
                return 'KEUCHIK';
            }
            if (in_array($case, ['lower', 'lowercase'])) {
                return 'keuchik';
            }
            return 'Keuchik';
        }

        if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
            return 'KEPALA DESA';
        }
        if (in_array($case, ['lower', 'lowercase'])) {
            return 'kepala desa';
        }
        return 'Kepala Desa';
    }
}

if (!function_exists('sebutan_sekdes')) {
    /**
     * Mengembalikan sebutan 'Sekretaris Gampong' (Aceh) atau 'Sekretaris Desa' (Luar Aceh)
     */
    function sebutan_sekdes(?int $villageId = null, string $case = 'title'): string
    {
        $isAceh = is_aceh($villageId);
        $case = strtolower($case);

        if ($isAceh) {
            if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
                return 'SEKRETARIS GAMPONG';
            }
            if (in_array($case, ['lower', 'lowercase'])) {
                return 'sekretaris gampong';
            }
            return 'Sekretaris Gampong';
        }

        if (in_array($case, ['upper', 'uppercase', 'all_caps'])) {
            return 'SEKRETARIS DESA';
        }
        if (in_array($case, ['lower', 'lowercase'])) {
            return 'sekretaris desa';
        }
        return 'Sekretaris Desa';
    }
}

if (!function_exists('format_teks_wilayah')) {
    /**
     * Mengubah teks secara dinamis: jika di Aceh, 'Desa' -> 'Gampong' dan 'Kepala Desa' -> 'Keuchik'
     */
    function format_teks_wilayah(string $text, ?int $villageId = null): string
    {
        if (!is_aceh($villageId)) {
            return $text;
        }

        $dictionary = [
            'Kepala Desa'            => 'Keuchik',
            'KEPALA DESA'            => 'KEUCHIK',
            'kepala desa'            => 'keuchik',
            'Kepala desa'            => 'Keuchik',
            'Kades'                  => 'Keuchik',
            'KADES'                  => 'KEUCHIK',
            'kades'                  => 'keuchik',
            'Sekretaris Desa'        => 'Sekretaris Gampong',
            'SEKRETARIS DESA'        => 'SEKRETARIS GAMPONG',
            'sekretaris desa'        => 'sekretaris gampong',
            'Sekdes'                 => 'Sekdes Gampong',
            'Admin Desa'             => 'Admin Gampong',
            'ADMIN DESA'             => 'ADMIN GAMPONG',
            'admin desa'             => 'admin gampong',
            'Operator Desa'          => 'Operator Gampong',
            'OPERATOR DESA'          => 'OPERATOR GAMPONG',
            'operator desa'          => 'operator gampong',
            'Perangkat Desa'         => 'Perangkat Gampong',
            'PERANGKAT DESA'         => 'PERANGKAT GAMPONG',
            'perangkat desa'         => 'perangkat gampong',
            'Balai Desa'             => 'Balai Gampong',
            'BALAI DESA'             => 'BALAI GAMPONG',
            'balai desa'             => 'balai gampong',
            'Kantor Desa'            => 'Kantor Keuchik',
            'KANTOR DESA'            => 'KANTOR KEUCHIK',
            'kantor desa'            => 'kantor keuchik',
            'Pemerintah Desa'        => 'Pemerintah Gampong',
            'PEMERINTAH DESA'        => 'PEMERINTAH GAMPONG',
            'pemerintah desa'        => 'pemerintah gampong',
            'Surat Keterangan Desa'  => 'Surat Keterangan Gampong',
            'SURAT KETERANGAN DESA'  => 'SURAT KETERANGAN GAMPONG',
            'surat keterangan desa'  => 'surat keterangan gampong',
            'Desa'                   => 'Gampong',
            'DESA'                   => 'GAMPONG',
            'desa'                   => 'gampong',
        ];

        return strtr($text, $dictionary);
    }
}

if (!function_exists('desa_info')) {
    /**
     * Mengambil data desa aktif berdasarkan session atau village_id
     */
    function desa_info(?int $villageId = null): ?array
    {
        static $desaCache = [];

        if ($villageId === null && function_exists('session') && session()->has('village_id')) {
            $villageId = (int) session('village_id');
        }

        if (empty($villageId)) {
            return null;
        }

        if (isset($desaCache[$villageId])) {
            return $desaCache[$villageId];
        }

        try {
            $db = \Config\Database::connect();
            $desa = $db->table('desa')->where('id', $villageId)->get()->getRowArray();
            $desaCache[$villageId] = $desa ?: null;
            return $desaCache[$villageId];
        } catch (\Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('format_wa_url')) {
    /**
     * Memformat nomor telepon menjadi link WhatsApp dengan prefilled text
     */
    function format_wa_url(?string $phone, string $message = ''): string
    {
        if (empty($phone)) {
            return '#';
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        $url = 'https://wa.me/' . $clean;
        if (!empty($message)) {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}
