<?php

namespace App\Controllers\AdminDesa;

use App\Controllers\BaseController;
use App\Models\PendudukModel;
use App\Models\PekerjaanModel;

class KependudukanController extends BaseController
{
    protected PendudukModel $pendudukModel;
    protected PekerjaanModel $pekerjaanModel;

    public function __construct()
    {
        $this->pendudukModel = new PendudukModel();
        $this->pekerjaanModel = new PekerjaanModel();
    }

    /**
     * Dapatkan base route path berdasarkan role pengguna
     */
    private function getBaseRoute(): string
    {
        $role = session('role_slug');
        if ($role === 'operator') {
            return 'operator/penduduk';
        }
        return 'admin-desa/kependudukan';
    }

    /**
     * Halaman daftar data penduduk
     */
    public function index()
    {
        $search         = $this->request->getGet('q');
        $gender         = $this->request->getGet('gender');
        $statusPenduduk = $this->request->getGet('status');
        $dusun          = $this->request->getGet('dusun');

        $villageId = (int) session('village_id');
        $db = \Config\Database::connect();

        $builder = $db->table('penduduk')
            ->where('deleted_at IS NULL');

        if ($villageId > 0) {
            $builder->where('village_id', $villageId);
        }

        if (!empty($search)) {
            $search = trim($search);
            $builder->groupStart()
                ->like('nama_lengkap', $search)
                ->orLike('nik', $search)
                ->orLike('no_kk', $search)
                ->orLike('alamat_lengkap', $search)
                ->orLike('pekerjaan', $search)
                ->groupEnd();
        }

        if (!empty($gender) && in_array($gender, ['L', 'P'])) {
            $builder->where('jenis_kelamin', $gender);
        }

        if (!empty($statusPenduduk)) {
            $builder->where('status_penduduk', $statusPenduduk);
        }

        if (!empty($dusun)) {
            $builder->where('dusun', $dusun);
        }

        $pendudukList = $builder->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        // Ambil daftar dusun unik untuk filter
        $dusunList = $db->table('penduduk')
            ->select('dusun')
            ->where('deleted_at IS NULL')
            ->where('village_id', $villageId)
            ->where('dusun IS NOT NULL')
            ->where('dusun !=', '')
            ->groupBy('dusun')
            ->orderBy('dusun', 'ASC')
            ->get()->getResultArray();

        $stats = $this->pendudukModel->getStatistik();

        return view('admin_desa/kependudukan/index', [
            'title'          => 'Data Kependudukan - SiPelayan Desa',
            'pendudukList'   => $pendudukList,
            'stats'          => $stats,
            'dusunList'      => array_column($dusunList, 'dusun'),
            'search'         => $search,
            'gender'         => $gender,
            'statusPenduduk' => $statusPenduduk,
            'selectedDusun'  => $dusun,
            'baseRoute'      => $this->getBaseRoute(),
        ]);
    }

