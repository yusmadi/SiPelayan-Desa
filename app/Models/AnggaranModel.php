<?php

namespace App\Models;

class AnggaranModel extends BaseTenantModel
{
    protected $table            = 'informasi_desa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'village_id',
        'author_id',
        'kategori',
        'nomor_pengumuman',
        'periode_anggaran',
        'sifat',
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'thumbnail_path',
        'lampiran_path',
        'lampiran_nama',
        'tahun_anggaran',
        'total_apbdes',
        'total_pendapatan',
        'realisasi_pendapatan',
        'total_belanja',
        'realisasi_belanja',
        'total_pembiayaan',
        'rincian_anggaran',
        'is_published',
        'published_at',
        'views',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public const PERIODE_LIST = [
        'Murni'         => 'APBDes/APBG Murni (Awal Tahun)',
        'Tahap I'       => 'Tahap I',
        'Tahap II'      => 'Tahap II',
        'Tahap III'     => 'Tahap III',
        'Semester I'    => 'Laporan Semester I',
        'Semester II'   => 'Laporan Semester II',
        'Laporan Akhir' => 'Laporan Akhir Tahun Anggaran',
    ];

    public const BIDANG_LIST = [
        'bidang_1' => [
            'key'   => 'bidang_1',
            'nama'  => 'Penyelenggaraan Pemerintahan Desa',
            'icon'  => 'bi-bank',
            'color' => 'primary',
        ],
        'bidang_2' => [
            'key'   => 'bidang_2',
            'nama'  => 'Pelaksanaan Pembangunan Desa',
            'icon'  => 'bi-cone-striped',
            'color' => 'success',
        ],
        'bidang_3' => [
            'key'   => 'bidang_3',
            'nama'  => 'Pembinaan Kemasyarakatan Desa',
            'icon'  => 'bi-people',
            'color' => 'info',
        ],
        'bidang_4' => [
            'key'   => 'bidang_4',
            'nama'  => 'Pemberdayaan Masyarakat Desa',
            'icon'  => 'bi-lightbulb',
            'color' => 'warning',
        ],
        'bidang_5' => [
            'key'   => 'bidang_5',
            'nama'  => 'Penanggulangan Bencana & Mendesak',
            'icon'  => 'bi-shield-exclamation',
            'color' => 'danger',
        ],
    ];

    /**
     * Ambil laporan transparansi terbaru yang dipublikasikan
     */
    public function getLatestPublished()
    {
        $row = $this->whereIn('kategori', ['APBDes', 'Transparansi'])
            ->where('is_published', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('tahun_anggaran', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if ($row && !empty($row['rincian_anggaran']) && is_string($row['rincian_anggaran'])) {
            $row['rincian'] = json_decode($row['rincian_anggaran'], true) ?: [];
        } else {
            $row['rincian'] = is_array($row['rincian_anggaran'] ?? null) ? $row['rincian_anggaran'] : [];
        }

        return $row;
    }

    /**
     * Dapatkan daftar transparansi untuk operator
     */
    public function getListForOperator(?string $search = null, ?int $tahun = null, ?string $status = null)
    {
        $builder = $this->db->table($this->table)
            ->select('informasi_desa.*, users.nama_lengkap as author_nama')
            ->join('users', 'users.id = informasi_desa.author_id', 'left')
            ->whereIn('informasi_desa.kategori', ['APBDes', 'Transparansi'])
            ->where('informasi_desa.deleted_at IS NULL');

        $villageId = $this->getCurrentVillageId();
        if ($villageId !== null && !$this->isSuperAdmin()) {
            $builder->where('informasi_desa.village_id', $villageId);
        }

        if (!empty($search)) {
            $search = trim($search);
            $builder->groupStart()
                ->like('informasi_desa.judul', $search)
                ->orLike('informasi_desa.nomor_pengumuman', $search)
                ->orLike('informasi_desa.periode_anggaran', $search)
                ->groupEnd();
        }

        if (!empty($tahun)) {
            $builder->where('informasi_desa.tahun_anggaran', $tahun);
        }

        if ($status !== null && $status !== '') {
            $builder->where('informasi_desa.is_published', (int) $status);
        }

        $results = $builder->orderBy('informasi_desa.tahun_anggaran', 'DESC')
            ->orderBy('informasi_desa.id', 'DESC')
            ->get()->getResultArray();

        foreach ($results as &$r) {
            if (!empty($r['rincian_anggaran']) && is_string($r['rincian_anggaran'])) {
                $r['rincian'] = json_decode($r['rincian_anggaran'], true) ?: [];
            } else {
                $r['rincian'] = is_array($r['rincian_anggaran'] ?? null) ? $r['rincian_anggaran'] : [];
            }
        }

        return $results;
    }

    /**
     * Ambil 1 data transparansi lengkap dengan author dan decode rincian
     */
    public function getDetailWithAuthor(int $id)
    {
        $builder = $this->db->table($this->table)
            ->select('informasi_desa.*, users.nama_lengkap as author_nama')
            ->join('users', 'users.id = informasi_desa.author_id', 'left')
            ->where('informasi_desa.id', $id)
            ->whereIn('informasi_desa.kategori', ['APBDes', 'Transparansi'])
            ->where('informasi_desa.deleted_at IS NULL');

        $villageId = $this->getCurrentVillageId();
        if ($villageId !== null && !$this->isSuperAdmin()) {
            $builder->where('informasi_desa.village_id', $villageId);
        }

        $row = $builder->get()->getRowArray();
        if ($row) {
            if (!empty($row['rincian_anggaran']) && is_string($row['rincian_anggaran'])) {
                $row['rincian'] = json_decode($row['rincian_anggaran'], true) ?: [];
            } else {
                $row['rincian'] = is_array($row['rincian_anggaran'] ?? null) ? $row['rincian_anggaran'] : [];
            }
        }

        return $row;
    }

    /**
     * Increment views
     */
    public function incrementViews(int $id): void
    {
        $this->db->table($this->table)
            ->where('id', $id)
            ->increment('views', 1);
    }
}
