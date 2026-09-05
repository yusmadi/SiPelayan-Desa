<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Models\AnggaranModel;

class TransparansiController extends BaseController
{
    protected AnggaranModel $anggaranModel;

    public function __construct()
    {
        $this->anggaranModel = new AnggaranModel();
    }

    /**
     * Halaman arsip transparansi anggaran untuk warga
     */
    public function index()
    {
        $search = $this->request->getGet('q');
        $tahun  = (int) $this->request->getGet('tahun');
        $villageId = (int) session('village_id');

        $builder = $this->anggaranModel
            ->whereIn('kategori', ['APBDes', 'Transparansi'])
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
                ->orLike('periode_anggaran', $search)
                ->groupEnd();
        }

        if (!empty($tahun)) {
            $builder->where('tahun_anggaran', $tahun);
        }

        $list = $builder->orderBy('tahun_anggaran', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($list as &$item) {
            if (!empty($item['rincian_anggaran']) && is_string($item['rincian_anggaran'])) {
                $item['rincian'] = json_decode($item['rincian_anggaran'], true) ?: [];
            } else {
                $item['rincian'] = is_array($item['rincian_anggaran'] ?? null) ? $item['rincian_anggaran'] : [];
            }
        }

        $desa = desa_info($villageId);

        return view('warga/transparansi/index', [
            'title'       => 'Papan Transparansi ' . (is_aceh() ? 'APBG' : 'APBDes') . ' - SiPelayan Desa',
            'list'        => $list,
            'search'      => $search,
            'tahun'       => $tahun,
            'desa'        => $desa,
            'bidangList'  => AnggaranModel::BIDANG_LIST,
            'periodeList' => AnggaranModel::PERIODE_LIST,
        ]);
    }

    /**
     * Halaman detail infografis anggaran resmi (Format Dokumen Resmi)
     */
    public function detail(string $slugOrId)
    {
        $villageId = (int) session('village_id');

        $builder = $this->anggaranModel
            ->whereIn('kategori', ['APBDes', 'Transparansi'])
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

        $anggaran = $builder->first();

        if (!$anggaran) {
            return redirect()->to('/warga/transparansi')->with('error', 'Laporan transparansi tidak ditemukan.');
        }

        if (!empty($anggaran['rincian_anggaran']) && is_string($anggaran['rincian_anggaran'])) {
            $anggaran['rincian'] = json_decode($anggaran['rincian_anggaran'], true) ?: [];
        } else {
            $anggaran['rincian'] = is_array($anggaran['rincian_anggaran'] ?? null) ? $anggaran['rincian_anggaran'] : [];
        }

        // Increment view count
        $this->anggaranModel->incrementViews($anggaran['id']);

        $desa = desa_info($anggaran['village_id']);

        return view('warga/transparansi/detail', [
            'title'      => esc($anggaran['judul']) . ' - Transparansi Resmi',
            'anggaran'   => $anggaran,
            'desa'       => $desa,
            'bidangList' => AnggaranModel::BIDANG_LIST,
        ]);
    }
}
