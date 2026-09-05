<?php

namespace App\Models;

class PengaduanModel extends BaseTenantModel
{
    protected $table            = 'pengaduan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'village_id',
        'pelapor_id',
        'no_tiket',
        'kategori',
        'judul',
        'isi_laporan',
        'lokasi',
        'latitude',
        'longitude',
        'foto_paths',
        'status',
        'is_anonymous',
        'tanggapan',
        'ditangani_oleh',
        'resolved_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const KATEGORI_LIST = [
        'Infrastruktur'  => 'Infrastruktur (Jalan, Jembatan, Drainase, dll)',
        'Layanan Publik' => 'Layanan Publik & Administrasi',
        'Keamanan'       => 'Ketertiban & Keamanan Lingkungan',
        'Lingkungan'     => 'Kebersihan & Sampah / Lingkungan Hidup',
        'Lainnya'        => 'Lainnya',
    ];

    public const STATUS_LABELS = [
        'open'        => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved'    => 'Selesai',
        'closed'      => 'Ditutup',
        'rejected'    => 'Ditolak',
    ];

    public const STATUS_COLORS = [
        'open'        => 'warning',
        'in_progress' => 'primary',
        'resolved'    => 'success',
        'closed'      => 'secondary',
        'rejected'    => 'danger',
    ];
}
