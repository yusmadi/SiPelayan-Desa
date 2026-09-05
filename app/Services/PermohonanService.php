<?php

namespace App\Services;

use App\Models\PermohonanSuratModel;

class PermohonanService
{
    protected PermohonanSuratModel $permohonanModel;

    public function __construct()
    {
        $this->permohonanModel = new PermohonanSuratModel();
    }

    /**
     * Generate nomor permohonan unik format: SPD-[KODE_DESA]-[YYYY]-[NNNNN]
     */
    public function generateNoPermohonan(int $villageId): string
    {
        $tahun = date('Y');
        $kodeDesa = sprintf('DS%02d', $villageId);
        $prefix = "SPD-{$kodeDesa}-{$tahun}-";

        $db = \Config\Database::connect();
        $lastRecord = $db->table('permohonan_surat')
            ->select('no_permohonan')
            ->like('no_permohonan', $prefix, 'after')
            ->orderBy('no_permohonan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($lastRecord && !empty($lastRecord['no_permohonan'])) {
            $parts = explode('-', $lastRecord['no_permohonan']);
            $lastSeq = (int) end($parts);
            $nextSeq = $lastSeq + 1;
        }

        $urutan = str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        $candidate = "{$prefix}{$urutan}";

        // Verifikasi tidak ada bentrok nomor
        while ($db->table('permohonan_surat')->where('no_permohonan', $candidate)->countAllResults() > 0) {
            $nextSeq++;
            $urutan = str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
            $candidate = "{$prefix}{$urutan}";
        }

        return $candidate;
    }

    /**
     * Membuat permohonan baru
     */
    public function buatPermohonan(array $data): array
    {
        $villageId = (int) ($data['village_id'] ?? 1);
        $data['no_permohonan'] = $this->generateNoPermohonan($villageId);
        $data['status'] = $data['status'] ?? 'submitted';

        // Encoding array data form
        if (is_array($data['data_form'])) {
            $data['data_form'] = json_encode($data['data_form']);
        }
        
        if (isset($data['dokumen_syarat']) && is_array($data['dokumen_syarat'])) {
            $data['dokumen_syarat'] = json_encode($data['dokumen_syarat']);
        }

        $id = $this->permohonanModel->insert($data);

        if (! $id) {
            return ['success' => false, 'message' => 'Gagal menyimpan permohonan.'];
        }

        return [
            'success' => true,
            'id' => $id,
            'no_permohonan' => $data['no_permohonan']
        ];
    }

    /**
     * Simulasi mendapatkan status timeline
     */
    public function getStatusTimeline(int $permohonanId): array
    {
        return [
            ['status' => 'submitted', 'time' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
            ['status' => 'verified_operator', 'time' => null],
            ['status' => 'approved_admin', 'time' => null],
            ['status' => 'completed', 'time' => null]
        ];
    }
}
