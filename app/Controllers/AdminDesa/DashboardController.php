<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $totalPenduduk = $db->table('penduduk')->where('village_id', $villageId)->countAllResults();
        $totalSurat    = $db->table('permohonan_surat')->where('village_id', $villageId)->countAllResults();
        $pendingSurat  = $db->table('permohonan_surat')
            ->where('village_id', $villageId)
            ->whereIn('status', ['submitted', 'verified_operator', 'approved_admin'])
            ->countAllResults();

        $recentSurat = $db->table('permohonan_surat')
            ->select('permohonan_surat.*, jenis_surat.nama as nama_surat, users.nama_lengkap as nama_pemohon')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id')
            ->join('users', 'users.id = permohonan_surat.pemohon_id')
            ->where('permohonan_surat.village_id', $villageId)
            ->orderBy('permohonan_surat.id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('admin_desa/dashboard/index', [
            'title'         => 'Dashboard Admin Desa - SiPelayan Desa',
            'totalPenduduk' => $totalPenduduk,
            'totalSurat'    => $totalSurat,
            'pendingSurat'  => $pendingSurat,
            'recentSurat'   => $recentSurat,
        ]);
    }
}
