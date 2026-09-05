<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\KabupatenModel;
use App\Models\ProvinsiModel;

class KabupatenController extends BaseController
{
    protected KabupatenModel $kabupatenModel;
    protected ProvinsiModel $provinsiModel;

    public function __construct()
    {
        $this->kabupatenModel = new KabupatenModel();
        $this->provinsiModel  = new ProvinsiModel();
    }

    public function index()
    {
        $search     = $this->request->getGet('q');
        $provinsiId = $this->request->getGet('provinsi_id');
        $perPage    = 25;

        $kabupatenList = $this->kabupatenModel->getKabupatenPaginated(
            $search ? trim($search) : null,
            $provinsiId ? (int)$provinsiId : null,
            $perPage
        );

        $provinsiList = cache()->remember('master_provinsi_active_list', 86400, function() {
            return $this->provinsiModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
        });

        return view('super_admin/kabupaten/index', [
            'title'          => 'Master Data Kabupaten / Kota - Super Admin',
            'kabupatenList'  => $kabupatenList,
            'provinsiList'   => $provinsiList,
            'pager'          => $this->kabupatenModel->pager,
            'search'         => $search,
            'provinsiId'     => $provinsiId,
            'perPage'        => $perPage,
        ]);
    }

    public function create()
    {
        $provinsiList = $this->provinsiModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/kabupaten/create', [
            'title'        => 'Tambah Kabupaten / Kota Baru - Super Admin',
            'provinsiList' => $provinsiList,
            'validation'   => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function store()
    {
        $rules = [
            'provinsi_id'     => 'required|is_not_unique[provinsi.id]',
            'kode_kemendagri' => 'required|min_length[2]|max_length[10]|is_unique[kabupaten.kode_kemendagri]',
            'nama'            => 'required|min_length[3]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $provinsi = $this->provinsiModel->find((int)$this->request->getPost('provinsi_id'));

        $data = [
            'provinsi_id'     => (int) $this->request->getPost('provinsi_id'),
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'provinsi'        => $provinsi ? $provinsi['nama'] : '',
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->kabupatenModel->insert($data);
        cache()->delete('master_kabupaten_total_count');
        cache()->delete('master_kabupaten_active_list');

        return redirect()->to('/super-admin/kabupaten')->with('success', 'Kabupaten/Kota ' . esc($data['nama']) . ' berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kabupaten = $this->kabupatenModel->find($id);
        if (! $kabupaten) {
            return redirect()->to('/super-admin/kabupaten')->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $provinsiList = $this->provinsiModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/kabupaten/edit', [
            'title'        => 'Edit Kabupaten / Kota - ' . $kabupaten['nama'],
            'kabupaten'    => $kabupaten,
            'provinsiList' => $provinsiList,
            'validation'   => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function update(int $id)
    {
        $kabupaten = $this->kabupatenModel->find($id);
        if (! $kabupaten) {
            return redirect()->to('/super-admin/kabupaten')->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $rules = [
            'provinsi_id'     => 'required|is_not_unique[provinsi.id]',
            'kode_kemendagri' => "required|min_length[2]|max_length[10]|is_unique[kabupaten.kode_kemendagri,id,{$id}]",
            'nama'            => 'required|min_length[3]|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $provinsi = $this->provinsiModel->find((int)$this->request->getPost('provinsi_id'));

        $data = [
            'provinsi_id'     => (int) $this->request->getPost('provinsi_id'),
            'kode_kemendagri' => trim($this->request->getPost('kode_kemendagri')),
            'nama'            => trim($this->request->getPost('nama')),
            'provinsi'        => $provinsi ? $provinsi['nama'] : $kabupaten['provinsi'],
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->kabupatenModel->update($id, $data);

        return redirect()->to('/super-admin/kabupaten')->with('success', 'Data kabupaten ' . esc($data['nama']) . ' berhasil diperbarui.');
    }

    public function toggleStatus(int $id)
    {
        $kabupaten = $this->kabupatenModel->find($id);
        if (! $kabupaten) {
            return redirect()->to('/super-admin/kabupaten')->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $newStatus = $kabupaten['is_active'] ? 0 : 1;
        $this->kabupatenModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/super-admin/kabupaten')->with('success', "Kabupaten {$kabupaten['nama']} berhasil {$statusText}.");
    }

    public function delete(int $id)
    {
        $kabupaten = $this->kabupatenModel->find($id);
        if (! $kabupaten) {
            return redirect()->to('/super-admin/kabupaten')->with('error', 'Data kabupaten tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $desaCount = $db->table('desa')->where('kabupaten_id', $id)->countAllResults();
        $kecCount  = $db->table('kecamatan')->where('kabupaten_id', $id)->countAllResults();

        if ($desaCount > 0 || $kecCount > 0) {
            return redirect()->to('/super-admin/kabupaten')->with('error', "Tidak dapat menghapus kabupaten {$kabupaten['nama']} karena masih memiliki {$kecCount} kecamatan dan {$desaCount} desa terkait.");
        }

        $this->kabupatenModel->delete($id);
        cache()->delete('master_kabupaten_total_count');
        cache()->delete('master_kabupaten_active_list');

        return redirect()->to('/super-admin/kabupaten')->with('success', "Kabupaten {$kabupaten['nama']} berhasil dihapus.");
    }
}
