<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DesaModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;

class DesaController extends BaseController
{
    protected DesaModel $desaModel;
    protected KabupatenModel $kabupatenModel;
    protected KecamatanModel $kecamatanModel;

    public function __construct()
    {
        $this->desaModel      = new DesaModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->kecamatanModel = new KecamatanModel();
    }

    /**
     * Tampilkan daftar master desa
     */
    public function index()
    {
        $search      = $this->request->getGet('q');
        $kabupatenId = $this->request->getGet('kabupaten_id');
        $perPage     = 25;

        $desaList = $this->desaModel->getDesaPaginated(
            $search ? trim($search) : null,
            $kabupatenId ? (int)$kabupatenId : null,
            $perPage
        );

        $kabupatenList = cache()->remember('master_kabupaten_active_list', 86400, function() {
            return $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
        });

        return view('super_admin/desa/index', [
            'title'         => 'Master Data Desa - SiPelayan Desa',
            'desaList'      => $desaList,
            'kabupatenList' => $kabupatenList,
            'pager'         => $this->desaModel->pager,
            'search'        => $search,
            'kabupatenId'   => $kabupatenId,
            'perPage'       => $perPage,
        ]);
    }

    /**
     * Form tambah desa baru
     */
    public function create()
    {
        $kabupatenList = $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
        $kecamatanList = $this->kecamatanModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/desa/create', [
            'title'         => 'Tambah Desa Baru - Super Admin',
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan data desa baru
     */
    public function store()
    {
        $rules = [
            'kabupaten_id'    => 'required|is_not_unique[kabupaten.id]',
            'kode_kemendagri' => 'required|min_length[5]|max_length[15]|is_unique[desa.kode_kemendagri]',
            'nama_desa'       => 'required|min_length[2]|max_length[150]',
            'nama_kecamatan'  => 'required|min_length[2]|max_length[150]',
            'tenant_slug'     => 'required|min_length[2]|max_length[100]|alpha_dash|is_unique[desa.tenant_slug]',
            'email'           => 'permit_empty|valid_email|max_length[100]',
            'telepon'         => 'permit_empty|max_length[20]',
            'whatsapp_kades'  => 'permit_empty|max_length[20]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $slug = url_title(strtolower(trim($this->request->getPost('tenant_slug'))), '-', true);

        $data = [
            'kabupaten_id'     => (int) $this->request->getPost('kabupaten_id'),
            'kode_kemendagri'  => trim($this->request->getPost('kode_kemendagri')),
            'nama_desa'        => trim($this->request->getPost('nama_desa')),
            'nama_kecamatan'   => trim($this->request->getPost('nama_kecamatan')),
            'tenant_slug'      => $slug,
            'nama_kepala_desa' => trim($this->request->getPost('nama_kepala_desa') ?? ''),
            'nip_kepala_desa'  => trim($this->request->getPost('nip_kepala_desa') ?? ''),
            'alamat_kantor'    => trim($this->request->getPost('alamat_kantor') ?? ''),
            'telepon'          => trim($this->request->getPost('telepon') ?? ''),
            'email'            => trim($this->request->getPost('email') ?? ''),
            'whatsapp_kades'   => trim($this->request->getPost('whatsapp_kades') ?? ''),
            'website'          => trim($this->request->getPost('website') ?? ''),
            'kode_pos'         => trim($this->request->getPost('kode_pos') ?? ''),
            'visi'             => trim($this->request->getPost('visi') ?? ''),
            'misi'             => trim($this->request->getPost('misi') ?? ''),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle upload logo desa
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/logo';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $logoFile->getRandomName();
            $logoFile->move($uploadPath, $newName);
            $data['logo_path'] = 'uploads/logo/' . $newName;
        }

        $this->desaModel->insert($data);
        cache()->delete('master_desa_total_count');

        return redirect()->to('/super-admin/desa')->with('success', 'Data desa "' . esc($data['nama_desa']) . '" berhasil ditambahkan.');
    }

    /**
     * Form edit desa
     */
    public function edit(int $id)
    {
        $desa = $this->desaModel->find($id);
        if (!$desa) {
            return redirect()->to('/super-admin/desa')->with('error', 'Data desa tidak ditemukan.');
        }

        $kabupatenList = $this->kabupatenModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
        $kecamatanList = $this->kecamatanModel->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();

        return view('super_admin/desa/edit', [
            'title'         => 'Edit Desa - ' . $desa['nama_desa'],
            'desa'          => $desa,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Update data desa
     */
    public function update(int $id)
    {
        $desa = $this->desaModel->find($id);
        if (!$desa) {
            return redirect()->to('/super-admin/desa')->with('error', 'Data desa tidak ditemukan.');
        }

        $rules = [
            'kabupaten_id'    => 'required|is_not_unique[kabupaten.id]',
            'kode_kemendagri' => "required|min_length[5]|max_length[15]|is_unique[desa.kode_kemendagri,id,{$id}]",
            'nama_desa'       => 'required|min_length[2]|max_length[150]',
            'nama_kecamatan'  => 'required|min_length[2]|max_length[150]',
            'tenant_slug'     => "required|min_length[2]|max_length[100]|alpha_dash|is_unique[desa.tenant_slug,id,{$id}]",
            'email'           => 'permit_empty|valid_email|max_length[100]',
            'telepon'         => 'permit_empty|max_length[20]',
            'whatsapp_kades'  => 'permit_empty|max_length[20]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $slug = url_title(strtolower(trim($this->request->getPost('tenant_slug'))), '-', true);

        $data = [
            'kabupaten_id'     => (int) $this->request->getPost('kabupaten_id'),
            'kode_kemendagri'  => trim($this->request->getPost('kode_kemendagri')),
            'nama_desa'        => trim($this->request->getPost('nama_desa')),
            'nama_kecamatan'   => trim($this->request->getPost('nama_kecamatan')),
            'tenant_slug'      => $slug,
            'nama_kepala_desa' => trim($this->request->getPost('nama_kepala_desa') ?? ''),
            'nip_kepala_desa'  => trim($this->request->getPost('nip_kepala_desa') ?? ''),
            'alamat_kantor'    => trim($this->request->getPost('alamat_kantor') ?? ''),
            'telepon'          => trim($this->request->getPost('telepon') ?? ''),
            'email'            => trim($this->request->getPost('email') ?? ''),
            'whatsapp_kades'   => trim($this->request->getPost('whatsapp_kades') ?? ''),
            'website'          => trim($this->request->getPost('website') ?? ''),
            'kode_pos'         => trim($this->request->getPost('kode_pos') ?? ''),
            'visi'             => trim($this->request->getPost('visi') ?? ''),
            'misi'             => trim($this->request->getPost('misi') ?? ''),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle upload logo desa
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/logo';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $logoFile->getRandomName();
            $logoFile->move($uploadPath, $newName);
            $data['logo_path'] = 'uploads/logo/' . $newName;
        }

        $this->desaModel->update($id, $data);

        return redirect()->to('/super-admin/desa')->with('success', 'Data desa "' . esc($data['nama_desa']) . '" berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(int $id)
    {
        $desa = $this->desaModel->find($id);
        if (!$desa) {
            return redirect()->to('/super-admin/desa')->with('error', 'Data desa tidak ditemukan.');
        }

        $newStatus = $desa['is_active'] ? 0 : 1;
        $this->desaModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/super-admin/desa')->with('success', "Desa {$desa['nama_desa']} berhasil {$statusText}.");
    }

    /**
     * Hapus data desa (dengan pengecekan relasi)
     */
    public function delete(int $id)
    {
        $desa = $this->desaModel->find($id);
        if (!$desa) {
            return redirect()->to('/super-admin/desa')->with('error', 'Data desa tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $userCount = $db->table('users')->where('village_id', $id)->countAllResults();
        $suratCount = $db->table('permohonan_surat')->where('village_id', $id)->countAllResults();

        if ($userCount > 0 || $suratCount > 0) {
            return redirect()->to('/super-admin/desa')->with('error', "Tidak dapat menghapus desa {$desa['nama_desa']} karena masih memiliki {$userCount} pengguna dan {$suratCount} permohonan surat terkait. Silakan nonaktifkan status desa sebagai gantinya.");
        }

        $this->desaModel->delete($id);
        cache()->delete('master_desa_total_count');

        return redirect()->to('/super-admin/desa')->with('success', "Desa {$desa['nama_desa']} berhasil dihapus.");
    }
}
