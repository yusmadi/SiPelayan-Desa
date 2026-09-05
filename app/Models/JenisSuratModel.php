<?php

namespace App\Models;

use CodeIgniter\Model;

// Tabel jenis_surat bukan tenant-specific, jadi pakai Model biasa
class JenisSuratModel extends Model
{
    protected $table            = 'jenis_surat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'kode', 'nama', 'deskripsi', 'template_path',
        'schema_form', 'syarat_dokumen', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
