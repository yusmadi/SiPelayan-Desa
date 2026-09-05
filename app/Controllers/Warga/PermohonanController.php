<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Models\PermohonanSuratModel;
use App\Models\JenisSuratModel;
use App\Services\PermohonanService;

class PermohonanController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

        $builder = $db->table('permohonan_surat')
            ->select('permohonan_surat.*, jenis_surat.nama as nama_surat, jenis_surat.kode as kode_surat, jenis_surat.deskripsi as deskripsi_surat')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id', 'left');

        // Jika user warga login, filter berdasarkan pemohon_id
        if ($userId) {
            $builder->where('permohonan_surat.pemohon_id', $userId);
        }

        if ($villageId) {
            $builder->where('permohonan_surat.village_id', $villageId);
        }

        $permohonan = $builder->orderBy('permohonan_surat.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('warga/permohonan/index', [
            'title'      => 'Riwayat Permohonan Surat - SiPelayan Desa',
            'permohonan' => $permohonan,
        ]);
    }

    public function buat()
    {
        $jenisSuratModel = new JenisSuratModel();
        $jenisSurat = $jenisSuratModel->where('is_active', 1)->findAll();

        return view('warga/permohonan/buat', [
            'title'      => 'Ajukan Surat Baru - SiPelayan Desa',
            'jenisSurat' => $jenisSurat
        ]);
    }

    public function simpan()
    {
        $rules = [
            'jenis_surat_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenisSuratId = (int) $this->request->getPost('jenis_surat_id');
        $dataForm = [];

        // Ambil semua data form dinamis yang berawalan data_form_
        $allPost = $this->request->getPost();
        foreach ($allPost as $key => $val) {
            if (str_starts_with($key, 'data_form_')) {
                $fieldName = str_replace('data_form_', '', $key);
                $dataForm[$fieldName] = $val;
            }
        }

        // Handle upload dokumen syarat jika ada
        $dokumenSyarat = [];
        $file = $this->request->getFile('dokumen_syarat');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/syarat';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $dokumenSyarat[] = $newName;
        }

        $permohonanService = new PermohonanService();
        $res = $permohonanService->buatPermohonan([
            'village_id'     => (int) (session('village_id') ?? 1),
            'jenis_surat_id' => $jenisSuratId,
            'pemohon_id'     => (int) (session('user_id') ?? 4),
            'data_form'      => $dataForm,
            'dokumen_syarat' => $dokumenSyarat,
            'channel'        => 'online',
            'priority'       => 'normal',
        ]);

        if (! ($res['success'] ?? false)) {
            return redirect()->back()->withInput()->with('errors', [$res['message'] ?? 'Gagal membuat permohonan surat.']);
        }

        return redirect()->to('/warga/permohonan')->with('success', 'Permohonan surat dengan nomor ' . esc($res['no_permohonan']) . ' berhasil diajukan!');
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

        $builder = $db->table('permohonan_surat')
            ->select('permohonan_surat.*, jenis_surat.nama as nama_surat, jenis_surat.kode as kode_surat, users.nama_lengkap as nama_pemohon, users.nik as nik_pemohon')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id', 'left')
            ->join('users', 'users.id = permohonan_surat.pemohon_id', 'left')
            ->where('permohonan_surat.id', $id);

        if ($userId) {
            $builder->where('permohonan_surat.pemohon_id', $userId);
        }
        if ($villageId) {
            $builder->where('permohonan_surat.village_id', $villageId);
        }

        $permohonan = $builder->get()->getRowArray();

        if (! $permohonan) {
            return redirect()->to('/warga/permohonan')->with('error', 'Data permohonan tidak ditemukan.');
        }

        return view('warga/permohonan/detail', [
            'title'      => 'Detail Permohonan - SiPelayan Desa',
            'permohonan' => $permohonan
        ]);
    }

    public function tracking($noPermohonan)
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

        $builder = $db->table('permohonan_surat')
            ->select('permohonan_surat.*, jenis_surat.nama as nama_surat, jenis_surat.kode as kode_surat')
            ->join('jenis_surat', 'jenis_surat.id = permohonan_surat.jenis_surat_id', 'left')
            ->where('permohonan_surat.no_permohonan', $noPermohonan);

        if ($userId) {
            $builder->where('permohonan_surat.pemohon_id', $userId);
        }
        if ($villageId) {
            $builder->where('permohonan_surat.village_id', $villageId);
        }

        $permohonan = $builder->get()->getRowArray();

        return view('warga/permohonan/tracking', [
            'title'      => 'Tracking Permohonan - SiPelayan Desa',
            'permohonan' => $permohonan
        ]);
    }

    public function cancel($id)
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

        $builder = $db->table('permohonan_surat')
            ->where('id', $id)
            ->where('status', 'submitted');

        if ($userId) {
            $builder->where('pemohon_id', $userId);
        }
        if ($villageId) {
            $builder->where('village_id', $villageId);
        }

        $builder->update([
            'status'     => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/warga/permohonan')->with('success', 'Permohonan berhasil dibatalkan.');
    }

    public function preview(int $id)
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        $villageId = session('village_id');

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

        if (session('role_slug') === 'warga' && $userId) {
            $builder->where('permohonan_surat.pemohon_id', $userId);
        }
        if ($villageId) {
            $builder->where('permohonan_surat.village_id', $villageId);
        }

        $permohonan = $builder->get()->getRowArray();

        if (! $permohonan) {
            return redirect()->to('/warga/permohonan')->with('error', 'Data permohonan surat tidak ditemukan.');
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

        // Preview nomor surat jika belum ada
        if (empty($permohonan['no_surat_keluar'])) {
            $romawiBulan = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $bln = $romawiBulan[(int)date('n')];
            $thn = date('Y');
            $kodeSurat = !empty($permohonan['kode_surat']) ? strtoupper($permohonan['kode_surat']) : 'DS';
            $seq = sprintf('%03d', $permohonan['id']);
            $permohonan['no_surat_keluar'] = "470 / {$seq} / {$kodeSurat} / {$bln} / {$thn} (PRATINJAU)";
        }

        // Parse data isian form
        $formData = [];
        if (!empty($permohonan['data_form'])) {
            $decoded = json_decode($permohonan['data_form'], true);
            if (is_array($decoded)) {
                $formData = $decoded;
            }
        }

        return view('warga/permohonan/preview_surat', [
            'title'      => 'Pratinjau Surat - ' . ($permohonan['nama_surat'] ?? 'Surat'),
            'permohonan' => $permohonan,
            'formData'   => $formData
        ]);
    }
}

