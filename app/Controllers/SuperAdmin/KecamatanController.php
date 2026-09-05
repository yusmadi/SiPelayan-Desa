<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\KecamatanModel;
use App\Models\KabupatenModel;

class KecamatanController extends BaseController
{
    protected KecamatanModel $kecamatanModel;
    protected KabupatenModel $kabupatenModel;

    public function __construct()
    {
        $this->kecamatanModel = new KecamatanModel();
        $this->kabupatenModel = new KabupatenModel();
    }

    public function index()
    {
        $search      = $this->request->getGet('q');
        $kabupatenId = $this->request->getGet('kabupaten_id');
        $perPage     = 25;

        $kecamatanList = $this->kecamatanModel->getKecamatanPaginated(
            $search ? trim($search) : null,
            $kabupatenId ? (int)$kabupatenId : null,
            $perPage
        );

        $kabupatenList = cache()->remember('master_kabupaten_active_list', 86400, function() {
            return $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
        });

        return view('super_admin/kecamatan/index', [
            'title'          => 'Master Data Kecamatan - Super Admin',
            'kecamatanList'  => $kecamatanList,
            'kabupatenList'  => $kabupatenList,
            'pager'          => $this->kecamatanModel->pager,
            'search'         => $search,
            'kabupatenId'    => $kabupatenId,
            'perPage'        => $perPage,
        ]);
    }

    public function create()
    {
        $kabupatenList = $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/kecamatan/create', [
            'title'         => 'Tambah Kecamatan Baru - Super Admin',
            'kabupatenList' => $kabupatenList,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function store()
    {
        $rules = [
            'kabupaten_id'    => 'required|is_not_unique[kabupaten.id]',
            'kode_kemendagri' => 'required|min_length[4]|max_length[15]|is_unique[kecamatan.kode_kemendagri]',
            'nama'            => 'required|min_length[2]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kabupaten_id'    => (int) $this->request->getPost('kabupaten_id'),
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->kecamatanModel->insert($data);
        cache()->delete('master_kecamatan_total_count');

        return redirect()->to('/super-admin/kecamatan')->with('success', 'Kecamatan ' . esc($data['nama']) . ' berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kecamatan = $this->kecamatanModel->find($id);
        if (! $kecamatan) {
            return redirect()->to('/super-admin/kecamatan')->with('error', 'Data kecamatan tidak ditemukan.');
        }

        $kabupatenList = $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/kecamatan/edit', [
            'title'         => 'Edit Kecamatan - ' . $kecamatan['nama'],
            'kecamatan'     => $kecamatan,
            'kabupatenList' => $kabupatenList,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function update(int $id)
    {
        $kecamatan = $this->kecamatanModel->find($id);
        if (! $kecamatan) {
            return redirect()->to('/super-admin/kecamatan')->with('error', 'Data kecamatan tidak ditemukan.');
        }

        $rules = [
            'kabupaten_id'    => 'required|is_not_unique[kabupaten.id]',
            'kode_kemendagri' => "required|min_length[4]|max_length[15]|is_unique[kecamatan.kode_kemendagri,id,{$id}]",
            'nama'            => 'required|min_length[2]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kabupaten_id'    => (int) $this->request->getPost('kabupaten_id'),
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->kecamatanModel->update($id, $data);

        return redirect()->to('/super-admin/kecamatan')->with('success', 'Data kecamatan ' . esc($data['nama']) . ' berhasil diperbarui.');
    }

    public function toggleStatus(int $id)
    {
        $kecamatan = $this->kecamatanModel->find($id);
        if (! $kecamatan) {
            return redirect()->to('/super-admin/kecamatan')->with('error', 'Data kecamatan tidak ditemukan.');
        }

        $newStatus = $kecamatan['is_active'] ? 0 : 1;
        $this->kecamatanModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/super-admin/kecamatan')->with('success', "Kecamatan {$kecamatan['nama']} berhasil {$statusText}.");
    }

    public function delete(int $id)
    {
        $kecamatan = $this->kecamatanModel->find($id);
        if (! $kecamatan) {
            return redirect()->to('/super-admin/kecamatan')->with('error', 'Data kecamatan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $desaCount = $db->table('desa')->where('nama_kecamatan', $kecamatan['nama'])->countAllResults();

        if ($desaCount > 0) {
            return redirect()->to('/super-admin/kecamatan')->with('error', "Tidak dapat menghapus kecamatan {$kecamatan['nama']} karena masih memiliki {$desaCount} desa terkait.");
        }

        $this->kecamatanModel->delete($id);
        cache()->delete('master_kecamatan_total_count');

        return redirect()->to('/super-admin/kecamatan')->with('success', "Kecamatan {$kecamatan['nama']} berhasil dihapus.");
    }
}
