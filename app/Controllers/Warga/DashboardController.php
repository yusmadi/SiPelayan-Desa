<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

        $builder = $db->table('permohonan_surat')
            ->whereIn('status', ['submitted', 'verified_operator', 'approved_admin', 'diajukan', 'diproses']);

        if ($userId) {
            $builder->where('pemohon_id', $userId);
        }

        if ($villageId) {
            $builder->where('village_id', $villageId);
        }

        $processCount = $builder->countAllResults();

        $pengaduanBuilder = $db->table('pengaduan')
            ->whereIn('status', ['open', 'in_progress']);

        if ($userId) {
            $pengaduanBuilder->where('pelapor_id', $userId);
        }

        if ($villageId) {
            $pengaduanBuilder->where('village_id', $villageId);
        }

        $pengaduanCount = $pengaduanBuilder->countAllResults();
        $desa = desa_info($villageId);

        // Ambil pengumuman resmi terbaru yang dipublikasikan
        $pengumumanModel = new \App\Models\PengumumanModel();
        $latestPengumuman = $pengumumanModel
            ->where('kategori', 'Pengumuman')
            ->where('is_published', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        // Ambil daftar pengumuman lainnya untuk referensi cepat
        $otherPengumuman = [];
        if ($latestPengumuman) {
            $otherPengumuman = $pengumumanModel
                ->where('kategori', 'Pengumuman')
                ->where('is_published', 1)
                ->where('deleted_at IS NULL')
                ->where('id !=', $latestPengumuman['id'])
                ->orderBy('published_at', 'DESC')
                ->findAll(3);
        }

        // Ambil data transparansi anggaran (APBDes/APBG) terbaru yang dipublikasikan
        $anggaranModel = new \App\Models\AnggaranModel();
        $latestAnggaran = $anggaranModel->getLatestPublished();

        return view('warga/dashboard/index', [
            'title'            => 'Dashboard Warga - SiPelayan Desa',
            'processCount'     => $processCount,
            'pengaduanCount'   => $pengaduanCount,
            'desa'             => $desa,
            'latestPengumuman' => $latestPengumuman,
            'otherPengumuman'  => $otherPengumuman,
            'latestAnggaran'   => $latestAnggaran,
        ]);
    }
}
