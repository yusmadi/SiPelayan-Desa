<?php

namespace App\Models;

use CodeIgniter\Model;

class DesaModel extends Model
{
    protected $table            = 'desa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'kabupaten_id',
        'kode_kemendagri',
        'nama_desa',
        'nama_kecamatan',
        'nama_kepala_desa',
        'nip_kepala_desa',
        'alamat_kantor',
        'telepon',
        'email',
        'whatsapp_kades',
        'website',
        'logo_path',
        'kode_pos',
        'latitude',
        'longitude',
        'visi',
        'misi',
        'is_active',
        'tenant_slug',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Ambil data desa lengkap dengan nama kabupaten
     */
    public function getDesaWithKabupaten(?int $id = null, ?string $search = null, ?int $kabupatenId = null)
    {
        $columns = 'desa.*, kabupaten.nama as nama_kabupaten, kabupaten.provinsi';
        $builder = $this->select($columns)
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left');

        if ($id !== null) {
            return $builder->where('desa.id', $id)->first();
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('desa.nama_desa', $search)
                ->orLike('desa.nama_kecamatan', $search)
                ->orLike('desa.kode_kemendagri', $search)
                ->orLike('desa.tenant_slug', $search)
                ->orLike('kabupaten.nama', $search)
                ->groupEnd();
        }

        if (!empty($kabupatenId)) {
            $builder->where('desa.kabupaten_id', $kabupatenId);
        }

        return $builder->orderBy('desa.nama_desa', 'ASC')->findAll();
    }

    /**
     * Ambil data desa berpaginasi untuk efisiensi data puluhan ribu (hanya kolom yang ditampilkan)
     */
    public function getDesaPaginated(?string $search = null, ?int $kabupatenId = null, int $perPage = 25, string $group = 'default', ?int $page = null)
    {
        $pager = service('pager');
        $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);

        $db = $this->db;

        // 1. Hitung total secara optimal (cache default count untuk respon instan < 10ms)
        if (empty($search) && empty($kabupatenId)) {
            $total = cache()->remember('master_desa_total_count', 600, function() use ($db) {
                return $db->table('desa')->countAll();
            });
        } elseif (empty($search) && !empty($kabupatenId)) {
            $total = $db->table('desa')->where('kabupaten_id', $kabupatenId)->countAllResults();
        } else {
            $countBuilder = $db->table('desa')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->groupStart()
                ->like('desa.nama_desa', $search)
                ->orLike('desa.nama_kecamatan', $search)
                ->orLike('desa.kode_kemendagri', $search)
                ->orLike('desa.tenant_slug', $search)
                ->orLike('kabupaten.nama', $search)
                ->groupEnd();

            if (!empty($kabupatenId)) {
                $countBuilder->where('desa.kabupaten_id', $kabupatenId);
            }

            $total = $countBuilder->countAllResults();
        }

        $this->pager = $pager->store($group, $page, $perPage, $total);
        $offset      = ($page - 1) * $perPage;

        // 2. Query Data dengan Index & Proyeksi Ringan
        $columns = 'desa.id, desa.nama_desa, desa.nama_kecamatan, desa.kode_kemendagri, desa.nama_kepala_desa, desa.whatsapp_kades, desa.telepon, desa.is_active, desa.tenant_slug, desa.kabupaten_id, kabupaten.nama as nama_kabupaten, kabupaten.provinsi';

        $builder = $this->select($columns)
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('desa.nama_desa', $search)
                ->orLike('desa.nama_kecamatan', $search)
                ->orLike('desa.kode_kemendagri', $search)
                ->orLike('desa.tenant_slug', $search)
                ->orLike('kabupaten.nama', $search)
                ->groupEnd();
        }

        if (!empty($kabupatenId)) {
            $builder->where('desa.kabupaten_id', $kabupatenId);
        }

        return $builder->orderBy('desa.nama_desa', 'ASC')->limit($perPage, $offset)->get()->getResultArray();
    }
}
