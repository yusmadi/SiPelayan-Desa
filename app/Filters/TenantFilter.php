<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * TenantFilter — Validasi isolasi tenant (desa)
 *
 * Memastikan pengguna hanya bisa mengakses resource milik desanya sendiri.
 * Super Admin bypass filter ini.
 */
class TenantFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Super Admin boleh akses lintas tenant
        if (session('role_slug') === 'super_admin') {
            return null;
        }

        $sessionVillageId = session('village_id');

        if (empty($sessionVillageId)) {
            session()->destroy();
            return redirect()->to('/auth/login')->with('error', 'Sesi tidak valid. Silakan login ulang.');
        }

        // Validasi village_id dari input atau parameter jika ada
        $urlVillageId = $request->getVar('village_id');

        if (!empty($urlVillageId) && is_numeric($urlVillageId) && (int)$urlVillageId !== (int)$sessionVillageId) {
            log_message('error', sprintf(
                '[TenantFilter] Percobaan akses lintas tenant! UserID: %s, VillageID Session: %s, URL VillageID: %s, IP: %s',
                session('user_id'), $sessionVillageId, $urlVillageId, $request->getIPAddress()
            ));

            // Catat sebagai anomali keamanan
            if (class_exists('\App\Services\AuditService')) {
                $auditService = new \App\Services\AuditService();
                $auditService->log('CROSS_TENANT_ATTEMPT', 'security', null, null, [
                    'attempted_village_id' => $urlVillageId
                ]);
            }

            return redirect()->to('/')->with('error', 'Akses tidak sah terdeteksi dan telah dicatat.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
