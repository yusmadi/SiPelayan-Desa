<?php

namespace App\Models;

class UserModel extends BaseTenantModel
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'village_id', 'role_id', 'nik', 'nama_lengkap', 'email', 'no_hp',
        'password_hash', 'avatar_path', 'sk_kades_path', 'is_verified', 'is_nik_verified',
        'is_active', 'is_banned', 'ban_reason', 'email_verify_token',
        'password_reset_token', 'password_reset_expires', 'last_login_at',
        'last_login_ip', 'failed_login_count', 'locked_until'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Cari user berdasarkan email untuk login (bypass tenant scope saat auth)
     */
    public function findByEmailWithRole(string $email): ?array
    {
        return $this->withoutTenantScope()
            ->select('users.*, roles.name as role_name, roles.slug as role_slug, roles.level as role_level, desa.nama_desa, desa.tenant_slug')
            ->join('roles', 'roles.id = users.role_id')
            ->join('desa', 'desa.id = users.village_id', 'left')
            ->where('users.email', trim($email))
            ->first();
    }

    /**
     * Cari user berdasarkan Email atau Nomor HP/WhatsApp (bypass tenant scope saat auth)
     */
    public function findByIdentifierWithRole(string $identifier): ?array
    {
        $identifier = trim($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->findByEmailWithRole($identifier);
        }

        // Variasi format nomor WhatsApp / HP
        $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);
        $phoneVariants = [$identifier];

        if (! empty($cleanPhone)) {
            $phoneVariants[] = $cleanPhone;
            if (str_starts_with($cleanPhone, '62')) {
                $phoneVariants[] = '0' . substr($cleanPhone, 2);
                $phoneVariants[] = '+62' . substr($cleanPhone, 2);
            } elseif (str_starts_with($cleanPhone, '0')) {
                $phoneVariants[] = '62' . substr($cleanPhone, 1);
                $phoneVariants[] = '+62' . substr($cleanPhone, 1);
            }
        }
        $phoneVariants = array_values(array_unique(array_filter($phoneVariants)));

        return $this->withoutTenantScope()
            ->select('users.*, roles.name as role_name, roles.slug as role_slug, roles.level as role_level, desa.nama_desa, desa.tenant_slug')
            ->join('roles', 'roles.id = users.role_id')
            ->join('desa', 'desa.id = users.village_id', 'left')
            ->groupStart()
                ->where('users.email', $identifier)
                ->orWhereIn('users.no_hp', $phoneVariants)
            ->groupEnd()
            ->first();
    }

    /**
     * Catat login sukses dan reset failed login counter
     */
    public function recordSuccessfulLogin(int $userId, ?string $ip = null): void
    {
        $this->withoutTenantScope()->update($userId, [
            'last_login_at'      => date('Y-m-d H:i:s'),
            'last_login_ip'      => $ip,
            'failed_login_count' => 0,
            'locked_until'       => null,
        ]);
    }

    /**
     * Catat login gagal dan proteksi brute-force (kunci akun jika gagal >= 5 kali)
     */
    public function recordFailedLogin(int $userId, int $currentFailCount): void
    {
        $newCount = $currentFailCount + 1;
        $data = [
            'failed_login_count' => $newCount,
        ];

        // Jika 5 kali berturut-turut gagal, kunci akun selama 15 menit
        if ($newCount >= 5) {
            $data['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $this->withoutTenantScope()->update($userId, $data);
    }
}
