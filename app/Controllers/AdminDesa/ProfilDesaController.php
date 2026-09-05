<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;
use App\Models\DesaModel;

class ProfilDesaController extends BaseController
{
    protected DesaModel $desaModel;

    public function __construct()
    {
        $this->desaModel = new DesaModel();
    }

    public function index()
    {
        $villageId = (int) session('village_id');
        $desa = $this->desaModel->getDesaWithKabupaten($villageId ?: 1);

        if (!$desa) {
            return redirect()->to('/admin-desa/dashboard')->with('error', 'Data desa tidak ditemukan.');
        }

        return view('admin_desa/profil/index', [
            'title'      => 'Pengaturan & Profil Desa - ' . ($desa['nama_desa'] ?? 'SiPelayan Desa'),
            'desa'       => $desa,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    public function update()
    {
        $villageId = (int) session('village_id');
        $desa = $this->desaModel->find($villageId ?: 1);

        if (!$desa) {
            return redirect()->to('/admin-desa/dashboard')->with('error', 'Data desa tidak ditemukan.');
        }

        $rules = [
            'nama_kepala_desa' => 'permit_empty|max_length[150]',
            'nip_kepala_desa'  => 'permit_empty|max_length[50]',
            'alamat_kantor'    => 'permit_empty',
            'telepon'          => 'permit_empty|max_length[30]',
            'email'            => 'permit_empty|valid_email|max_length[100]',
            'whatsapp_kades'   => 'permit_empty|max_length[30]',
            'website'          => 'permit_empty|max_length[150]',
            'kode_pos'         => 'permit_empty|max_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
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

        $this->desaModel->update($desa['id'], $data);

        return redirect()->to('/admin-desa/profil')->with('success', 'Profil dan Logo Desa berhasil diperbarui.');
    }
}
