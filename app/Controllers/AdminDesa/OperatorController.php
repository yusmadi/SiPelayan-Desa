<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DesaModel;

class OperatorController extends BaseController
{
    protected UserModel $userModel;
    protected DesaModel $desaModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->desaModel = new DesaModel();
        $this->db        = \Config\Database::connect();
    }

    /**
     * Dapatkan ID role operator
     */
    private function getOperatorRoleId(): int
    {
        $role = $this->db->table('roles')->where('slug', 'operator')->get()->getRowArray();
        return $role ? (int)$role['id'] : 3;
    }

    /**
     * Halaman daftar Operator Desa
     */
    public function index()
    {
        $villageId = (int) session('village_id');
        $operatorRoleId = $this->getOperatorRoleId();
        $search = trim((string) $this->request->getGet('q'));

        $builder = $this->db->table('users')
            ->select('users.*, roles.name as role_name, roles.slug as role_slug')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.deleted_at IS NULL')
            ->where('users.role_id', $operatorRoleId);

        if ($villageId > 0) {
            $builder->where('users.village_id', $villageId);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.nama_lengkap', $search)
                ->orLike('users.email', $search)
                ->orLike('users.no_hp', $search)
                ->orLike('users.nik', $search)
                ->groupEnd();
        }

        $operators = $builder->orderBy('users.id', 'DESC')->get()->getResultArray();

        return view('admin_desa/operator/index', [
            'title'      => 'Manajemen Akun Operator ' . sebutan_desa(),
            'operators'  => $operators,
            'search'     => $search,
            'totalCount' => count($operators),
        ]);
    }

    /**
     * Form pembuatan akun Operator Desa
     */
    public function create()
    {
        $villageId = (int) session('village_id');
        $desa = $this->desaModel->find($villageId);

        return view('admin_desa/operator/create', [
            'title'      => 'Buat Akun Operator ' . sebutan_desa(),
            'desa'       => $desa,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan akun Operator Desa baru
     */
    public function store()
    {
        $villageId = (int) session('village_id');
        if ($villageId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Desa belum terasosiasi dengan akun Admin.');
        }

        $rules = [
            'nama_lengkap'     => 'required|min_length[3]|max_length[150]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'nik'              => 'permit_empty|numeric|exact_length[16]',
            'no_hp'            => 'permit_empty|min_length[8]|max_length[20]',
        ];

        $messages = [
            'password_confirm' => [
                'matches' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
                'required' => 'Konfirmasi kata sandi wajib diisi.',
            ],
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar di sistem. Gunakan email lain.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'password' => [
                'min_length' => 'Password minimal harus 8 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $operatorRoleId = $this->getOperatorRoleId();
        $nik = trim((string) $this->request->getPost('nik')) ?: null;

        $data = [
            'village_id'      => $villageId,
            'role_id'         => $operatorRoleId,
            'nik'             => $nik,
            'nama_lengkap'    => trim((string) $this->request->getPost('nama_lengkap')),
            'email'           => trim((string) $this->request->getPost('email')),
            'no_hp'           => trim((string) $this->request->getPost('no_hp')) ?: null,
            'password_hash'   => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'is_verified'     => 1,
            'is_nik_verified' => 0,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'is_banned'       => 0,
        ];

        $userId = $this->userModel->withoutTenantScope()->insert($data);

        if ($userId && $nik) {
            $this->db->table('penduduk')
                ->where('nik', $nik)
                ->where('village_id', $villageId)
                ->update(['user_id' => $userId]);
        }

        return redirect()->to('/admin-desa/operator')->with('success', 'Akun Operator ' . sebutan_desa() . ' <strong>' . esc($data['nama_lengkap']) . '</strong> berhasil dibuat.');
    }

    /**
     * Form edit Operator Desa
     */
    public function edit(int $id)
    {
        $villageId = (int) session('village_id');
        $operatorRoleId = $this->getOperatorRoleId();

        $operator = $this->userModel->withoutTenantScope()
            ->where('id', $id)
            ->where('village_id', $villageId)
            ->where('role_id', $operatorRoleId)
            ->where('deleted_at IS NULL')
            ->first();

        if (! $operator) {
            return redirect()->to('/admin-desa/operator')->with('error', 'Data Operator tidak ditemukan.');
        }

        return view('admin_desa/operator/edit', [
            'title'      => 'Edit Akun Operator - ' . $operator['nama_lengkap'],
            'operator'   => $operator,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Update akun Operator Desa
     */
    public function update(int $id)
    {
        $villageId = (int) session('village_id');
        $operatorRoleId = $this->getOperatorRoleId();

        $operator = $this->userModel->withoutTenantScope()
            ->where('id', $id)
            ->where('village_id', $villageId)
            ->where('role_id', $operatorRoleId)
            ->where('deleted_at IS NULL')
            ->first();

        if (! $operator) {
            return redirect()->to('/admin-desa/operator')->with('error', 'Data Operator tidak ditemukan.');
        }

        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'email'        => "required|valid_email|is_unique[users.email,id,{$id}]",
            'nik'          => 'permit_empty|numeric|exact_length[16]',
            'no_hp'        => 'permit_empty|min_length[8]|max_length[20]',
            'password'     => 'permit_empty|min_length[8]',
            'password_confirm' => 'permit_empty|matches[password]',
        ];

        $messages = [
            'password_confirm' => [
                'matches' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
            ],
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar di sistem. Gunakan email lain.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'password' => [
                'min_length' => 'Password minimal harus 8 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $nik = trim((string) $this->request->getPost('nik')) ?: null;

        $data = [
            'nik'          => $nik,
            'nama_lengkap' => trim((string) $this->request->getPost('nama_lengkap')),
            'email'        => trim((string) $this->request->getPost('email')),
            'no_hp'        => trim((string) $this->request->getPost('no_hp')) ?: null,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $password = (string) $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->withoutTenantScope()->update($id, $data);

        return redirect()->to('/admin-desa/operator')->with('success', 'Data Operator <strong>' . esc($data['nama_lengkap']) . '</strong> berhasil diperbarui.');
    }

    /**
     * Toggle status aktif Operator
     */
    public function toggleStatus(int $id)
    {
        $villageId = (int) session('village_id');
        $operatorRoleId = $this->getOperatorRoleId();

        $operator = $this->userModel->withoutTenantScope()
            ->where('id', $id)
            ->where('village_id', $villageId)
            ->where('role_id', $operatorRoleId)
            ->where('deleted_at IS NULL')
            ->first();

        if (! $operator) {
            return redirect()->to('/admin-desa/operator')->with('error', 'Data Operator tidak ditemukan.');
        }

        $newStatus = $operator['is_active'] ? 0 : 1;
        $this->userModel->withoutTenantScope()->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/admin-desa/operator')->with('success', "Akun Operator {$operator['nama_lengkap']} berhasil {$statusText}.");
    }

    /**
     * Hapus (soft delete) Operator
     */
    public function delete(int $id)
    {
        $villageId = (int) session('village_id');
        $operatorRoleId = $this->getOperatorRoleId();

        $operator = $this->userModel->withoutTenantScope()
            ->where('id', $id)
            ->where('village_id', $villageId)
            ->where('role_id', $operatorRoleId)
            ->where('deleted_at IS NULL')
            ->first();

        if (! $operator) {
            return redirect()->to('/admin-desa/operator')->with('error', 'Data Operator tidak ditemukan.');
        }

        $this->userModel->withoutTenantScope()->delete($id);

        return redirect()->to('/admin-desa/operator')->with('success', "Akun Operator {$operator['nama_lengkap']} berhasil dihapus.");
    }
}
