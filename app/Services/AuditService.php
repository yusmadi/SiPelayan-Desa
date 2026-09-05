<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;

class AuditService
{
    /**
     * Mencatat aktivitas ke tabel audit_logs
     * 
     * @param string $action      Aksi yang dilakukan (misal: SURAT_APPROVE)
     * @param string $module      Modul terkait (misal: permohonan_surat)
     * @param int|null $targetId  ID dari record yang diubah
     * @param string|null $targetType Nama tabel record yang diubah
     * @param array|null $newData Data tambahan (di-encode ke JSON)
     * @param array|null $oldData Data sebelum diubah (jika update)
     */
    public function log(string $action, string $module, ?int $targetId = null, ?string $targetType = null, ?array $newData = null, ?array $oldData = null)
    {
        if (ENVIRONMENT === 'testing') {
            return;
        }

        $db = \Config\Database::connect();
        
        $data = [
            'village_id'  => session('village_id') ?? null,
            'user_id'     => session('user_id') ?? null,
            'role_slug'   => session('role_slug') ?? null,
            'action'      => $action,
            'module'      => $module,
            'target_id'   => $targetId,
            'target_type' => $targetType,
            'ip_address'  => service('request')->getIPAddress(),
            'user_agent'  => mb_substr((string)service('request')->getUserAgent(), 0, 500),
            'created_at'  => date('Y-m-d H:i:s')
        ];

        if ($newData !== null) {
            $data['new_data'] = json_encode($newData);
        }
        
        if ($oldData !== null) {
            $data['old_data'] = json_encode($oldData);
        }

        $db->table('audit_logs')->insert($data);
    }
}
