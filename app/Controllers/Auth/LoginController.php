<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->has('is_logged_in') && session('is_logged_in')) {
            return $this->redirectByRole(session('role_slug'));
        }

        return view('auth/login', ['title' => 'Login - SiPelayan Desa']);
    }

    public function proses()
    {
        $identity = trim((string) (
            $this->request->getPost('identity')
            ?? $this->request->getPost('email')
            ?? $this->request->getPost('whatsapp')
            ?? $this->request->getPost('no_hp')
        ));
        $password = (string) $this->request->getPost('password');

        if (empty($identity) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Silakan isi email atau nomor WhatsApp dan password.');
        }

        $userModel = new UserModel();
        $user      = $userModel->findByIdentifierWithRole($identity);

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Email / No. WhatsApp atau password salah.');
        }

        // Cek apakah akun sedang terkunci karena brute force
        if (! empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $lockTime = date('H:i', strtotime($user['locked_until']));
            return redirect()->back()->with('error', "Akun Anda sementara terkunci hingga pukul {$lockTime} WIB karena terlalu banyak percobaan gagal.");
        }

        // Cek status aktif & blokir
        if (! $user['is_active']) {
            return redirect()->back()->with('error', 'Akun Anda berstatus non-aktif. Silakan hubungi admin.');
        }

        if ($user['is_banned']) {
            $reason = ! empty($user['ban_reason']) ? " Alasan: {$user['ban_reason']}" : '';
            return redirect()->back()->with('error', "Akun Anda telah ditangguhkan.{$reason}");
        }

        // Verifikasi Password Hash
        if (! password_verify($password, $user['password_hash'])) {
            $userModel->recordFailedLogin((int) $user['id'], (int) $user['failed_login_count']);
            $sisa = 4 - (int) $user['failed_login_count'];
            $warning = $sisa > 0 ? " (Sisa percobaan: {$sisa}x)" : " (Akun Anda sekarang dikunci 15 menit)";
            return redirect()->back()->withInput()->with('error', 'Email / No. WhatsApp atau password salah.' . $warning);
        }

        // Login Sukses
        $userModel->recordSuccessfulLogin((int) $user['id'], $this->request->getIPAddress());

        // Cek apakah desa user berada di Provinsi Aceh
        $userVillageId = $user['village_id'] !== null ? (int) $user['village_id'] : null;
        $isAceh = is_aceh($userVillageId);

        // Set Data Session
        $sessionData = [
            'user_id'      => (int) $user['id'],
            'nama_lengkap' => $user['nama_lengkap'],
            'email'        => $user['email'],
            'nik'          => $user['nik'],
            'role_id'      => (int) $user['role_id'],
            'role_slug'    => $user['role_slug'],
            'role_name'    => $user['role_name'],
            'village_id'   => $userVillageId,
            'nama_desa'    => $user['nama_desa'] ?? 'Pusat (Super Admin)',
            'tenant_slug'  => $user['tenant_slug'] ?? '',
            'is_aceh'      => $isAceh,
            'sebutan_desa' => $isAceh ? 'Gampong' : 'Desa',
            'sebutan_kades'=> $isAceh ? 'Keuchik' : 'Kepala Desa',
            'is_logged_in' => true,
        ];
        session()->set($sessionData);

        // Redirect URL jika sebelumnya tersimpan
        if ($redirectUrl = session()->get('redirect_after_login')) {
            session()->remove('redirect_after_login');
            return redirect()->to($redirectUrl)->with('success', 'Selamat datang kembali, ' . esc($user['nama_lengkap']));
        }

        return $this->redirectByRole($user['role_slug'])->with('success', 'Selamat datang kembali, ' . esc($user['nama_lengkap']));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Anda telah berhasil logout.');
    }

    private function redirectByRole(?string $roleSlug)
    {
        switch ($roleSlug) {
            case 'super_admin':
                return redirect()->to('/super-admin/dashboard');
            case 'admin_desa':
                return redirect()->to('/admin-desa/dashboard');
            case 'operator':
                return redirect()->to('/operator/dashboard');
            case 'warga':
            default:
                return redirect()->to('/warga/dashboard');
        }
    }
}
