<?php

namespace App\Controllers\Operator;

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
     * Tampilan daftar transparansi anggaran
     */
    public function index()
    {
        $search = $this->request->getGet('q');
        $tahun  = (int) $this->request->getGet('tahun');
        $status = $this->request->getGet('status');

        $list = $this->anggaranModel->getListForOperator($search, $tahun ?: null, $status);

        // Statistik singkat
        $villageId = (int) session('village_id');
        $db = \Config\Database::connect();
        $baseBuilder = $db->table('informasi_desa')
            ->whereIn('kategori', ['APBDes', 'Transparansi'])
            ->where('deleted_at IS NULL');
        if ($villageId > 0 && session('role_slug') !== 'super_admin') {
            $baseBuilder->where('village_id', $villageId);
        }

        $totalAll       = (clone $baseBuilder)->countAllResults();
        $totalPublished = (clone $baseBuilder)->where('is_published', 1)->countAllResults();
        $totalDraft     = (clone $baseBuilder)->where('is_published', 0)->countAllResults();

        // Ambil laporan anggaran aktif terbaru untuk ringkasan nominal
        $latest = $this->anggaranModel->getLatestPublished();
        $nominalBelanja   = $latest['total_belanja'] ?? 0;
        $nominalRealisasi = $latest['realisasi_belanja'] ?? 0;
        $persenRealisasi  = ($nominalBelanja > 0) ? round(($nominalRealisasi / $nominalBelanja) * 100, 1) : 0;

        return view('operator/transparansi/index', [
            'title'            => 'Transparansi ' . (is_aceh() ? 'APBG' : 'APBDes') . ' - SiPelayan Desa',
            'list'             => $list,
            'search'           => $search,
            'tahun'            => $tahun,
            'status'           => $status,
            'periodeList'      => AnggaranModel::PERIODE_LIST,
            'bidangList'       => AnggaranModel::BIDANG_LIST,
            'totalAll'         => $totalAll,
            'totalPublished'   => $totalPublished,
            'totalDraft'       => $totalDraft,
            'latest'           => $latest,
            'nominalBelanja'   => $nominalBelanja,
            'nominalRealisasi' => $nominalRealisasi,
            'persenRealisasi'  => $persenRealisasi,
        ]);
    }

    /**
     * Form tambah laporan anggaran baru
     */
    public function create()
    {
        $desa = desa_info();
        $isAceh = is_aceh();
        $istilah = $isAceh ? 'APBG' : 'APBDes';
        $sebutan = $isAceh ? 'Gampong' : 'Desa';
        $tahunSekarang = (int) date('Y');

        return view('operator/transparansi/create', [
            'title'         => "Tambah Laporan Transparansi {$istilah} - SiPelayan Desa",
            'periodeList'   => AnggaranModel::PERIODE_LIST,
            'bidangList'    => AnggaranModel::BIDANG_LIST,
            'tahunSekarang' => $tahunSekarang,
            'istilah'       => $istilah,
            'sebutan'       => $sebutan,
            'validation'    => \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan data transparansi anggaran baru
     */
    public function store()
    {
        $rules = [
            'tahun_anggaran'   => 'required|is_natural_no_zero|exact_length[4]',
            'periode_anggaran' => 'required|max_length[50]',
            'judul'            => 'required|min_length[5]|max_length[300]',
            'total_pendapatan' => 'required|numeric',
        ];

        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid()) {
            $rules['lampiran'] = 'max_size[lampiran,10240]|ext_in[lampiran,pdf,jpg,jpeg,png,doc,docx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul = trim($this->request->getPost('judul'));
        $konten = trim($this->request->getPost('konten') ?? '');
        $ringkasan = trim($this->request->getPost('ringkasan') ?? '');
        if (empty($ringkasan) && !empty($konten)) {
            $cleanText = strip_tags($konten);
            $ringkasan = mb_substr($cleanText, 0, 180) . (mb_strlen($cleanText) > 180 ? '...' : '');
        }

        $villageId = (int) session('village_id');
        $authorId  = (int) session('user_id');
        $isPublished = (int) ($this->request->getPost('is_published') ?? 1);

        // Parse rincian belanja 5 bidang
        $rincianBelanja = [];
        $totalBelanja = 0;
        $totalRealisasiBelanja = 0;

        foreach (AnggaranModel::BIDANG_LIST as $key => $meta) {
            $anggaranBidang  = (float) ($this->request->getPost("belanja_{$key}_anggaran") ?? 0);
            $realisasiBidang = (float) ($this->request->getPost("belanja_{$key}_realisasi") ?? 0);
            $ketBidang       = trim($this->request->getPost("belanja_{$key}_ket") ?? '');

            $totalBelanja += $anggaranBidang;
            $totalRealisasiBelanja += $realisasiBidang;

            $rincianBelanja[$key] = [
                'nama'      => $meta['nama'],
                'anggaran'  => $anggaranBidang,
                'realisasi' => $realisasiBidang,
                'keterangan'=> $ketBidang,
            ];
        }

        // Pendapatan & Pembiayaan
        $totalPendapatan      = (float) $this->request->getPost('total_pendapatan');
        $realisasiPendapatan  = (float) ($this->request->getPost('realisasi_pendapatan') ?? 0);
        $totalPembiayaan      = (float) ($this->request->getPost('total_pembiayaan') ?? 0);
        $realisasiPembiayaan  = (float) ($this->request->getPost('realisasi_pembiayaan') ?? 0);

        $rincianFull = [
            'pendapatan' => [
                'anggaran'  => $totalPendapatan,
                'realisasi' => $realisasiPendapatan,
                'keterangan'=> trim($this->request->getPost('pendapatan_ket') ?? ''),
            ],
            'belanja'    => $rincianBelanja,
            'pembiayaan' => [
                'penerimaan' => $totalPembiayaan,
                'realisasi'  => $realisasiPembiayaan,
                'keterangan' => trim($this->request->getPost('pembiayaan_ket') ?? ''),
            ],
        ];

        // Upload lampiran berkas resmi
        $lampiranPath = null;
        $lampiranNama = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/transparansi';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $lampiranPath = 'uploads/transparansi/' . $newName;
            $lampiranNama = $file->getClientName();
        }

        $slugBase = url_title($judul, '-', true);
        $slug = $slugBase . '-' . time();

        $data = [
            'village_id'           => $villageId,
            'author_id'            => $authorId,
            'kategori'             => 'APBDes',
            'nomor_pengumuman'     => trim($this->request->getPost('nomor_pengumuman') ?? '') ?: null,
            'periode_anggaran'     => $this->request->getPost('periode_anggaran'),
            'sifat'                => 'Biasa',
            'judul'                => $judul,
            'slug'                 => $slug,
            'ringkasan'            => $ringkasan,
            'konten'               => $konten,
            'lampiran_path'        => $lampiranPath,
            'lampiran_nama'        => $lampiranNama,
            'tahun_anggaran'       => (int) $this->request->getPost('tahun_anggaran'),
            'total_apbdes'         => $totalBelanja,
            'total_pendapatan'     => $totalPendapatan,
            'realisasi_pendapatan' => $realisasiPendapatan,
            'total_belanja'        => $totalBelanja,
            'realisasi_belanja'    => $totalRealisasiBelanja,
            'total_pembiayaan'     => $totalPembiayaan,
            'rincian_anggaran'     => json_encode($rincianFull),
            'is_published'         => $isPublished,
            'published_at'         => $isPublished ? date('Y-m-d H:i:s') : null,
            'views'                => 0,
        ];

        $this->anggaranModel->insert($data);

        return redirect()->to('/operator/transparansi')->with('success', 'Data transparansi anggaran berhasil disimpan.');
    }

    /**
     * Form edit laporan anggaran
     */
    public function edit(int $id)
    {
        $anggaran = $this->anggaranModel->getDetailWithAuthor($id);
        if (!$anggaran) {
            return redirect()->to('/operator/transparansi')->with('error', 'Data laporan anggaran tidak ditemukan.');
        }

        $isAceh = is_aceh();
        $istilah = $isAceh ? 'APBG' : 'APBDes';
        $sebutan = $isAceh ? 'Gampong' : 'Desa';

        return view('operator/transparansi/edit', [
            'title'       => "Edit Laporan Transparansi {$istilah} - SiPelayan Desa",
            'anggaran'    => $anggaran,
            'periodeList' => AnggaranModel::PERIODE_LIST,
            'bidangList'  => AnggaranModel::BIDANG_LIST,
            'istilah'     => $istilah,
            'sebutan'     => $sebutan,
            'validation'  => \Config\Services::validation(),
        ]);
    }

    /**
     * Update data laporan anggaran
     */
    public function update(int $id)
    {
        $anggaran = $this->anggaranModel->getDetailWithAuthor($id);
        if (!$anggaran) {
            return redirect()->to('/operator/transparansi')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'tahun_anggaran'   => 'required|is_natural_no_zero|exact_length[4]',
            'periode_anggaran' => 'required|max_length[50]',
            'judul'            => 'required|min_length[5]|max_length[300]',
            'total_pendapatan' => 'required|numeric',
        ];

        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid()) {
            $rules['lampiran'] = 'max_size[lampiran,10240]|ext_in[lampiran,pdf,jpg,jpeg,png,doc,docx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul = trim($this->request->getPost('judul'));
        $konten = trim($this->request->getPost('konten') ?? '');
        $ringkasan = trim($this->request->getPost('ringkasan') ?? '');
        if (empty($ringkasan) && !empty($konten)) {
            $cleanText = strip_tags($konten);
            $ringkasan = mb_substr($cleanText, 0, 180) . (mb_strlen($cleanText) > 180 ? '...' : '');
        }

        // Parse rincian belanja 5 bidang
        $rincianBelanja = [];
        $totalBelanja = 0;
        $totalRealisasiBelanja = 0;

        foreach (AnggaranModel::BIDANG_LIST as $key => $meta) {
            $anggaranBidang  = (float) ($this->request->getPost("belanja_{$key}_anggaran") ?? 0);
            $realisasiBidang = (float) ($this->request->getPost("belanja_{$key}_realisasi") ?? 0);
            $ketBidang       = trim($this->request->getPost("belanja_{$key}_ket") ?? '');

            $totalBelanja += $anggaranBidang;
            $totalRealisasiBelanja += $realisasiBidang;

            $rincianBelanja[$key] = [
                'nama'      => $meta['nama'],
                'anggaran'  => $anggaranBidang,
                'realisasi' => $realisasiBidang,
                'keterangan'=> $ketBidang,
            ];
        }

        // Pendapatan & Pembiayaan
        $totalPendapatan      = (float) $this->request->getPost('total_pendapatan');
        $realisasiPendapatan  = (float) ($this->request->getPost('realisasi_pendapatan') ?? 0);
        $totalPembiayaan      = (float) ($this->request->getPost('total_pembiayaan') ?? 0);
        $realisasiPembiayaan  = (float) ($this->request->getPost('realisasi_pembiayaan') ?? 0);

        $rincianFull = [
            'pendapatan' => [
                'anggaran'  => $totalPendapatan,
                'realisasi' => $realisasiPendapatan,
                'keterangan'=> trim($this->request->getPost('pendapatan_ket') ?? ''),
            ],
            'belanja'    => $rincianBelanja,
            'pembiayaan' => [
                'penerimaan' => $totalPembiayaan,
                'realisasi'  => $realisasiPembiayaan,
                'keterangan' => trim($this->request->getPost('pembiayaan_ket') ?? ''),
            ],
        ];

        $isPublished = (int) ($this->request->getPost('is_published') ?? 1);
        $publishedAt = $anggaran['published_at'];
        if ($isPublished && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $data = [
            'nomor_pengumuman'     => trim($this->request->getPost('nomor_pengumuman') ?? '') ?: null,
            'periode_anggaran'     => $this->request->getPost('periode_anggaran'),
            'judul'                => $judul,
            'ringkasan'            => $ringkasan,
            'konten'               => $konten,
            'tahun_anggaran'       => (int) $this->request->getPost('tahun_anggaran'),
            'total_apbdes'         => $totalBelanja,
            'total_pendapatan'     => $totalPendapatan,
            'realisasi_pendapatan' => $realisasiPendapatan,
            'total_belanja'        => $totalBelanja,
            'realisasi_belanja'    => $totalRealisasiBelanja,
            'total_pembiayaan'     => $totalPembiayaan,
            'rincian_anggaran'     => json_encode($rincianFull),
            'is_published'         => $isPublished,
            'published_at'         => $publishedAt,
        ];

        // Hapus lampiran jika dicentang
        if ($this->request->getPost('hapus_lampiran') == '1' && !empty($anggaran['lampiran_path'])) {
            $oldPath = FCPATH . $anggaran['lampiran_path'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $data['lampiran_path'] = null;
            $data['lampiran_nama'] = null;
        }

        // Upload lampiran baru jika ada
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/transparansi';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (!empty($anggaran['lampiran_path'])) {
                $oldPath = FCPATH . $anggaran['lampiran_path'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $data['lampiran_path'] = 'uploads/transparansi/' . $newName;
            $data['lampiran_nama'] = $file->getClientName();
        }

        $this->anggaranModel->update($id, $data);

        return redirect()->to('/operator/transparansi')->with('success', 'Data transparansi anggaran berhasil diperbarui.');
    }

    /**
     * Toggle status publikasi
     */
    public function togglePublish(int $id)
    {
        $anggaran = $this->anggaranModel->getDetailWithAuthor($id);
        if (!$anggaran) {
            return redirect()->to('/operator/transparansi')->with('error', 'Data tidak ditemukan.');
        }

        $newStatus = $anggaran['is_published'] ? 0 : 1;
        $updateData = ['is_published' => $newStatus];

        if ($newStatus === 1 && empty($anggaran['published_at'])) {
            $updateData['published_at'] = date('Y-m-d H:i:s');
        }

        $this->anggaranModel->update($id, $updateData);

        $statusLabel = $newStatus ? 'diterbitkan' : 'disimpan sebagai draft';
        return redirect()->to('/operator/transparansi')->with('success', "Status laporan berhasil diubah menjadi {$statusLabel}.");
    }

    /**
     * Hapus data anggaran
     */
    public function delete(int $id)
    {
        $anggaran = $this->anggaranModel->getDetailWithAuthor($id);
        if (!$anggaran) {
            return redirect()->to('/operator/transparansi')->with('error', 'Data tidak ditemukan.');
        }

        if (!empty($anggaran['lampiran_path'])) {
            $filePath = FCPATH . $anggaran['lampiran_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->anggaranModel->delete($id);

        return redirect()->to('/operator/transparansi')->with('success', 'Data laporan anggaran berhasil dihapus.');
    }

    /**
     * Pratinjau administratif infografis anggaran
     */
    public function detail(int $id)
    {
        $anggaran = $this->anggaranModel->getDetailWithAuthor($id);
        if (!$anggaran) {
            return redirect()->to('/operator/transparansi')->with('error', 'Data tidak ditemukan.');
        }

        $desa = desa_info($anggaran['village_id']);

        return view('operator/transparansi/detail', [
            'title'       => 'Pratinjau: ' . esc($anggaran['judul']),
            'anggaran'    => $anggaran,
            'desa'        => $desa,
            'bidangList'  => AnggaranModel::BIDANG_LIST,
        ]);
    }
}
