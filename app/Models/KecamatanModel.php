<?php

namespace App\Models;

use CodeIgniter\Model;

class KecamatanModel extends Model
{
    protected $table            = 'kecamatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'kabupaten_id',
        'kode_kemendagri',
        'nama',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil data kecamatan dengan join ke kabupaten & provinsi
     */
    public function getKecamatanWithKabupaten(?int $id = null, ?string $search = null, ?int $kabupatenId = null)
    {
        $builder = $this->db->table($this->table)
            ->select('kecamatan.*, kabupaten.nama as nama_kabupaten, COALESCE(provinsi.nama, kabupaten.provinsi) as nama_provinsi, (SELECT COUNT(*) FROM desa WHERE desa.nama_kecamatan = kecamatan.nama) as total_desa', false)
            ->join('kabupaten', 'kabupaten.id = kecamatan.kabupaten_id', 'left')
            ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left');

        if ($id !== null) {
            return $builder->where('kecamatan.id', $id)->get()->getRowArray();
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kecamatan.nama', $search)
                ->orLike('kecamatan.kode_kemendagri', $search)
                ->orLike('kabupaten.nama', $search)
                ->groupEnd();
        }

        if (!empty($kabupatenId)) {
            $builder->where('kecamatan.kabupaten_id', $kabupatenId);
        }

        return $builder->orderBy('kecamatan.nama', 'ASC')->get()->getResultArray();
    }

    /**
     * Ambil data kecamatan berpaginasi untuk performa cepat (25 per halaman)
     */
    public function getKecamatanPaginated(?string $search = null, ?int $kabupatenId = null, int $perPage = 25, string $group = 'default', ?int $page = null)
    {
        $pager = service('pager');
        $page  = $page >= 1 ? $page : $pager->getCurrentPage($group);
        $db    = $this->db;

        // 1. Hitung total records secara efisien
        if (empty($search) && empty($kabupatenId)) {
            $total = cache()->remember('master_kecamatan_total_count', 600, function() use ($db) {
                return $db->table('kecamatan')->countAll();
            });
        } elseif (empty($search) && !empty($kabupatenId)) {
            $total = $db->table('kecamatan')->where('kabupaten_id', $kabupatenId)->countAllResults();
        } else {
            $countBuilder = $db->table('kecamatan')
                ->join('kabupaten', 'kabupaten.id = kecamatan.kabupaten_id', 'left');

            if (!empty($search)) {
                $countBuilder->groupStart()
                    ->like('kecamatan.nama', $search)
                    ->orLike('kecamatan.kode_kemendagri', $search)
                    ->orLike('kabupaten.nama', $search)
                    ->groupEnd();
            }

            if (!empty($kabupatenId)) {
                $countBuilder->where('kecamatan.kabupaten_id', $kabupatenId);
            }

            $total = $countBuilder->countAllResults();
        }

        $this->pager = $pager->store($group, $page, $perPage, $total);
        $offset      = ($page - 1) * $perPage;

        // 2. Ambil 25 data kecamatan aktif
        $builder = $db->table('kecamatan')
            ->select('kecamatan.id, kecamatan.kabupaten_id, kecamatan.kode_kemendagri, kecamatan.nama, kecamatan.is_active, kabupaten.nama as nama_kabupaten, COALESCE(provinsi.nama, kabupaten.provinsi) as nama_provinsi, (SELECT COUNT(*) FROM desa WHERE desa.nama_kecamatan = kecamatan.nama) as total_desa', false)
            ->join('kabupaten', 'kabupaten.id = kecamatan.kabupaten_id', 'left')
            ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kecamatan.nama', $search)
                ->orLike('kecamatan.kode_kemendagri', $search)
                ->orLike('kabupaten.nama', $search)
                ->groupEnd();
        }

        if (!empty($kabupatenId)) {
            $builder->where('kecamatan.kabupaten_id', $kabupatenId);
        }

        return $builder->orderBy('kecamatan.nama', 'ASC')->limit($perPage, $offset)->get()->getResultArray();
    }
}
