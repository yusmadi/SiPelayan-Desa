<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $totalProvinsi  = $db->table('provinsi')->countAllResults();
        $totalKabupaten = $db->table('kabupaten')->countAllResults();
        $totalKecamatan = $db->table('kecamatan')->countAllResults();
        $totalDesa      = $db->table('desa')->countAllResults();
        $totalUsers     = $db->table('users')->countAllResults();
        $totalSurat     = $db->table('permohonan_surat')->countAllResults();

        // Ambil desa yang sudah memiliki akun Kepala Desa atau Admin Desa
        $recentDesa = $db->table('desa')
            ->select('desa.*, kabupaten.nama as nama_kabupaten, MAX(users.nama_lengkap) as nama_admin_desa, MAX(users.email) as email_admin, MAX(users.no_hp) as no_hp_admin, MAX(users.is_active) as admin_is_active')
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
            ->join('users', 'users.village_id = desa.id')
            ->join('roles', 'roles.id = users.role_id')
            ->groupStart()
                ->where('roles.slug', 'admin_desa')
                ->orWhere('users.role_id', 2)
            ->groupEnd()
            ->where('users.deleted_at', null)
            ->groupBy('desa.id')
            ->orderBy('desa.id', 'DESC')
            ->get()->getResultArray();

        return view('super_admin/dashboard/index', [
            'title'          => 'Dashboard Super Admin - SiPelayan Desa',
            'totalProvinsi'  => $totalProvinsi,
            'totalKabupaten' => $totalKabupaten,
            'totalKecamatan' => $totalKecamatan,
            'totalDesa'      => $totalDesa,
            'totalUsers'     => $totalUsers,
            'totalSurat'     => $totalSurat,
            'recentDesa'     => $recentDesa,
        ]);
    }
}
