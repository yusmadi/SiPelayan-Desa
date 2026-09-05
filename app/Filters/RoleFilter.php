<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RoleFilter — Kontrol akses berbasis Role (RBAC)
 *
 * Penggunaan di Routes.php:
 *   $routes->group('super-admin', ['filter' => 'role:super_admin'], ...);
 *   $routes->group('admin-desa', ['filter' => 'role:admin_desa,super_admin'], ...);
 *
 * Argumen berupa role slug yang diizinkan (pisah koma untuk multi-role).
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRoleSlug = session('role_slug');

        if (empty($arguments)) {
            return null; // Tidak ada pembatasan role
        }

        // CI4 dapat mengirim arguments sebagai ['arg1', 'arg2'] atau ['arg1,arg2']
        $allowedRoles = [];
        foreach ($arguments as $arg) {
            foreach (explode(',', (string) $arg) as $item) {
                $trimmed = trim($item);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($userRoleSlug, $allowedRoles, true)) {
            // Log akses ilegal
            log_message('warning', sprintf(
                '[RBAC] Akses ditolak. User ID: %s, Role: %s, URL: %s',
                session('user_id'),
                $userRoleSlug,
                current_url()
            ));

            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki izin.']);
            }

            return redirect()->to('/')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
