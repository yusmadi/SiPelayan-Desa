<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BaseTenantModel
 * 
 * WAJIB di-extend oleh semua model yang menyimpan data per-desa.
 * Secara otomatis menambahkan filter village_id pada setiap query SELECT.
 * 
 * KEAMANAN: Mencegah data leak antar tenant.
 */
abstract class BaseTenantModel extends Model
{
    /** Set true hanya pada Super Admin context atau saat auth */
    protected bool $bypassTenant = false;

    /** Nama kolom tenant discriminator */
    protected string $tenantColumn = 'village_id';

    /** Model Event Callbacks */
    protected $beforeFind   = ['applyTenantScope'];
    protected $beforeInsert = ['applyTenantInsert'];
    protected $beforeUpdate = ['applyTenantScope'];
    protected $beforeDelete = ['applyTenantScope'];

    /**
     * Inject WHERE village_id = ? secara otomatis sebelum query berjalan
     */
    protected function applyTenantScope(array $data): array
    {
        if ($this->bypassTenant) {
            return $data;
        }

        $villageId = $this->getCurrentVillageId();

        if ($villageId === null) {
            // Paksa return empty jika bukan Super Admin
            if (! $this->isSuperAdmin()) {
                $this->where("{$this->table}.{$this->tenantColumn}", 0);
            }
            return $data;
        }

        $this->where("{$this->table}.{$this->tenantColumn}", $villageId);
        return $data;
    }

    /**
     * Otomatis set village_id saat insert jika belum ada
     */
    protected function applyTenantInsert(array $data): array
    {
        if ($this->bypassTenant) {
            return $data;
        }

        $villageId = $this->getCurrentVillageId();
        if ($villageId !== null && ! isset($data['data'][$this->tenantColumn])) {
            $data['data'][$this->tenantColumn] = $villageId;
        }

        return $data;
    }

    protected function getCurrentVillageId(): ?int
    {
        if (function_exists('session') && session()->has('village_id')) {
            return (int) session('village_id');
        }
        return null;
    }

    protected function isSuperAdmin(): bool
    {
        return function_exists('session') && session()->has('role_slug') && session('role_slug') === 'super_admin';
    }

    /**
     * Digunakan oleh Super Admin atau proses Auth untuk query lintas desa
     */
    public function withoutTenantScope(): static
    {
        $clone = clone $this;
        $clone->bypassTenant = true;
        return $clone;
    }

    /**
     * Query khusus desa tertentu (untuk Super Admin / rekap kabupaten)
     */
    public function forVillage(int $villageId): static
    {
        $clone = clone $this;
        $clone->bypassTenant = true;
        $clone->where("{$this->table}.{$this->tenantColumn}", $villageId);
        return $clone;
    }
}
