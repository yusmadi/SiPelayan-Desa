<?php

namespace App\Models;

class PengumumanModel extends BaseTenantModel
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

    public const SIFAT_LIST = [
        'Biasa'   => 'Biasa (Informasi Rutin)',
        'Penting' => 'Penting (Perlu Perhatian Warga)',
        'Segera'  => 'Segera (Mendesak / Waktu Terbatas)',
    ];

    public const SIFAT_BADGES = [
        'Biasa'   => 'primary',
        'Penting' => 'warning',
        'Segera'  => 'danger',
    ];

    public const SIFAT_ICONS = [
        'Biasa'   => 'bi-info-circle',
        'Penting' => 'bi-exclamation-circle',
        'Segera'  => 'bi-lightning-charge',
    ];

    /**
     * Dapatkan pengumuman terbaru yang dipublikasikan
     */
    public function getLatestPublished(int $limit = 1)
    {
        $query = $this->where('kategori', 'Pengumuman')
            ->where('is_published', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC');

        if ($limit === 1) {
            return $query->first();
        }

        return $query->findAll($limit);
    }

    /**
     * Dapatkan daftar pengumuman untuk operator dengan data author
     */
    public function getListForOperator(?string $search = null, ?string $sifat = null, ?string $status = null)
    {
        $builder = $this->db->table($this->table)
            ->select('informasi_desa.*, users.nama_lengkap as author_nama, roles.name as author_role')
            ->join('users', 'users.id = informasi_desa.author_id', 'left')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('informasi_desa.kategori', 'Pengumuman')
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
                ->orLike('informasi_desa.konten', $search)
                ->groupEnd();
        }

        if (!empty($sifat) && array_key_exists($sifat, self::SIFAT_LIST)) {
            $builder->where('informasi_desa.sifat', $sifat);
        }

        if ($status !== null && $status !== '') {
            $builder->where('informasi_desa.is_published', (int) $status);
        }

        return $builder->orderBy('informasi_desa.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Ambil 1 data pengumuman lengkap dengan author
     */
    public function getDetailWithAuthor(int $id)
    {
        $builder = $this->db->table($this->table)
            ->select('informasi_desa.*, users.nama_lengkap as author_nama, roles.name as author_role')
            ->join('users', 'users.id = informasi_desa.author_id', 'left')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('informasi_desa.id', $id)
            ->where('informasi_desa.kategori', 'Pengumuman')
            ->where('informasi_desa.deleted_at IS NULL');

        $villageId = $this->getCurrentVillageId();
        if ($villageId !== null && !$this->isSuperAdmin()) {
            $builder->where('informasi_desa.village_id', $villageId);
        }

        return $builder->get()->getRowArray();
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
