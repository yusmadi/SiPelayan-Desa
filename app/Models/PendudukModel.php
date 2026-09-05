<?php

namespace App\Models;

class PendudukModel extends BaseTenantModel
{
    protected $table            = 'penduduk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'village_id',
        'user_id',
        'nik',
        'no_kk',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan',
        'kewarganegaraan',
        'status_hubungan_kk',
        'rt',
        'rw',
        'dusun',
        'alamat_lengkap',
        'status_penduduk',
        'foto_ktp_path',
        'foto_kk_path',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Hitung statistik kependudukan untuk desa aktif
     */
    public function getStatistik(): array
    {
        $villageId = $this->getCurrentVillageId();
        $db = \Config\Database::connect();

        $builder = $db->table($this->table)->where('deleted_at IS NULL');
        if ($villageId !== null) {
            $builder->where('village_id', $villageId);
        }

        $total = (clone $builder)->countAllResults();
        $laki  = (clone $builder)->where('jenis_kelamin', 'L')->countAllResults();
        $perempuan = (clone $builder)->where('jenis_kelamin', 'P')->countAllResults();
        $kkCount = (clone $builder)->where('status_hubungan_kk', 'Kepala Keluarga')->countAllResults();

        return [
            'total'     => $total,
            'laki'      => $laki,
            'perempuan' => $perempuan,
            'kk'        => $kkCount,
        ];
    }
}
