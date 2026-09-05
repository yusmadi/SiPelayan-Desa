<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;
use App\Models\PengaduanModel;
use App\Models\UserModel;

class PengaduanController extends BaseController
{
    public function index()
    {
        $model = new PengaduanModel();
        
        $statusFilter   = $this->request->getGet('status');
        $kategoriFilter = $this->request->getGet('kategori');
        $search         = $this->request->getGet('q');

        $query = $model->select('pengaduan.*, users.nama_lengkap as nama_pelapor, users.email as email_pelapor')
            ->join('users', 'users.id = pengaduan.pelapor_id', 'left');

        if (!empty($statusFilter)) {
            $query->where('pengaduan.status', $statusFilter);
        }

        if (!empty($kategoriFilter)) {
            $query->where('pengaduan.kategori', $kategoriFilter);
        }

        if (!empty($search)) {
            $query->groupStart()
                ->like('pengaduan.judul', $search)
                ->orLike('pengaduan.no_tiket', $search)
                ->orLike('pengaduan.isi_laporan', $search)
                ->orLike('users.nama_lengkap', $search)
                ->groupEnd();
        }

        $pengaduanList = $query->orderBy('pengaduan.id', 'DESC')->findAll();

        return view('admin_desa/pengaduan/index', [
            'title'          => 'Kelola Pengaduan Warga - Admin Desa',
            'pengaduanList'  => $pengaduanList,
            'kategoriList'   => PengaduanModel::KATEGORI_LIST,
            'statusLabels'   => PengaduanModel::STATUS_LABELS,
            'statusColors'   => PengaduanModel::STATUS_COLORS,
            'statusFilter'   => $statusFilter,
            'kategoriFilter' => $kategoriFilter,
            'search'         => $search,
        ]);
    }

    public function detail(int $id)
    {
        $model = new PengaduanModel();
        $pengaduan = $model->select('pengaduan.*, users.nama_lengkap as nama_pelapor, users.email as email_pelapor, users.no_hp as hp_pelapor, users.nik as nik_pelapor')
            ->join('users', 'users.id = pengaduan.pelapor_id', 'left')
            ->find($id);

        if (!$pengaduan) {
            return redirect()->to(session('role_slug') === 'operator' ? '/operator/pengaduan' : '/admin-desa/pengaduan')->with('error', 'Pengaduan tidak ditemukan.');
        }

        $petugas = null;
        if (!empty($pengaduan['ditangani_oleh'])) {
            $userModel = new UserModel();
            $petugas = $userModel->find($pengaduan['ditangani_oleh']);
        }

        return view('admin_desa/pengaduan/detail', [
            'title'        => 'Detail Pengaduan #' . $pengaduan['no_tiket'],
            'pengaduan'    => $pengaduan,
            'petugas'      => $petugas,
            'statusLabels' => PengaduanModel::STATUS_LABELS,
            'statusColors' => PengaduanModel::STATUS_COLORS,
        ]);
    }

    public function tanggapi(int $id)
    {
        $model = new PengaduanModel();
        $pengaduan = $model->find($id);

        if (!$pengaduan) {
            return redirect()->back()->with('error', 'Pengaduan tidak ditemukan.');
        }

        $rules = [
            'status'    => 'required|in_list[open,in_progress,resolved,closed,rejected]',
            'tanggapan' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = $this->request->getPost('status');
        $updateData = [
            'status'         => $status,
            'tanggapan'      => trim($this->request->getPost('tanggapan')),
            'ditangani_oleh' => session('user_id'),
        ];

        if (in_array($status, ['resolved', 'closed', 'rejected'])) {
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
        }

        $model->update($id, $updateData);

        $redirectPrefix = session('role_slug') === 'operator' ? '/operator/pengaduan' : '/admin-desa/pengaduan';
        return redirect()->to($redirectPrefix . '/' . $id)->with('success', 'Tanggapan dan status pengaduan berhasil disimpan.');
    }
}
