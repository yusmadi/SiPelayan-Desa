<?php

namespace App\Models;

class PermohonanSuratModel extends BaseTenantModel
{
    protected $table            = 'permohonan_surat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'village_id', 'jenis_surat_id', 'pemohon_id', 'no_permohonan',
        'data_form', 'dokumen_syarat', 'status', 'catatan_operator',
        'catatan_admin', 'catatan_penolakan', 'operator_id', 'admin_id',
        'verified_at', 'approved_at', 'rejected_at', 'completed_at',
        'surat_output_path', 'no_surat_keluar', 'ttd_digital_hash',
        'priority', 'channel'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public const STATUS_LABELS = [
        'draft'             => 'Draft',
        'submitted'         => 'Diajukan',
        'verified_operator' => 'Diverifikasi Operator',
        'approved_admin'    => 'Disetujui Admin',
        'ttd_kades'         => 'TTD Kades',
        'rejected'          => 'Ditolak',
        'completed'         => 'Selesai',
        'cancelled'         => 'Dibatalkan',
    ];

    public const STATUS_COLORS = [
        'draft'             => 'secondary',
        'submitted'         => 'info',
        'verified_operator' => 'primary',
        'approved_admin'    => 'success',
        'ttd_kades'         => 'info',
        'rejected'          => 'danger',
        'completed'         => 'success',
        'cancelled'         => 'warning',
    ];
}
