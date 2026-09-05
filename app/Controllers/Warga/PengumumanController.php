<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Models\PengumumanModel;

class PengumumanController extends BaseController
{
    protected PengumumanModel $pengumumanModel;

    public function __construct()
    {
        $this->pengumumanModel = new PengumumanModel();
    }

    /**
     * Halaman arsip daftar pengumuman untuk warga
     */
    public function index()
    {
        $search = $this->request->getGet('q');
        $sifat  = $this->request->getGet('sifat');
        $villageId = (int) session('village_id');

        $builder = $this->pengumumanModel
            ->where('kategori', 'Pengumuman')
            ->where('is_published', 1)
            ->where('deleted_at IS NULL');

        if ($villageId > 0 && session('role_slug') !== 'super_admin') {
            $builder->where('village_id', $villageId);
        }

        if (!empty($search)) {
            $search = trim($search);
            $builder->groupStart()
                ->like('judul', $search)
                ->orLike('nomor_pengumuman', $search)
                ->orLike('konten', $search)
                ->groupEnd();
        }

        if (!empty($sifat) && array_key_exists($sifat, PengumumanModel::SIFAT_LIST)) {
            $builder->where('sifat', $sifat);
        }

        $list = $builder->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $desa = desa_info($villageId);

        return view('warga/pengumuman/index', [
            'title'       => 'Papan Pengumuman Resmi ' . sebutan_desa() . ' - SiPelayan Desa',
            'list'        => $list,
            'search'      => $search,
            'sifat'       => $sifat,
            'sifatList'   => PengumumanModel::SIFAT_LIST,
            'sifatBadges' => PengumumanModel::SIFAT_BADGES,
            'desa'        => $desa,
        ]);
    }

    /**
     * Halaman detail lengkap pengumuman resmi (Format Dokumen Resmi)
     */
    public function detail(string $slugOrId)
    {
        $villageId = (int) session('village_id');

        $builder = $this->pengumumanModel
            ->where('kategori', 'Pengumuman')
            ->where('is_published', 1)
            ->where('deleted_at IS NULL');

        if ($villageId > 0 && session('role_slug') !== 'super_admin') {
            $builder->where('village_id', $villageId);
        }

        if (is_numeric($slugOrId)) {
            $builder->where('id', (int) $slugOrId);
        } else {
            $builder->where('slug', $slugOrId);
        }

        $pengumuman = $builder->first();

        if (!$pengumuman) {
            return redirect()->to('/warga/pengumuman')->with('error', 'Pengumuman tidak ditemukan atau belum dipublikasikan.');
        }

        // Tambah jumlah view
        $this->pengumumanModel->incrementViews($pengumuman['id']);

        $desa = desa_info($pengumuman['village_id']);

        return view('warga/pengumuman/detail', [
            'title'      => esc($pengumuman['judul']) . ' - Pengumuman Resmi',
            'pengumuman' => $pengumuman,
            'desa'       => $desa,
        ]);
    }
}
