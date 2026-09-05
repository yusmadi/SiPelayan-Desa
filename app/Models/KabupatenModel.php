<?php

namespace App\Models;

use CodeIgniter\Model;

class KabupatenModel extends Model
{
    protected $table            = 'kabupaten';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'provinsi_id',
        'kode_kemendagri',
        'nama',
        'provinsi',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil data kabupaten dengan join ke provinsi
     */
    public function getKabupatenWithProvinsi(?int $id = null, ?string $search = null, ?int $provinsiId = null)
    {
        $builder = $this->db->table($this->table)
            ->select('kabupaten.*, COALESCE(provinsi.nama, kabupaten.provinsi) as nama_provinsi, (SELECT COUNT(*) FROM desa WHERE desa.kabupaten_id = kabupaten.id) as total_desa, (SELECT COUNT(*) FROM kecamatan WHERE kecamatan.kabupaten_id = kabupaten.id) as total_kecamatan', false)
            ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left');

        if ($id !== null) {
            return $builder->where('kabupaten.id', $id)->get()->getRowArray();
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kabupaten.nama', $search)
                ->orLike('kabupaten.kode_kemendagri', $search)
                ->orLike('provinsi.nama', $search)
                ->orLike('kabupaten.provinsi', $search)
                ->groupEnd();
        }

        if (!empty($provinsiId)) {
            $builder->where('kabupaten.provinsi_id', $provinsiId);
        }

        return $builder->orderBy('kabupaten.nama', 'ASC')->get()->getResultArray();
    }

    /**
     * Ambil data kabupaten berpaginasi (25 per page) dengan agregasi efisien
     */
    public function getKabupatenPaginated(?string $search = null, ?int $provinsiId = null, int $perPage = 25, string $group = 'default', ?int $page = null)
    {
        $pager = service('pager');
        $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
        $db    = $this->db;

        // 1. Hitung total secara efisien
        if (empty($search) && empty($provinsiId)) {
            $total = cache()->remember('master_kabupaten_total_count', 600, function() use ($db) {
                return $db->table('kabupaten')->countAll();
            });
        } elseif (empty($search) && !empty($provinsiId)) {
            $total = $db->table('kabupaten')->where('provinsi_id', $provinsiId)->countAllResults();
        } else {
            $countBuilder = $db->table('kabupaten')
                ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left');

            if (!empty($search)) {
                $countBuilder->groupStart()
                    ->like('kabupaten.nama', $search)
                    ->orLike('kabupaten.kode_kemendagri', $search)
                    ->orLike('provinsi.nama', $search)
                    ->orLike('kabupaten.provinsi', $search)
                    ->groupEnd();
            }

            if (!empty($provinsiId)) {
                $countBuilder->where('kabupaten.provinsi_id', $provinsiId);
            }

            $total = $countBuilder->countAllResults();
        }

        $this->pager = $pager->store($group, $page, $perPage, $total);
        $offset      = ($page - 1) * $perPage;

        // 2. Query 25 data kabupaten
        $builder = $db->table('kabupaten')
            ->select('kabupaten.id, kabupaten.provinsi_id, kabupaten.kode_kemendagri, kabupaten.nama, kabupaten.provinsi, kabupaten.is_active, COALESCE(provinsi.nama, kabupaten.provinsi) as nama_provinsi, (SELECT COUNT(*) FROM desa WHERE desa.kabupaten_id = kabupaten.id) as total_desa, (SELECT COUNT(*) FROM kecamatan WHERE kecamatan.kabupaten_id = kabupaten.id) as total_kecamatan', false)
            ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kabupaten.nama', $search)
                ->orLike('kabupaten.kode_kemendagri', $search)
                ->orLike('provinsi.nama', $search)
                ->orLike('kabupaten.provinsi', $search)
                ->groupEnd();
        }

        if (!empty($provinsiId)) {
            $builder->where('kabupaten.provinsi_id', $provinsiId);
        }

        return $builder->orderBy('kabupaten.nama', 'ASC')->limit($perPage, $offset)->get()->getResultArray();
    }
}
