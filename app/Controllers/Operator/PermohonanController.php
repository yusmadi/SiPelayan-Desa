<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;

class PermohonanController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $permohonan = $db->table('permohonan_surat')
            ->select('permohonan_surat.*, jenis_surat.nama as nama_surat, users.nama_lengkap as nama_pemohon')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id')
            ->join('users', 'users.id = permohonan_surat.pemohon_id')
            ->where('permohonan_surat.village_id', $villageId)
            ->orderBy('permohonan_surat.id', 'DESC')
            ->get()->getResultArray();

        return view('operator/permohonan/index', [
            'title'      => 'Verifikasi Permohonan Surat - SiPelayan Desa',
            'permohonan' => $permohonan
        ]);
    }

    public function verify(int $id)
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $db->table('permohonan_surat')
            ->where('id', $id)
            ->where('village_id', $villageId)
            ->update([
                'status'      => 'verified_operator',
                'operator_id' => session('user_id'),
                'verified_at' => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

        return redirect()->back()->with('success', 'Permohonan surat berhasil diverifikasi dan diteruskan ke Kades/Sekdes.');
    }

    public function generate(int $id)
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $builder = $db->table('permohonan_surat')
            ->select('
                permohonan_surat.*, 
                jenis_surat.nama as nama_surat, 
                jenis_surat.kode as kode_surat, 
                jenis_surat.deskripsi as deskripsi_surat,
                users.nama_lengkap as nama_pemohon, 
                users.nik as nik_user,
                users.email as email_user,
                users.no_hp as no_hp_user,
                desa.nama_desa,
                desa.nama_kecamatan,
                desa.nama_kepala_desa,
                desa.nip_kepala_desa,
                desa.alamat_kantor,
                desa.telepon as telepon_desa,
                desa.email as email_desa,
                desa.kode_kemendagri as kode_desa,
                desa.logo_path,
                kabupaten.nama as nama_kabupaten,
                kabupaten.provinsi as nama_provinsi,
                penduduk.nik as nik_penduduk,
                penduduk.no_kk,
                penduduk.tempat_lahir,
                penduduk.tanggal_lahir,
                penduduk.jenis_kelamin,
                penduduk.agama,
                penduduk.status_perkawinan,
                penduduk.pekerjaan,
                penduduk.pendidikan,
                penduduk.kewarganegaraan,
                penduduk.alamat_lengkap as alamat_penduduk,
                penduduk.rt,
                penduduk.rw,
                penduduk.dusun
            ')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id', 'left')
            ->join('users', 'users.id = permohonan_surat.pemohon_id', 'left')
            ->join('desa', 'desa.id = permohonan_surat.village_id', 'left')
            ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
            ->join('penduduk', 'penduduk.user_id = permohonan_surat.pemohon_id OR (penduduk.nik IS NOT NULL AND penduduk.nik = users.nik)', 'left')
            ->where('permohonan_surat.id', $id);

        if ($villageId) {
            $builder->where('permohonan_surat.village_id', $villageId);
        }

        $permohonan = $builder->get()->getRowArray();

        if (! $permohonan) {
            return redirect()->to('/operator/permohonan')->with('error', 'Data permohonan surat tidak ditemukan.');
        }

        // Fallback data desa jika tidak terisi dari join
        if (empty($permohonan['nama_kepala_desa'])) {
            $desaTargetId = (int) ($permohonan['village_id'] ?: ($villageId ?: 1));
            $desa = $db->table('desa')
                ->select('desa.*, kabupaten.nama as nama_kabupaten, kabupaten.provinsi as nama_provinsi')
                ->join('kabupaten', 'kabupaten.id = desa.kabupaten_id', 'left')
                ->where('desa.id', $desaTargetId)
                ->get()->getRowArray();

            if ($desa) {
                $permohonan['nama_desa']        = $desa['nama_desa'];
                $permohonan['nama_kecamatan']   = $desa['nama_kecamatan'];
                $permohonan['nama_kepala_desa'] = $desa['nama_kepala_desa'];
                $permohonan['nip_kepala_desa']  = $desa['nip_kepala_desa'];
                $permohonan['alamat_kantor']    = $desa['alamat_kantor'];
                $permohonan['telepon_desa']     = $desa['telepon'];
                $permohonan['email_desa']       = $desa['email'];
                $permohonan['logo_path']        = $desa['logo_path'];
                $permohonan['nama_kabupaten']   = $desa['nama_kabupaten'];
                $permohonan['nama_provinsi']    = $desa['nama_provinsi'];
            }
        }

        // Auto-assign Nomor Surat Keluar resmi jika belum ada
        if (empty($permohonan['no_surat_keluar'])) {
            $romawiBulan = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $bln = $romawiBulan[(int)date('n')];
            $thn = date('Y');
            $kodeSurat = !empty($permohonan['kode_surat']) ? strtoupper($permohonan['kode_surat']) : 'DS';
            $seq = sprintf('%03d', $permohonan['id']);
            $noSurat = "470 / {$seq} / {$kodeSurat} / {$bln} / {$thn}";

            $db->table('permohonan_surat')
                ->where('id', $id)
                ->update([
                    'no_surat_keluar' => $noSurat,
                    'updated_at'      => date('Y-m-d H:i:s')
                ]);
            $permohonan['no_surat_keluar'] = $noSurat;
        }

        // Parse data isian form
        $formData = [];
        if (!empty($permohonan['data_form'])) {
            $decoded = json_decode($permohonan['data_form'], true);
            if (is_array($decoded)) {
                $formData = $decoded;
            }
        }

        return view('operator/permohonan/cetak_surat', [
            'title'      => 'Generate ' . ($permohonan['nama_surat'] ?? 'Surat') . ' - ' . ($permohonan['nama_pemohon'] ?? ''),
            'permohonan' => $permohonan,
            'formData'   => $formData
        ]);
    }

    public function toggleSigned(int $id)
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $builder = $db->table('permohonan_surat')->where('id', $id);
        if ($villageId) {
            $builder->where('village_id', $villageId);
        }
        $permohonan = $builder->get()->getRowArray();

        if (! $permohonan) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data permohonan tidak ditemukan.']);
            }
            return redirect()->back()->with('error', 'Data permohonan tidak ditemukan.');
        }

        $isCurrentlySigned = !empty($permohonan['ttd_digital_hash']);
        $newHash = $isCurrentlySigned ? null : 'SIGNED_' . $id . '_' . date('YmdHis');
        $newSignedStatus = !empty($newHash);

        $updateData = [
            'ttd_digital_hash' => $newHash,
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        if ($newSignedStatus) {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        } else {
            if ($permohonan['status'] === 'completed') {
                $updateData['status'] = 'approved_admin';
                $updateData['completed_at'] = null;
            }
        }

        $db->table('permohonan_surat')
            ->where('id', $id)
            ->update($updateData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'is_signed' => $newSignedStatus,
                'message'   => $newSignedStatus ? 'Status berhasil diubah menjadi TTD dan diselesaikan (completed).' : 'Status tanda tangan berhasil dibatalkan.'
            ]);
        }

        return redirect()->back()->with('success', 'Status tanda tangan berhasil diperbarui.');
    }

    public function updateNomorSurat(int $id)
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');
        $noSurat = trim($this->request->getPost('no_surat_keluar') ?? '');

        if (empty($noSurat)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor surat tidak boleh kosong.']);
        }

        $builder = $db->table('permohonan_surat')->where('id', $id);
        if ($villageId) {
            $builder->where('village_id', $villageId);
        }

        $updated = $builder->update([
            'no_surat_keluar' => $noSurat,
            'updated_at'      => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Nomor surat berhasil diperbarui.',
                'no_surat' => $noSurat
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui nomor surat.']);
    }

    public function updateSurat(int $id)
    {
        $db = \Config\Database::connect();
        $villageId = (int) session('village_id');

        $builder = $db->table('permohonan_surat')->where('id', $id);
        if ($villageId) {
            $builder->where('village_id', $villageId);
        }
        $permohonan = $builder->get()->getRowArray();

        if (! $permohonan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data permohonan tidak ditemukan.']);
        }

        $noSurat = trim($this->request->getPost('no_surat_keluar') ?? '');
        $formData = !empty($permohonan['data_form']) ? json_decode($permohonan['data_form'], true) : [];
        if (!is_array($formData)) {
            $formData = [];
        }

        $fields = [
            'dalam_rangka',
            'tanggal_pelaksanaan',
            'hari_tanggal',
            'waktu_pelaksanaan',
            'pukul',
            'lokasi_kegiatan',
            'tempat',
            'jenis_hiburan',
            'acara',
            'tanggal_surat',
            'alamat_asal',
            'alamat_ktp',
            'keperluan',
            'tujuan',
            'alamat_sekarang'
        ];

        foreach ($fields as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $formData[$field] = trim($val);
            }
        }

        $updateData = [
            'data_form'  => json_encode($formData),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($noSurat)) {
            $updateData['no_surat_keluar'] = $noSurat;
        }

        $db->table('permohonan_surat')
            ->where('id', $id)
            ->update($updateData);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Perubahan data surat berhasil disimpan.',
            'no_surat'  => $updateData['no_surat_keluar'] ?? $permohonan['no_surat_keluar'],
            'data_form' => $formData
        ]);
    }
}
