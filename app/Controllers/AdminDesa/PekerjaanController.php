<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;

class PekerjaanController extends BaseController
{
    protected PekerjaanModel $pekerjaanModel;

    public function __construct()
    {
        $this->pekerjaanModel = new PekerjaanModel();
    }

    /**
     * Dapatkan base route path berdasarkan role pengguna
     */
    private function getBaseRoute(): string
    {
        $role = session('role_slug');
        if ($role === 'operator') {
            return 'operator/pekerjaan';
        }
        return 'admin-desa/pekerjaan';
    }

    /**
     * Halaman daftar data pekerjaan
     */
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));

        if ($search !== '') {
            $this->pekerjaanModel->groupStart()
                ->like('nama', $search)
                ->orLike('keterangan', $search)
                ->groupEnd();
        }

        $this->pekerjaanModel->orderBy('nama', 'ASC');

        $perPage = 15;
        $pekerjaan = $this->pekerjaanModel->paginate($perPage);
        $pager = $this->pekerjaanModel->pager;
        $total = $pager ? $pager->getTotal('default') : count($pekerjaan);

        // Hitung jumlah penggunaan pekerjaan pada tabel penduduk jika ada
        $db = \Config\Database::connect();
        $usageCounts = [];
        if ($db->tableExists('penduduk')) {
            $usageQuery = $db->table('penduduk')
                ->select('pekerjaan, COUNT(id) as total')
                ->where('deleted_at IS NULL')
                ->groupBy('pekerjaan')
                ->get()
                ->getResultArray();
            foreach ($usageQuery as $u) {
                if (!empty($u['pekerjaan'])) {
                    $usageCounts[$u['pekerjaan']] = (int) $u['total'];
                }
            }
        }

        return view('admin_desa/pekerjaan/index', [
            'title'       => 'Data Pekerjaan - SiPelayan Desa',
            'pekerjaan'   => $pekerjaan,
            'pager'       => $pager,
            'total'       => $total,
            'search'      => $search,
            'usageCounts' => $usageCounts,
            'baseRoute'   => $this->getBaseRoute(),
            'validation'  => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan data pekerjaan baru
     */
    public function store()
    {
        $rules = [
            'nama' => [
                'rules'  => 'required|min_length[2]|max_length[100]|is_unique[pekerjaan.nama]',
                'errors' => [
                    'required'   => 'Nama pekerjaan wajib diisi.',
                    'min_length' => 'Nama pekerjaan minimal 2 karakter.',
                    'max_length' => 'Nama pekerjaan maksimal 100 karakter.',
                    'is_unique'  => 'Nama pekerjaan ini sudah terdaftar.',
                ]
            ],
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url($this->getBaseRoute()))
                ->withInput()
                ->with('validation', $this->validator)
                ->with('error', 'Gagal menambahkan pekerjaan. Mohon periksa inputan Anda.');
        }

        $nama = trim((string) $this->request->getPost('nama'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $this->pekerjaanModel->insert([
            'nama'       => $nama,
            'keterangan' => $keterangan ?: null,
        ]);

        return redirect()->to(base_url($this->getBaseRoute()))
            ->with('success', "Pekerjaan '{$nama}' berhasil ditambahkan ke database.");
    }

    /**
     * Update data pekerjaan
     */
    public function update($id)
    {
        $pekerjaan = $this->pekerjaanModel->find($id);
        if (!$pekerjaan) {
            return redirect()->to(base_url($this->getBaseRoute()))
                ->with('error', 'Data pekerjaan tidak ditemukan.');
        }

        $rules = [
            'nama' => [
                'rules'  => "required|min_length[2]|max_length[100]|is_unique[pekerjaan.nama,id,{$id}]",
                'errors' => [
                    'required'   => 'Nama pekerjaan wajib diisi.',
                    'min_length' => 'Nama pekerjaan minimal 2 karakter.',
                    'max_length' => 'Nama pekerjaan maksimal 100 karakter.',
                    'is_unique'  => 'Nama pekerjaan ini sudah terdaftar.',
                ]
            ],
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url($this->getBaseRoute()))
                ->withInput()
                ->with('validation', $this->validator)
                ->with('error', 'Gagal memperbarui pekerjaan. Mohon periksa inputan Anda.');
        }

        $nama = trim((string) $this->request->getPost('nama'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));
        $oldNama = $pekerjaan['nama'];

        $this->pekerjaanModel->update($id, [
            'nama'       => $nama,
            'keterangan' => $keterangan ?: null,
        ]);

        // Sinkronisasi pembaruan nama pekerjaan ke tabel data penduduk
        if ($oldNama !== $nama) {
            $db = \Config\Database::connect();
            if ($db->tableExists('penduduk')) {
                $db->table('penduduk')
                    ->where('pekerjaan', $oldNama)
                    ->update(['pekerjaan' => $nama]);
            }
        }

        return redirect()->to(base_url($this->getBaseRoute()))
            ->with('success', "Data pekerjaan '{$nama}' berhasil diperbarui.");
    }

    /**
     * Hapus data pekerjaan
     */
    public function delete($id)
    {
        $pekerjaan = $this->pekerjaanModel->find($id);
        if (!$pekerjaan) {
            return redirect()->to(base_url($this->getBaseRoute()))
                ->with('error', 'Data pekerjaan tidak ditemukan.');
        }

        $nama = $pekerjaan['nama'];

        // Cegah penghapusan jika pekerjaan masih digunakan oleh data penduduk
        $db = \Config\Database::connect();
        if ($db->tableExists('penduduk')) {
            $usedCount = $db->table('penduduk')
                ->where('pekerjaan', $nama)
                ->where('deleted_at IS NULL')
                ->countAllResults();

            if ($usedCount > 0) {
                return redirect()->to(base_url($this->getBaseRoute()))
                    ->with('error', "Pekerjaan '{$nama}' tidak dapat dihapus karena masih digunakan oleh {$usedCount} data penduduk.");
            }
        }

        $this->pekerjaanModel->delete($id);

        return redirect()->to(base_url($this->getBaseRoute()))
            ->with('success', "Data pekerjaan '{$nama}' berhasil dihapus.");
    }
}