    /**
     * Form tambah data penduduk
     */
    public function create()
    {
        $listPekerjaan = $this->pekerjaanModel->orderBy('nama', 'ASC')->findAll();

        return view('admin_desa/kependudukan/create', [
            'title'         => 'Tambah Data Penduduk - SiPelayan Desa',
            'baseRoute'     => $this->getBaseRoute(),
            'listPekerjaan' => $listPekerjaan,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Simpan data penduduk baru
     */
    public function store()
    {
        $villageId = (int) session('village_id');

        $rules = [
            'nik' => [
                'rules' => "required|exact_length[16]|numeric",
                'errors' => [
                    'required'     => 'NIK wajib diisi.',
                    'exact_length' => 'NIK harus tepat 16 digit.',
                    'numeric'      => 'NIK hanya boleh berupa angka.',
                ]
            ],
            'no_kk' => [
                'rules' => 'required|exact_length[16]|numeric',
                'errors' => [
                    'required'     => 'No. Kartu Keluarga wajib diisi.',
                    'exact_length' => 'No. KK harus tepat 16 digit.',
                    'numeric'      => 'No. KK hanya boleh berupa angka.',
                ]
            ],
            'nama_lengkap' => [
                'rules' => 'required|min_length[2]|max_length[150]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                ]
            ],
            'tempat_lahir' => 'required|max_length[100]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'agama' => 'required|in_list[Islam,Kristen Protestan,Kristen Katolik,Hindu,Buddha,Konghucu,Lainnya]',
            'status_perkawinan' => 'required|in_list[Belum Kawin,Kawin,Cerai Hidup,Cerai Mati]',
            'status_penduduk' => 'required|in_list[Tetap,Sementara,Pindah,Meninggal]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Cek keunikan NIK dalam desa yang sama
        $nik = trim($this->request->getPost('nik'));
        $existing = $this->pendudukModel->where('nik', $nik)->where('village_id', $villageId)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "NIK {$nik} sudah terdaftar di desa ini.");
        }

        $data = [
            'village_id'         => $villageId,
            'nik'                => $nik,
            'no_kk'              => trim($this->request->getPost('no_kk')),
            'nama_lengkap'       => trim($this->request->getPost('nama_lengkap')),
            'tempat_lahir'       => trim($this->request->getPost('tempat_lahir')),
            'tanggal_lahir'      => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'golongan_darah'     => $this->request->getPost('golongan_darah') ?: null,
            'agama'              => $this->request->getPost('agama'),
            'status_perkawinan'  => $this->request->getPost('status_perkawinan'),
            'pekerjaan'          => trim($this->request->getPost('pekerjaan') ?? ''),
            'pendidikan'         => $this->request->getPost('pendidikan') ?: null,
            'kewarganegaraan'    => $this->request->getPost('kewarganegaraan') ?: 'WNI',
            'status_hubungan_kk' => $this->request->getPost('status_hubungan_kk') ?: null,
            'rt'                 => trim($this->request->getPost('rt') ?? ''),
            'rw'                 => trim($this->request->getPost('rw') ?? ''),
            'dusun'              => trim($this->request->getPost('dusun') ?? ''),
            'alamat_lengkap'     => trim($this->request->getPost('alamat_lengkap') ?? ''),
            'status_penduduk'    => $this->request->getPost('status_penduduk') ?: 'Tetap',
            'created_by'         => session('user_id'),
        ];

        $this->pendudukModel->insert($data);

        return redirect()->to(base_url($this->getBaseRoute()))->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    /**
     * Detail lengkap data penduduk
     */
    public function detail($id)
    {
        $villageId = (int) session('village_id');
        $penduduk = $this->pendudukModel->where('id', $id)->first();

        if (!$penduduk || ($villageId > 0 && $penduduk['village_id'] != $villageId)) {
            return redirect()->to(base_url($this->getBaseRoute()))->with('error', 'Data penduduk tidak ditemukan.');
        }

        return view('admin_desa/kependudukan/detail', [
            'title'     => 'Detail Penduduk - ' . esc($penduduk['nama_lengkap']),
            'penduduk'  => $penduduk,
            'baseRoute' => $this->getBaseRoute(),
        ]);
    }

    /**
     * Form edit data penduduk
     */
    public function edit($id)
    {
        $villageId = (int) session('village_id');
        $penduduk = $this->pendudukModel->where('id', $id)->first();

        if (!$penduduk || ($villageId > 0 && $penduduk['village_id'] != $villageId)) {
            return redirect()->to(base_url($this->getBaseRoute()))->with('error', 'Data penduduk tidak ditemukan.');
        }

        $listPekerjaan = $this->pekerjaanModel->orderBy('nama', 'ASC')->findAll();

        return view('admin_desa/kependudukan/edit', [
            'title'         => 'Edit Data Penduduk - ' . esc($penduduk['nama_lengkap']),
            'penduduk'      => $penduduk,
            'baseRoute'     => $this->getBaseRoute(),
            'listPekerjaan' => $listPekerjaan,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ]);
    }

    /**
     * Update data penduduk
     */
    public function update($id)
    {
        $villageId = (int) session('village_id');
        $penduduk = $this->pendudukModel->where('id', $id)->first();

        if (!$penduduk || ($villageId > 0 && $penduduk['village_id'] != $villageId)) {
            return redirect()->to(base_url($this->getBaseRoute()))->with('error', 'Data penduduk tidak ditemukan.');
        }

        $rules = [
            'nik' => [
                'rules' => "required|exact_length[16]|numeric",
                'errors' => [
                    'required'     => 'NIK wajib diisi.',
                    'exact_length' => 'NIK harus tepat 16 digit.',
                    'numeric'      => 'NIK hanya boleh berupa angka.',
                ]
            ],
            'no_kk' => [
                'rules' => 'required|exact_length[16]|numeric',
                'errors' => [
                    'required'     => 'No. Kartu Keluarga wajib diisi.',
                    'exact_length' => 'No. KK harus tepat 16 digit.',
                    'numeric'      => 'No. KK hanya boleh berupa angka.',
                ]
            ],
            'nama_lengkap' => [
                'rules' => 'required|min_length[2]|max_length[150]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.',
                ]
            ],
            'tempat_lahir' => 'required|max_length[100]',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'agama' => 'required|in_list[Islam,Kristen Protestan,Kristen Katolik,Hindu,Buddha,Konghucu,Lainnya]',
            'status_perkawinan' => 'required|in_list[Belum Kawin,Kawin,Cerai Hidup,Cerai Mati]',
            'status_penduduk' => 'required|in_list[Tetap,Sementara,Pindah,Meninggal]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $nik = trim($this->request->getPost('nik'));
        if ($nik !== $penduduk['nik']) {
            $existing = $this->pendudukModel->where('nik', $nik)->where('village_id', $villageId)->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('error', "NIK {$nik} sudah terdaftar pada penduduk lain.");
            }
        }

        $data = [
            'nik'                => $nik,
            'no_kk'              => trim($this->request->getPost('no_kk')),
            'nama_lengkap'       => trim($this->request->getPost('nama_lengkap')),
            'tempat_lahir'       => trim($this->request->getPost('tempat_lahir')),
            'tanggal_lahir'      => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'golongan_darah'     => $this->request->getPost('golongan_darah') ?: null,
            'agama'              => $this->request->getPost('agama'),
            'status_perkawinan'  => $this->request->getPost('status_perkawinan'),
            'pekerjaan'          => trim($this->request->getPost('pekerjaan') ?? ''),
            'pendidikan'         => $this->request->getPost('pendidikan') ?: null,
            'kewarganegaraan'    => $this->request->getPost('kewarganegaraan') ?: 'WNI',
            'status_hubungan_kk' => $this->request->getPost('status_hubungan_kk') ?: null,
            'rt'                 => trim($this->request->getPost('rt') ?? ''),
            'rw'                 => trim($this->request->getPost('rw') ?? ''),
            'dusun'              => trim($this->request->getPost('dusun') ?? ''),
            'alamat_lengkap'     => trim($this->request->getPost('alamat_lengkap') ?? ''),
            'status_penduduk'    => $this->request->getPost('status_penduduk') ?: 'Tetap',
            'updated_by'         => session('user_id'),
        ];

        $this->pendudukModel->update($id, $data);

        return redirect()->to(base_url($this->getBaseRoute()))->with('success', 'Data penduduk berhasil diperbarui.');
    }

    /**
     * Halaman form import data kependudukan
     */
    public function importForm()
    {
        return view('admin_desa/kependudukan/import', [
            'title'     => 'Import Data Penduduk - SiPelayan Desa',
            'baseRoute' => $this->getBaseRoute(),
        ]);
    }

    /**
     * Download template file CSV untuk import
     */
    public function downloadTemplate()
    {
        $headers = [
            'NIK',
            'No_KK',
            'Nama_Lengkap',
            'Tempat_Lahir',
            'Tanggal_Lahir',
            'Jenis_Kelamin',
            'Golongan_Darah',
            'Agama',
            'Status_Perkawinan',
            'Pekerjaan',
            'Pendidikan',
            'Kewarganegaraan',
            'Status_Hubungan_KK',
            'RT',
            'RW',
            'Dusun',
            'Alamat_Lengkap',
            'Status_Penduduk',
        ];

        $sampleRows = [
            [
                '3201010101900001',
                '3201010101900001',
                'Budi Santoso',
                'Bandung',
                '1990-05-15',
                'L',
                'O',
                'Islam',
                'Kawin',
                'Wiraswasta',
                'S1',
                'WNI',
                'Kepala Keluarga',
                '001',
                '002',
                'Dusun Sukamaju',
                'Jl. Mawar No. 10',
                'Tetap',
            ],
            [
                '3201014101920002',
                '3201010101900001',
                'Siti Aminah',
                'Bandung',
                '1992-08-20',
                'P',
                'A',
                'Islam',
                'Kawin',
                'Ibu Rumah Tangga',
                'SMA/SMK',
                'WNI',
                'Istri',
                '001',
                '002',
                'Dusun Sukamaju',
                'Jl. Mawar No. 10',
                'Tetap',
            ],
        ];

        $filename = 'template_import_penduduk.csv';

        // Output CSV dengan UTF-8 BOM dan delimiter titik koma (;) agar langsung terpisah kolom saat dibuka di Microsoft Excel (Windows/Regional Indonesia)
        $output = "\xEF\xBB\xBF";
        $output .= implode(';', array_map(fn($h) => '"' . str_replace('"', '""', $h) . '"', $headers)) . "\r\n";
        foreach ($sampleRows as $row) {
            $output .= implode(';', array_map(fn($val) => '"' . str_replace('"', '""', $val) . '"', $row)) . "\r\n";
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($output);
    }

    /**
     * Proses file import data kependudukan (CSV)
     */
    public function processImport()
    {
        $villageId = (int) session('village_id');
        $duplicateAction = $this->request->getPost('duplicate_action') ?: 'skip'; // 'skip' atau 'update'

        $file = $this->request->getFile('file_import');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Silakan pilih file CSV/Excel yang valid untuk diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa CSV (.csv) atau Teks (.txt).');
        }

        $filepath = $file->getTempName();
        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Gagal membuka file yang diunggah.');
        }

        // Baca baris pertama untuk deteksi delimiter dan headers
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'File yang diunggah kosong.');
        }

