<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * AuthFilter — Memastikan pengguna sudah login
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->has('user_id') || ! session()->has('role_slug')) {
            session()->set('redirect_after_login', current_url());
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek akun aktif & tidak diblokir (Ini akan efektif jika kita mengaktifkan UserModel)
        // Sementara itu di-skip jika UserModel belum diinisialisasi atau dalam testing
        if (class_exists('\App\Models\UserModel') && ENVIRONMENT !== 'testing') {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->withoutTenantScope()->find(session('user_id'));

            if (! $user || ! $user['is_active'] || $user['is_banned']) {
                session()->destroy();
                return redirect()->to('/auth/login')->with('error', 'Akun Anda tidak aktif atau telah diblokir.');
            }

            // Cek lock akun (brute-force protection)
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $lockTime = date('H:i', strtotime($user['locked_until']));
                session()->destroy();
                return redirect()->to('/auth/login')->with('error', "Akun terkunci hingga pukul {$lockTime} karena terlalu banyak percobaan login.");
            }
        }

        return null; // Lanjutkan request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
