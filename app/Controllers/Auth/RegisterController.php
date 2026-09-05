<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    public function index()
    {
        if (session()->has('is_logged_in') && session('is_logged_in')) {
            return redirect()->to('/warga/dashboard');
        }

        $db = \Config\Database::connect();
        $oldVillageId = old('village_id');
        $selectedDesa = null;

        if (! empty($oldVillageId)) {
            $selectedDesa = $db->table('desa')
                ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', (int) $oldVillageId)
                ->where('desa.is_active', 1)
                ->get()
                ->getRowArray();
        }

        return view('auth/register', [
            'title'        => 'Daftar Akun Warga - SiPelayan Desa',
            'selectedDesa' => $selectedDesa,
        ]);
    }

    /**
     * Tampilan form registrasi khusus Kepala Desa (Admin Desa)
     */
    public function registerDesa()
    {
        if (session()->has('is_logged_in') && session('is_logged_in')) {
            return redirect()->to('/admin-desa/dashboard');
        }

        $db = \Config\Database::connect();
        $oldVillageId = old('village_id');
        $selectedDesa = null;

        if (! empty($oldVillageId)) {
            $selectedDesa = $db->table('desa')
                ->select('desa.id, desa.nama_desa, desa.nama_kecamatan, kabupaten.nama as nama_kabupaten')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', (int) $oldVillageId)
                ->where('desa.is_active', 1)
                ->get()
                ->getRowArray();
        }

        return view('auth/register_desa', [
            'title'        => 'Daftarkan Desa (Kepala Desa) - SiPelayan Desa',
            'selectedDesa' => $selectedDesa,
        ]);
    }

    /**
     * Endpoint AJAX untuk pencarian desa secara asinkron (respons cepat & hemat memori)
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
            ->limit(30)
            ->get()
            ->getResultArray();

        $data = array_map(static function ($item) {
            $label = 'Desa ' . $item['nama_desa'];
            if (! empty($item['nama_kecamatan'])) {
                $label .= ' (Kec. ' . $item['nama_kecamatan'];
                if (! empty($item['nama_kabupaten'])) {
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

    public function proses()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'nik'          => 'required|numeric|exact_length[16]',
            'village_id'   => 'required|is_not_unique[desa.id]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.',
            ],
            'nik' => [
                'exact_length' => 'NIK harus berjumlah tepat 16 digit angka.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $userModel = new UserModel();

        $villageId = (int) $this->request->getPost('village_id');
        $nik       = trim((string) $this->request->getPost('nik'));
        $nama      = trim((string) $this->request->getPost('nama_lengkap'));
        $email     = trim((string) $this->request->getPost('email'));
        $password  = (string) $this->request->getPost('password');

        // Cek apakah akun dengan role 'Admin Desa' untuk desa terkait sudah terdaftar dan aktif
        $adminDesa = $db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.village_id', $villageId)
            ->groupStart()
                ->where('roles.slug', 'admin_desa')
                ->orWhere('users.role_id', 2)
            ->groupEnd()
            ->where('users.deleted_at', null)
            ->where('users.is_active', 1)
            ->get()
            ->getRowArray();

        if (! $adminDesa) {
            $desa = $db->table('desa')->where('id', $villageId)->get()->getRowArray();
            $namaDesa = $desa ? 'Desa ' . $desa['nama_desa'] : 'Desa yang dipilih';

            return redirect()->back()->withInput()->with('swal_error', [
                'icon'  => 'warning',
                'title' => 'Desa Belum Terdaftar',
                'text'  => "Mohon maaf, {$namaDesa} belum terdaftar di sistem SiPelayan Desa (belum memiliki akun Admin Desa aktif). Silakan hubungi dan minta Kepala Desa Anda untuk mendaftarkan desanya terlebih dahulu.",
            ]);
        }

        // Cek apakah NIK sudah digunakan oleh akun user lain
        $existingUser = $userModel->withoutTenantScope()
            ->where('nik', $nik)
            ->first();

        if ($existingUser) {
            return redirect()->back()->withInput()->with('swal_error', [
                'icon'  => 'error',
                'title' => 'NIK Sudah Terdaftar',
                'text'  => "NIK {$nik} sudah memiliki akun di sistem. Silakan login ke akun Anda atau hubungi admin desa jika membutuhkan bantuan.",
            ]);
        }

        // Cek apakah NIK dan Nama Lengkap tercatat sebagai penduduk di desa domisili yang dipilih
        $penduduk = $db->table('penduduk')
            ->where('nik', $nik)
            ->where('village_id', $villageId)
            ->where('LOWER(TRIM(nama_lengkap))', strtolower($nama))
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (! $penduduk) {
            $desa = $db->table('desa')->where('id', $villageId)->get()->getRowArray();
            $namaDesa = $desa ? 'Desa ' . $desa['nama_desa'] : 'desa yang dipilih';

            return redirect()->back()->withInput()->with('swal_error', [
                'icon'  => 'error',
                'title' => 'Data Penduduk Tidak Ditemukan',
                'text'  => "NIK ({$nik}) dan Nama Lengkap ({$nama}) tidak tercatat sebagai penduduk di {$namaDesa}. Pastikan NIK, Nama Lengkap sesuai KTP, dan Desa Domisili yang Anda pilih sudah benar.",
            ]);
        }

        // Cek jika data penduduk sudah terhubung ke akun user lain
        if (! empty($penduduk['user_id'])) {
            $linkedUser = $userModel->withoutTenantScope()->find($penduduk['user_id']);
            if ($linkedUser) {
                return redirect()->back()->withInput()->with('swal_error', [
                    'icon'  => 'error',
                    'title' => 'Akun Sudah Terhubung',
                    'text'  => 'Data kependudukan ini telah terhubung dengan akun pengguna lain. Silakan login menggunakan akun yang sudah terdaftar.',
                ]);
            }
        }

        $userData = [
            'village_id'      => $villageId,
            'role_id'         => 4, // Role Warga
            'nik'             => $nik,
            'nama_lengkap'    => $nama,
            'email'           => $email,
            'password_hash'   => password_hash($password, PASSWORD_BCRYPT),
            'is_verified'     => 1,
            'is_nik_verified' => 1,
            'is_active'       => 1,
            'is_banned'       => 0,
        ];

        $userId = $userModel->withoutTenantScope()->insert($userData);

        if ($userId) {
            // Link ke penduduk jika data kependudukan sudah ada
            $db->table('penduduk')
                ->where('id', $penduduk['id'])
                ->update(['user_id' => $userId]);

            return redirect()->to('/auth/login')->with('success', 'Registrasi berhasil! Data Anda terverifikasi sebagai penduduk desa. Silakan login.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mendaftar. Silakan coba kembali.');
    }

    /**
     * Proses pendaftaran desa & akun Kepala Desa (Admin Desa)
     * Status akun diset is_active = 0 dan harus diaktifkan oleh Super Administrator.
     */
    public function prosesDesa()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'nik'          => 'required|numeric|exact_length[16]',
            'no_hp'        => 'required|min_length[8]|max_length[20]',
            'village_id'   => 'required|is_not_unique[desa.id]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'sk_kades'     => 'uploaded[sk_kades]|max_size[sk_kades,5120]|ext_in[sk_kades,pdf,jpg,jpeg,png]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau hubungi Super Administrator.',
            ],
            'nik' => [
                'exact_length' => 'NIK harus berjumlah tepat 16 digit angka.',
            ],
            'sk_kades' => [
                'uploaded' => 'Wajib mengunggah berkas SK Kepala Desa / Surat Tugas.',
                'max_size' => 'Ukuran berkas SK Kepala Desa maksimal 5 MB.',
                'ext_in'   => 'Format berkas SK Kepala Desa harus PDF, JPG, JPEG, atau PNG.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $userModel = new UserModel();

        $villageId = (int) $this->request->getPost('village_id');
        $nik       = trim((string) $this->request->getPost('nik'));
        $nama      = trim((string) $this->request->getPost('nama_lengkap'));
        $email     = trim((string) $this->request->getPost('email'));
        $noHp      = trim((string) $this->request->getPost('no_hp'));
        $password  = (string) $this->request->getPost('password');

        // Cek apakah sudah ada akun Admin Desa yang aktif di desa ini
        $activeAdminDesa = $db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.village_id', $villageId)
            ->groupStart()
                ->where('roles.slug', 'admin_desa')
                ->orWhere('users.role_id', 2)
            ->groupEnd()
            ->where('users.deleted_at', null)
            ->where('users.is_active', 1)
            ->get()
            ->getRowArray();

        if ($activeAdminDesa) {
            $desa = $db->table('desa')->where('id', $villageId)->get()->getRowArray();
            $namaDesa = $desa ? 'Desa ' . $desa['nama_desa'] : 'Desa terpilih';

            return redirect()->back()->withInput()->with('swal_error', [
                'icon'  => 'warning',
                'title' => 'Desa Sudah Memiliki Admin Aktif',
                'text'  => "{$namaDesa} sudah memiliki akun Kepala Desa / Admin Desa yang aktif ({$activeAdminDesa['nama_lengkap']}). Silakan hubungi Super Administrator jika ada pergantian pejabat desa.",
            ]);
        }

        // Cek apakah NIK sudah digunakan
        $existingUser = $userModel->withoutTenantScope()
            ->where('nik', $nik)
            ->first();

        if ($existingUser) {
            return redirect()->back()->withInput()->with('swal_error', [
                'icon'  => 'error',
                'title' => 'NIK Sudah Terdaftar',
                'text'  => "NIK {$nik} sudah terdaftar di sistem. Silakan login atau hubungi Super Administrator.",
            ]);
        }

        // Handle upload berkas SK Kepala Desa
        $skFile = $this->request->getFile('sk_kades');
        $skPath = null;

        if ($skFile && $skFile->isValid() && ! $skFile->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/sk_kades';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newName = $skFile->getRandomName();
            $skFile->move($uploadDir, $newName);
            $skPath = 'uploads/sk_kades/' . $newName;
        }

        // Simpan data akun Kepala Desa (is_active = 0)
        $userData = [
            'village_id'      => $villageId,
            'role_id'         => 2, // Role Admin Desa
            'nik'             => $nik,
            'nama_lengkap'    => $nama,
            'email'           => $email,
            'no_hp'           => $noHp,
            'password_hash'   => password_hash($password, PASSWORD_BCRYPT),
            'avatar_path'     => null,
            'sk_kades_path'   => $skPath,
            'is_verified'     => 0,
            'is_nik_verified' => 1,
            'is_active'       => 0, // Akun disetting tidak aktif (is_active=0), menunggu aktivasi Super Administrator
            'is_banned'       => 0,
        ];

        $userId = $userModel->withoutTenantScope()->insert($userData);

        if ($userId) {
            // Update nama kades di data profil desa jika masih kosong
            $desa = $db->table('desa')->where('id', $villageId)->get()->getRowArray();
            if ($desa) {
                $desaUpdate = [];
                if (empty($desa['nama_kepala_desa'])) {
                    $desaUpdate['nama_kepala_desa'] = $nama;
                }
                if (empty($desa['whatsapp_kades'])) {
                    $desaUpdate['whatsapp_kades'] = $noHp;
                }
                if (! empty($desaUpdate)) {
                    $db->table('desa')->where('id', $villageId)->update($desaUpdate);
                }
            }

            // Link ke tabel penduduk jika NIK ada di database kependudukan desa
            $db->table('penduduk')
                ->where('nik', $nik)
                ->where('village_id', $villageId)
                ->update(['user_id' => $userId]);

            return redirect()->to('/auth/login')->with('success', 'Pendaftaran Desa berhasil! Akun Kepala Desa Anda saat ini dalam status Menunggu Aktivasi (is_active=0). Super Administrator akan memverifikasi berkas SK Kepala Desa Anda sebelum akun diaktifkan.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan desa. Silakan periksa kembali formulir Anda.');
    }
}
