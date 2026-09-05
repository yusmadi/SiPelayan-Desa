<?php

namespace App\Controllers\Operator;

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
     * Tampilan daftar pengumuman operator
     */
    public function index()
    {
        $search = $this->request->getGet('q');
        $sifat  = $this->request->getGet('sifat');
        $status = $this->request->getGet('status');

        $list = $this->pengumumanModel->getListForOperator($search, $sifat, $status);

        // Statistik singkat
        $villageId = (int) session('village_id');
        $db = \Config\Database::connect();
        $baseBuilder = $db->table('informasi_desa')
            ->where('kategori', 'Pengumuman')
            ->where('deleted_at IS NULL');
        if ($villageId > 0 && session('role_slug') !== 'super_admin') {
            $baseBuilder->where('village_id', $villageId);
        }

        $totalAll       = (clone $baseBuilder)->countAllResults();
        $totalPublished = (clone $baseBuilder)->where('is_published', 1)->countAllResults();
        $totalDraft     = (clone $baseBuilder)->where('is_published', 0)->countAllResults();
        $totalPenting   = (clone $baseBuilder)->whereIn('sifat', ['Penting', 'Segera'])->countAllResults();

        return view('operator/pengumuman/index', [
            'title'          => 'Manajemen Pengumuman ' . sebutan_desa() . ' - SiPelayan Desa',
            'list'           => $list,
            'search'         => $search,
            'sifat'          => $sifat,
            'status'         => $status,
            'sifatList'      => PengumumanModel::SIFAT_LIST,
            'sifatBadges'    => PengumumanModel::SIFAT_BADGES,
            'totalAll'       => $totalAll,
            'totalPublished' => $totalPublished,
            'totalDraft'     => $totalDraft,
            'totalPenting'   => $totalPenting,
        ]);
    }

    /**
     * Form tambah pengumuman baru
     */
    public function create()
    {
        $desa = desa_info();
        $kodeDesa = $desa['nama_desa'] ? strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $desa['nama_desa']), 0, 3)) : 'DES';
        $romawiBulan = $this->getRomawi(date('n'));
        $suggestedNomor = sprintf('00%d/PG-%s/%s/%s', rand(1, 9), $kodeDesa, $romawiBulan, date('Y'));

        return view('operator/pengumuman/create', [
            'title'          => 'Tambah Pengumuman Resmi Baru - SiPelayan Desa',
            'sifatList'      => PengumumanModel::SIFAT_LIST,
            'suggestedNomor' => $suggestedNomor,
            'validation'     => \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan pengumuman baru
     */
    public function store()
    {
        $rules = [
            'judul'            => 'required|min_length[5]|max_length[300]',
            'konten'           => 'required|min_length[10]',
            'sifat'            => 'required|in_list[Biasa,Penting,Segera]',
            'nomor_pengumuman' => 'permit_empty|max_length[100]',
            'ringkasan'        => 'permit_empty|max_length[500]',
        ];

        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid()) {
            $rules['lampiran'] = 'max_size[lampiran,5120]|ext_in[lampiran,pdf,jpg,jpeg,png,doc,docx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul = trim($this->request->getPost('judul'));
        $konten = trim($this->request->getPost('konten'));
        $ringkasan = trim($this->request->getPost('ringkasan') ?? '');
        if (empty($ringkasan)) {
            $cleanText = strip_tags($konten);
            $ringkasan = mb_substr($cleanText, 0, 180) . (mb_strlen($cleanText) > 180 ? '...' : '');
        }

        $villageId = (int) session('village_id');
        $authorId  = (int) session('user_id');
        $isPublished = (int) ($this->request->getPost('is_published') ?? 1);

        $lampiranPath = null;
        $lampiranNama = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/pengumuman';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $lampiranPath = 'uploads/pengumuman/' . $newName;
            $lampiranNama = $file->getClientName();
        }

        $slugBase = url_title($judul, '-', true);
        $slug = $slugBase . '-' . time();

        $data = [
            'village_id'       => $villageId,
            'author_id'        => $authorId,
            'kategori'         => 'Pengumuman',
            'nomor_pengumuman' => trim($this->request->getPost('nomor_pengumuman') ?? '') ?: null,
            'sifat'            => $this->request->getPost('sifat') ?: 'Biasa',
            'judul'            => $judul,
            'slug'             => $slug,
            'ringkasan'        => $ringkasan,
            'konten'           => $konten,
            'lampiran_path'    => $lampiranPath,
            'lampiran_nama'    => $lampiranNama,
            'is_published'     => $isPublished,
            'published_at'     => $isPublished ? date('Y-m-d H:i:s') : null,
            'views'            => 0,
        ];

        $this->pengumumanModel->insert($data);

        return redirect()->to('/operator/pengumuman')->with('success', 'Pengumuman resmi berhasil dibuat dan disimpan.');
    }

    /**
     * Form edit pengumuman
     */
    public function edit(int $id)
    {
        $pengumuman = $this->pengumumanModel->getDetailWithAuthor($id);
        if (!$pengumuman) {
            return redirect()->to('/operator/pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        return view('operator/pengumuman/edit', [
            'title'      => 'Edit Pengumuman Resmi - SiPelayan Desa',
            'pengumuman' => $pengumuman,
            'sifatList'  => PengumumanModel::SIFAT_LIST,
            'validation' => \Config\Services::validation(),
        ]);
    }

    /**
     * Proses update pengumuman
     */
    public function update(int $id)
    {
        $pengumuman = $this->pengumumanModel->getDetailWithAuthor($id);
        if (!$pengumuman) {
            return redirect()->to('/operator/pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        $rules = [
            'judul'            => 'required|min_length[5]|max_length[300]',
            'konten'           => 'required|min_length[10]',
            'sifat'            => 'required|in_list[Biasa,Penting,Segera]',
            'nomor_pengumuman' => 'permit_empty|max_length[100]',
            'ringkasan'        => 'permit_empty|max_length[500]',
        ];

        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid()) {
            $rules['lampiran'] = 'max_size[lampiran,5120]|ext_in[lampiran,pdf,jpg,jpeg,png,doc,docx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $judul = trim($this->request->getPost('judul'));
        $konten = trim($this->request->getPost('konten'));
        $ringkasan = trim($this->request->getPost('ringkasan') ?? '');
        if (empty($ringkasan)) {
            $cleanText = strip_tags($konten);
            $ringkasan = mb_substr($cleanText, 0, 180) . (mb_strlen($cleanText) > 180 ? '...' : '');
        }

        $isPublished = (int) ($this->request->getPost('is_published') ?? 1);
        $publishedAt = $pengumuman['published_at'];
        if ($isPublished && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $data = [
            'nomor_pengumuman' => trim($this->request->getPost('nomor_pengumuman') ?? '') ?: null,
            'sifat'            => $this->request->getPost('sifat') ?: 'Biasa',
            'judul'            => $judul,
            'ringkasan'        => $ringkasan,
            'konten'           => $konten,
            'is_published'     => $isPublished,
            'published_at'     => $publishedAt,
        ];

        // Hapus lampiran jika dicentang
        if ($this->request->getPost('hapus_lampiran') == '1' && !empty($pengumuman['lampiran_path'])) {
            $oldPath = FCPATH . $pengumuman['lampiran_path'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $data['lampiran_path'] = null;
            $data['lampiran_nama'] = null;
        }

        // Upload lampiran baru jika ada
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/pengumuman';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Hapus file lama jika ada
            if (!empty($pengumuman['lampiran_path'])) {
                $oldPath = FCPATH . $pengumuman['lampiran_path'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $data['lampiran_path'] = 'uploads/pengumuman/' . $newName;
            $data['lampiran_nama'] = $file->getClientName();
        }

        $this->pengumumanModel->update($id, $data);

        return redirect()->to('/operator/pengumuman')->with('success', 'Pengumuman resmi berhasil diperbarui.');
    }

    /**
     * Toggle status publikasi
     */
    public function togglePublish(int $id)
    {
        $pengumuman = $this->pengumumanModel->getDetailWithAuthor($id);
        if (!$pengumuman) {
            return redirect()->to('/operator/pengumuman')->with('error', 'Data tidak ditemukan.');
        }

        $newStatus = $pengumuman['is_published'] ? 0 : 1;
        $updateData = ['is_published' => $newStatus];

        if ($newStatus === 1 && empty($pengumuman['published_at'])) {
            $updateData['published_at'] = date('Y-m-d H:i:s');
        }

        $this->pengumumanModel->update($id, $updateData);

        $statusLabel = $newStatus ? 'diterbitkan' : 'disimpan sebagai draft';
        return redirect()->to('/operator/pengumuman')->with('success', "Status pengumuman berhasil diubah menjadi {$statusLabel}.");
    }

    /**
     * Hapus pengumuman
     */
    public function delete(int $id)
    {
        $pengumuman = $this->pengumumanModel->getDetailWithAuthor($id);
        if (!$pengumuman) {
            return redirect()->to('/operator/pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        if (!empty($pengumuman['lampiran_path'])) {
            $filePath = FCPATH . $pengumuman['lampiran_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->pengumumanModel->delete($id);

        return redirect()->to('/operator/pengumuman')->with('success', 'Pengumuman berhasil dihapus dari sistem.');
    }

    /**
     * Pratinjau administratif pengumuman
     */
    public function detail(int $id)
    {
        $pengumuman = $this->pengumumanModel->getDetailWithAuthor($id);
        if (!$pengumuman) {
            return redirect()->to('/operator/pengumuman')->with('error', 'Pengumuman tidak ditemukan.');
        }

        $desa = desa_info($pengumuman['village_id']);

        return view('operator/pengumuman/detail', [
            'title'      => 'Pratinjau Pengumuman: ' . esc($pengumuman['judul']),
            'pengumuman' => $pengumuman,
            'desa'       => $desa,
        ]);
    }

    /**
     * Helper angka romawi untuk nomor surat
     */
    private function getRomawi(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$bulan] ?? 'I';
    }
}
