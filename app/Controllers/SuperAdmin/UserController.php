<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DesaModel;

class UserController extends BaseController
{
    protected UserModel $userModel;
    protected DesaModel $desaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->desaModel = new DesaModel();
    }

    /**
     * Tampilkan daftar user dengan server-side pagination & filter efisien
     */
    public function index()
    {
        $search    = $this->request->getGet('q');
        $roleId    = $this->request->getGet('role_id');
        $villageId = $this->request->getGet('village_id');
        $perPage   = 15;
        $page      = (int) ($this->request->getGet('page') ?? 1);
        $page      = max(1, $page);

        $db = \Config\Database::connect();

        // 1. Query Builder untuk Menghitung Total
        $countBuilder = $db->table('users')
            ->where('users.deleted_at IS NULL');

        if (!empty($search)) {
            $search = trim($search);
            $countBuilder->join('desa', 'desa.id = users.village_id', 'left')
                ->groupStart()
                ->like('users.nama_lengkap', $search)
                ->orLike('users.email', $search)
                ->orLike('users.nik', $search)
                ->orLike('users.no_hp', $search)
                ->orLike('desa.nama_desa', $search)
                ->groupEnd();
        }

        if (!empty($roleId)) {
            $countBuilder->where('users.role_id', (int)$roleId);
        }

        if (!empty($villageId)) {
            $countBuilder->where('users.village_id', (int)$villageId);
        }

        $totalUsers = $countBuilder->countAllResults();

        // 2. Setup Pager
        $pager = service('pager');
        $pager->store('default', $page, $perPage, $totalUsers);
        $offset = ($page - 1) * $perPage;

        // 3. Query Data Pengguna (Hanya kolom yang dibutuhkan)
        $builder = $db->table('users')
            ->select('users.id, users.village_id, users.role_id, users.nik, users.nama_lengkap, users.email, users.no_hp, users.is_active, users.is_nik_verified, users.sk_kades_path, roles.name as role_name, roles.slug as role_slug, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('desa', 'desa.id = users.village_id', 'left')
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
            ->where('users.deleted_at IS NULL');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.nama_lengkap', $search)
                ->orLike('users.email', $search)
                ->orLike('users.nik', $search)
                ->orLike('users.no_hp', $search)
                ->orLike('desa.nama_desa', $search)
                ->groupEnd();
        }

        if (!empty($roleId)) {
            $builder->where('users.role_id', (int)$roleId);
        }

        if (!empty($villageId)) {
            $builder->where('users.village_id', (int)$villageId);
        }

        $userList = $builder->orderBy('users.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $roles = $db->table('roles')->orderBy('level', 'ASC')->get()->getResultArray();

        // Ambil informasi desa yang sedang difilter jika ada
        $selectedDesa = null;
        if (!empty($villageId)) {
            $selectedDesa = $db->table('desa')
                ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', (int)$villageId)
                ->get()
                ->getRowArray();
        }

        return view('super_admin/users/index', [
            'title'        => 'Manajemen Pengguna - Super Admin',
            'userList'     => $userList,
            'roles'        => $roles,
            'selectedDesa' => $selectedDesa,
            'search'       => $search,
            'roleId'       => $roleId,
            'villageId'    => $villageId,
            'totalUsers'   => $totalUsers,
            'pager'        => $pager,
            'perPage'      => $perPage,
            'currentPage'  => $page,
        ]);
    }

    /**
     * Endpoint AJAX untuk pencarian desa secara asinkron (respons cepat < 10ms & hemat memori)
     */
    public function searchDesa()
    {
        $q = trim((string) $this->request->getGet('q'));

        $db = \Config\Database::connect();
        $builder = $db->table('desa')
            ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
            ->where('desa.is_active', 1);

        if ($q !== '') {
            $builder->groupStart()
                ->like('desa.nama_desa', $q)
                ->orLike('desa.nama_kecamatan', $q)
                ->orLike('kabupaten.nama', $q)
                ->groupEnd();
        }

        $results = $builder->orderBy('desa.nama_desa', 'ASC')
            ->limit(25)
            ->get()
            ->getResultArray();

        $data = array_map(static function ($item) {
            $label = 'Desa ' . $item['nama_desa'];
            if (!empty($item['nama_kecamatan'])) {
                $label .= ' (Kec. ' . $item['nama_kecamatan'];
                if (!empty($item['nama_kabupaten'])) {
                    $label .= ', ' . $item['nama_kabupaten'];
                }
                $label .= ')';
            }

            return [
                'id'   => (int) $item['id'],
                'text' => $label,
            ];
        }, $results);

        return $this->response->setJSON($data);
    }

    /**
     * Form tambah user baru
     */
    public function create()
    {
        $db = \Config\Database::connect();
        $roles = $db->table('roles')->orderBy('level', 'ASC')->get()->getResultArray();

        $oldVillageId = old('village_id');
        $selectedDesa = null;
        if (!empty($oldVillageId)) {
            $selectedDesa = $db->table('desa')
                ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', (int)$oldVillageId)
                ->get()
                ->getRowArray();
        }

        return view('super_admin/users/create', [
            'title'        => 'Tambah Pengguna Baru - Super Admin',
            'roles'        => $roles,
            'selectedDesa' => $selectedDesa,
            'validation'   => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan user baru
     */
    public function store()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'role_id'      => 'required|is_not_unique[roles.id]',
            'nik'          => 'permit_empty|numeric|exact_length[16]',
            'no_hp'        => 'permit_empty|min_length[8]|max_length[20]',
        ];

        $roleId = (int)$this->request->getPost('role_id');
        // Jika bukan Super Admin (role_id != 1), wajib pilih desa
        if ($roleId !== 1) {
            $rules['village_id'] = 'required|is_not_unique[desa.id]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $villageId = ($roleId === 1) ? null : ((int)$this->request->getPost('village_id') ?: null);
        $nik       = trim((string)$this->request->getPost('nik')) ?: null;

        $data = [
            'village_id'      => $villageId,
            'role_id'         => $roleId,
            'nik'             => $nik,
            'nama_lengkap'    => trim($this->request->getPost('nama_lengkap')),
            'email'           => trim($this->request->getPost('email')),
            'no_hp'           => trim((string)$this->request->getPost('no_hp')) ?: null,
            'password_hash'   => password_hash((string)$this->request->getPost('password'), PASSWORD_BCRYPT),
            'is_verified'     => 1,
            'is_nik_verified' => $this->request->getPost('is_nik_verified') ? 1 : 0,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'is_banned'       => 0,
        ];

        $userId = $this->userModel->withoutTenantScope()->insert($data);

        if ($userId && $nik && $villageId) {
            $db = \Config\Database::connect();
            $db->table('penduduk')
                ->where('nik', $nik)
                ->where('village_id', $villageId)
                ->update(['user_id' => $userId]);
        }

        return redirect()->to('/super-admin/users')->with('success', 'Pengguna ' . esc($data['nama_lengkap']) . ' berhasil ditambahkan.');
    }

    /**
     * Form edit user
     */
    public function edit(int $id)
    {
        $user = $this->userModel->withoutTenantScope()->find($id);
        if (! $user) {
            return redirect()->to('/super-admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $roles = $db->table('roles')->orderBy('level', 'ASC')->get()->getResultArray();

        $villageId = old('village_id', $user['village_id']);
        $selectedDesa = null;
        if (!empty($villageId)) {
            $selectedDesa = $db->table('desa')
                ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', (int)$villageId)
                ->get()
                ->getRowArray();
        }

        return view('super_admin/users/edit', [
            'title'        => 'Edit Pengguna - ' . $user['nama_lengkap'],
            'user'         => $user,
            'roles'        => $roles,
            'selectedDesa' => $selectedDesa,
            'validation'   => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Update data user
     */
    public function update(int $id)
    {
        $user = $this->userModel->withoutTenantScope()->find($id);
        if (! $user) {
            return redirect()->to('/super-admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'email'        => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id'      => 'required|is_not_unique[roles.id]',
            'nik'          => 'permit_empty|numeric|exact_length[16]',
            'no_hp'        => 'permit_empty|min_length[8]|max_length[20]',
            'password'     => 'permit_empty|min_length[8]',
        ];

        $roleId = (int)$this->request->getPost('role_id');
        if ($roleId !== 1) {
            $rules['village_id'] = 'required|is_not_unique[desa.id]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $villageId = ($roleId === 1) ? null : ((int)$this->request->getPost('village_id') ?: null);
        $nik       = trim((string)$this->request->getPost('nik')) ?: null;

        $data = [
            'village_id'      => $villageId,
            'role_id'         => $roleId,
            'nik'             => $nik,
            'nama_lengkap'    => trim($this->request->getPost('nama_lengkap')),
            'email'           => trim($this->request->getPost('email')),
            'no_hp'           => trim((string)$this->request->getPost('no_hp')) ?: null,
            'is_nik_verified' => $this->request->getPost('is_nik_verified') ? 1 : 0,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $password = (string)$this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->withoutTenantScope()->update($id, $data);

        return redirect()->to('/super-admin/users')->with('success', 'Data pengguna ' . esc($data['nama_lengkap']) . ' berhasil diperbarui.');
    }

    /**
     * Toggle status aktif user
     */
    public function toggleStatus(int $id)
    {
        $user = $this->userModel->withoutTenantScope()->find($id);
        if (! $user) {
            return redirect()->to('/super-admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        if ($id === (int)session('user_id')) {
            return redirect()->to('/super-admin/users')->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->withoutTenantScope()->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/super-admin/users')->with('success', "Pengguna {$user['nama_lengkap']} berhasil {$statusText}.");
    }

    /**
     * Soft delete user
     */
    public function delete(int $id)
    {
        $user = $this->userModel->withoutTenantScope()->find($id);
        if (! $user) {
            return redirect()->to('/super-admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        if ($id === (int)session('user_id')) {
            return redirect()->to('/super-admin/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $this->userModel->withoutTenantScope()->delete($id);

        return redirect()->to('/super-admin/users')->with('success', "Pengguna {$user['nama_lengkap']} berhasil dihapus.");
    }
}