        // Hapus BOM jika ada
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        // Deteksi delimiter (koma, titik koma, atau tab)
        $delimiters = [',', ';', "\t"];
        $detectedDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($firstLine, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $d;
            }
        }

        $headers = str_getcsv(trim($firstLine), $detectedDelimiter);
        $headerMap = [];
        foreach ($headers as $index => $col) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $col));
            $headerMap[$cleanKey] = $index;
        }

        // Mapping alias kolom
        $colMap = [
            'nik'                => ['nik', 'nomorindukkependudukan', 'nomorktp'],
            'no_kk'              => ['nokk', 'nomorkk', 'nomorkartukeluarga', 'kartukeluarga'],
            'nama_lengkap'       => ['namalengkap', 'nama', 'namawarga'],
            'tempat_lahir'       => ['tempatlahir', 'tmplahir', 'kotaslahir'],
            'tanggal_lahir'      => ['tanggallahir', 'tgllahir', 'tgllahiryyyymmdd'],
            'jenis_kelamin'      => ['jeniskelamin', 'jk', 'gender', 'sex'],
            'golongan_darah'     => ['golongandarah', 'goldar', 'golongan_darah'],
            'agama'              => ['agama', 'religi'],
            'status_perkawinan'  => ['statusperkawinan', 'statuskawin', 'perkawinan', 'statusnikah'],
            'pekerjaan'          => ['pekerjaan', 'profesi'],
            'pendidikan'         => ['pendidikan', 'pendidikanterakhir'],
            'kewarganegaraan'    => ['kewarganegaraan', 'warga_negara', 'wn'],
            'status_hubungan_kk' => ['statushubungankk', 'hubungankk', 'statuskeluarga', 'shdk'],
            'rt'                 => ['rt'],
            'rw'                 => ['rw'],
            'dusun'              => ['dusun', 'lingkungan', 'kampung', 'dukuh'],
            'alamat_lengkap'     => ['alamatlengkap', 'alamat', 'jalan'],
            'status_penduduk'    => ['statuspenduduk', 'statuskeberadaan', 'statuswarga'],
        ];

        $getColIndex = function($field) use ($headerMap, $colMap) {
            if (!isset($colMap[$field])) return null;
            foreach ($colMap[$field] as $alias) {
                if (isset($headerMap[$alias])) {
                    return $headerMap[$alias];
                }
            }
            return null;
        };

        $totalRows     = 0;
        $importedCount = 0;
        $updatedCount  = 0;
        $skippedCount  = 0;
        $errors        = [];

        $rowNumber = 1; // Baris 1 adalah header

        while (($row = fgetcsv($handle, 0, $detectedDelimiter)) !== false) {
            $rowNumber++;

            // Abaikan baris kosong
            if (empty(array_filter($row, fn($val) => trim($val) !== ''))) {
                continue;
            }

            $totalRows++;

            $getValue = function($field, $default = '') use ($row, $getColIndex) {
                $idx = $getColIndex($field);
                if ($idx !== null && isset($row[$idx])) {
                    return trim($row[$idx]);
                }
                return $default;
            };

            // Ambil NIK dan No KK (bersihkan karakter non-digit atau tanda kutip)
            $nik = preg_replace('/[^0-9]/', '', $getValue('nik'));
            $noKk = preg_replace('/[^0-9]/', '', $getValue('no_kk'));
            $namaLengkap = $getValue('nama_lengkap');
            $tempatLahir = $getValue('tempat_lahir');
            $rawTglLahir = $getValue('tanggal_lahir');
            $rawJk       = strtoupper($getValue('jenis_kelamin'));
            $goldar      = strtoupper($getValue('golongan_darah'));
            $agama       = $getValue('agama');
            $statusKawin = $getValue('status_perkawinan');
            $pekerjaan   = $getValue('pekerjaan');
            $pendidikan  = $getValue('pendidikan');
            $kewarganegaraan = $getValue('kewarganegaraan') ?: 'WNI';
            $statusHubKk = $getValue('status_hubungan_kk');
            $rt          = $getValue('rt');
            $rw          = $getValue('rw');
            $dusun       = $getValue('dusun');
            $alamat      = $getValue('alamat_lengkap');
            $statusPend  = $getValue('status_penduduk') ?: 'Tetap';

            // Validasi dasar NIK & No KK
            if (strlen($nik) !== 16) {
                $errors[] = "Baris {$rowNumber}: NIK '{$getValue('nik')}' tidak valid (harus 16 digit angka).";
                $skippedCount++;
                continue;
            }

            if (strlen($noKk) !== 16) {
                $errors[] = "Baris {$rowNumber}: No. KK '{$getValue('no_kk')}' tidak valid (harus 16 digit angka).";
                $skippedCount++;
                continue;
            }

            if (empty($namaLengkap)) {
                $errors[] = "Baris {$rowNumber}: Nama Lengkap wajib diisi.";
                $skippedCount++;
                continue;
            }

            if (empty($tempatLahir)) {
                $tempatLahir = '-';
            }

            // Normalisasi Tanggal Lahir (YYYY-MM-DD, DD/MM/YYYY, DD-MM-YYYY)
            $tanggalLahir = null;
            if (!empty($rawTglLahir)) {
                $tglTime = strtotime($rawTglLahir);
                if ($tglTime !== false) {
                    $tanggalLahir = date('Y-m-d', $tglTime);
                } else {
                    $d = \DateTime::createFromFormat('d/m/Y', $rawTglLahir);
                    if ($d && $d->format('d/m/Y') === $rawTglLahir) {
                        $tanggalLahir = $d->format('Y-m-d');
                    }
                }
            }

            if (!$tanggalLahir) {
                $errors[] = "Baris {$rowNumber}: Tanggal Lahir '{$rawTglLahir}' tidak valid (format: YYYY-MM-DD atau DD/MM/YYYY).";
                $skippedCount++;
                continue;
            }

            // Normalisasi Jenis Kelamin
            $jenisKelamin = 'L';
            if (in_array($rawJk, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE', 'F'])) {
                $jenisKelamin = 'P';
            } elseif (in_array($rawJk, ['L', 'LAKI-LAKI', 'LAKI', 'PRIA', 'MALE', 'M'])) {
                $jenisKelamin = 'L';
            }

            // Normalisasi Agama
            $validAgama = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
            $matchedAgama = 'Islam';
            foreach ($validAgama as $va) {
                if (strcasecmp($va, $agama) === 0 || stripos($agama, $va) !== false) {
                    $matchedAgama = $va;
                    break;
                }
            }

            // Normalisasi Status Perkawinan
            $validKawin = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
            $matchedKawin = 'Belum Kawin';
            foreach ($validKawin as $vk) {
                if (strcasecmp($vk, $statusKawin) === 0 || stripos($statusKawin, $vk) !== false) {
                    $matchedKawin = $vk;
                    break;
                }
            }

            // Normalisasi Status Penduduk
            $validStatusPend = ['Tetap', 'Sementara', 'Pindah', 'Meninggal'];
            $matchedStatusPend = 'Tetap';
            foreach ($validStatusPend as $vsp) {
                if (strcasecmp($vsp, $statusPend) === 0 || stripos($statusPend, $vsp) !== false) {
                    $matchedStatusPend = $vsp;
                    break;
                }
            }

            // Normalisasi Golongan Darah
            $matchedGoldar = null;
            if (in_array($goldar, ['A', 'B', 'AB', 'O'])) {
                $matchedGoldar = $goldar;
            }

            $payload = [
                'village_id'         => $villageId,
                'nik'                => $nik,
                'no_kk'              => $noKk,
                'nama_lengkap'       => $namaLengkap,
                'tempat_lahir'       => $tempatLahir,
                'tanggal_lahir'      => $tanggalLahir,
                'jenis_kelamin'      => $jenisKelamin,
                'golongan_darah'     => $matchedGoldar,
                'agama'              => $matchedAgama,
                'status_perkawinan'  => $matchedKawin,
                'pekerjaan'          => $pekerjaan,
                'pendidikan'         => $pendidikan ?: null,
                'kewarganegaraan'    => $kewarganegaraan,
                'status_hubungan_kk' => $statusHubKk ?: null,
                'rt'                 => $rt,
                'rw'                 => $rw,
                'dusun'              => $dusun,
                'alamat_lengkap'     => $alamat,
                'status_penduduk'    => $matchedStatusPend,
            ];

            // Cek apakah NIK sudah ada di desa ini
            $existing = $this->pendudukModel->where('nik', $nik)->where('village_id', $villageId)->first();

            if ($existing) {
                if ($duplicateAction === 'update') {
                    $payload['updated_by'] = session('user_id');
                    $this->pendudukModel->update($existing['id'], $payload);
                    $updatedCount++;
                } else {
                    $skippedCount++;
                    $errors[] = "Baris {$rowNumber}: NIK {$nik} ({$namaLengkap}) sudah ada di database (dilewati).";
                }
            } else {
                $payload['created_by'] = session('user_id');
                $this->pendudukModel->insert($payload);
                $importedCount++;
            }
        }

        fclose($handle);

        $summary = [
            'total'    => $totalRows,
            'imported' => $importedCount,
            'updated'  => $updatedCount,
            'skipped'  => $skippedCount,
            'errors'   => array_slice($errors, 0, 50), // Maksimal 50 pesan error agar tidak overload session
        ];

        session()->setFlashdata('import_summary', $summary);

        if ($importedCount > 0 || $updatedCount > 0) {
            $msg = "Import selesai! {$importedCount} data baru ditambahkan";
            if ($updatedCount > 0) {
                $msg .= ", {$updatedCount} data diperbarui";
            }
            if ($skippedCount > 0) {
                $msg .= ", {$skippedCount} data dilewati/gagal.";
            } else {
                $msg .= ".";
            }
            return redirect()->to(base_url($this->getBaseRoute() . '/import'))->with('success', $msg);
        }

        return redirect()->to(base_url($this->getBaseRoute() . '/import'))->with('warning', 'Tidak ada data penduduk yang berhasil diimpor.');
    }

    /**
     * Hapus data penduduk (Soft delete)
     */
    public function delete($id)
    {
        $villageId = (int) session('village_id');
        $penduduk = $this->pendudukModel->where('id', $id)->first();

        if (!$penduduk || ($villageId > 0 && $penduduk['village_id'] != $villageId)) {
            return redirect()->to(base_url($this->getBaseRoute()))->with('error', 'Data penduduk tidak ditemukan.');
        }

        $this->pendudukModel->delete($id);

        return redirect()->to(base_url($this->getBaseRoute()))->with('success', 'Data penduduk berhasil dihapus.');
    }
}
