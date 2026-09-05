<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\ProvinsiModel;

class ProvinsiController extends BaseController
{
    protected ProvinsiModel $provinsiModel;

    public function __construct()
    {
        $this->provinsiModel = new ProvinsiModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');

        if (empty($search)) {
            $provinsiList = cache()->remember('master_provinsi_with_count', 86400, function() {
                return $this->provinsiModel->getProvinsiWithCount();
            });
        } else {
            $provinsiList = $this->provinsiModel->getProvinsiWithCount(null, trim($search));
        }

        return view('super_admin/provinsi/index', [
            'title'        => 'Master Data Provinsi - Super Admin',
            'provinsiList' => $provinsiList,
            'search'       => $search,
            'totalProvinsi'=> count($provinsiList),
        ]);
    }

    public function create()
    {
        return view('super_admin/provinsi/create', [
            'title'      => 'Tambah Provinsi Baru - Super Admin',
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function store()
    {
        $rules = [
            'kode_kemendagri' => 'required|min_length[2]|max_length[10]|is_unique[provinsi.kode_kemendagri]',
            'nama'            => 'required|min_length[3]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->provinsiModel->insert($data);
        cache()->delete('master_provinsi_with_count');
        cache()->delete('master_provinsi_active_list');

        return redirect()->to('/super-admin/provinsi')->with('success', 'Provinsi ' . esc($data['nama']) . ' berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $provinsi = $this->provinsiModel->find($id);
        if (! $provinsi) {
            return redirect()->to('/super-admin/provinsi')->with('error', 'Data provinsi tidak ditemukan.');
        }

        return view('super_admin/provinsi/edit', [
            'title'      => 'Edit Provinsi - ' . $provinsi['nama'],
            'provinsi'   => $provinsi,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function update(int $id)
    {
        $provinsi = $this->provinsiModel->find($id);
        if (! $provinsi) {
            return redirect()->to('/super-admin/provinsi')->with('error', 'Data provinsi tidak ditemukan.');
        }

        $rules = [
            'kode_kemendagri' => "required|min_length[2]|max_length[10]|is_unique[provinsi.kode_kemendagri,id,{$id}]",
            'nama'            => 'required|min_length[3]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->provinsiModel->update($id, $data);
        cache()->delete('master_provinsi_with_count');
        cache()->delete('master_provinsi_active_list');

        return redirect()->to('/super-admin/provinsi')->with('success', 'Data provinsi ' . esc($data['nama']) . ' berhasil diperbarui.');
    }

    public function toggleStatus(int $id)
    {
        $provinsi = $this->provinsiModel->find($id);
        if (! $provinsi) {
            return redirect()->to('/super-admin/provinsi')->with('error', 'Data provinsi tidak ditemukan.');
        }

        $newStatus = $provinsi['is_active'] ? 0 : 1;
        $this->provinsiModel->update($id, ['is_active' => $newStatus]);
        cache()->delete('master_provinsi_with_count');
        cache()->delete('master_provinsi_active_list');

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/super-admin/provinsi')->with('success', "Provinsi {$provinsi['nama']} berhasil {$statusText}.");
    }

    public function delete(int $id)
    {
        $provinsi = $this->provinsiModel->find($id);
        if (! $provinsi) {
            return redirect()->to('/super-admin/provinsi')->with('error', 'Data provinsi tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $kabCount = $db->table('kabupaten')->where('provinsi_id', $id)->countAllResults();

        if ($kabCount > 0) {
            return redirect()->to('/super-admin/provinsi')->with('error', "Tidak dapat menghapus provinsi {$provinsi['nama']} karena masih memiliki {$kabCount} kabupaten terkait.");
        }

        $this->provinsiModel->delete($id);
        cache()->delete('master_provinsi_with_count');
        cache()->delete('master_provinsi_active_list');

        return redirect()->to('/super-admin/provinsi')->with('success', "Provinsi {$provinsi['nama']} berhasil dihapus.");
    }
}
