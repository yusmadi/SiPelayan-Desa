<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinsiModel extends Model
{
    protected $table            = 'provinsi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'kode_kemendagri',
        'nama',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil data provinsi dengan hitungan total kabupaten
     */
    public function getProvinsiWithCount(?int $id = null, ?string $search = null)
    {
        $builder = $this->db->table($this->table)
            ->select('provinsi.*, (SELECT COUNT(*) FROM kabupaten WHERE kabupaten.provinsi_id = provinsi.id) as total_kabupaten', false);

        if ($id !== null) {
            return $builder->where('provinsi.id', $id)->get()->getRowArray();
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('provinsi.nama', $search)
                ->orLike('provinsi.kode_kemendagri', $search)
                ->groupEnd();
        }

        return $builder->orderBy('provinsi.nama', 'ASC')->get()->getResultArray();
    }
}
