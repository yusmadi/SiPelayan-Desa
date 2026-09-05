<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
    protected $table            = 'pekerjaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama' => 'required|min_length[2]|max_length[100]|is_unique[pekerjaan.nama,id,{id}]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama pekerjaan wajib diisi.',
            'min_length' => 'Nama pekerjaan minimal 2 karakter.',
            'max_length' => 'Nama pekerjaan maksimal 100 karakter.',
            'is_unique'  => 'Nama pekerjaan ini sudah ada dalam database.',
        ],
    ];
}
