<?php

namespace App\Controllers\Warga;

use App\Controllers\BaseController;
use App\Models\PengaduanModel;

class PengaduanController extends BaseController
{
    public function index()
    {
        $userId = session('user_id');
        $villageId = session('village_id');

        $model = new PengaduanModel();
        
        $statusFilter   = $this->request->getGet('status');
        $kategoriFilter = $this->request->getGet('kategori');
        $search         = $this->request->getGet('q');

        $query = $model->where('pelapor_id', $userId);

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($kategoriFilter)) {
            $query->where('kategori', $kategoriFilter);
        }

        if (!empty($search)) {
            $query->groupStart()
                ->like('judul', $search)
                ->orLike('no_tiket', $search)
                ->orLike('isi_laporan', $search)
                ->groupEnd();
        }

        $pengaduanList = $query->orderBy('id', 'DESC')->findAll();

        // Status counts for logged-in user
        $db = \Config\Database::connect();
        $countByStatus = function (?string $status = null) use ($db, $userId, $villageId) {
            $builder = $db->table('pengaduan')->where('pelapor_id', $userId);
            if ($villageId) {
                $builder->where('village_id', $villageId);
            }
            if ($status !== null) {
                $builder->where('status', $status);
            }
            return $builder->countAllResults();
        };

        $totalPengaduan = $countByStatus();
        $totalMenunggu  = $countByStatus('open');
        $totalDiproses  = $countByStatus('in_progress');
        $totalSelesai   = $countByStatus('resolved');

        return view('warga/pengaduan/index', [
            'title'          => 'Layanan Pengaduan Warga - SiPelayan Desa',
            'pengaduanList'  => $pengaduanList,
            'kategoriList'   => PengaduanModel::KATEGORI_LIST,
            'statusLabels'   => PengaduanModel::STATUS_LABELS,
            'statusColors'   => PengaduanModel::STATUS_COLORS,
            'statusFilter'   => $statusFilter,
            'kategoriFilter' => $kategoriFilter,
            'search'         => $search,
            'stats'          => [
                'total'    => $totalPengaduan,
                'menunggu' => $totalMenunggu,
                'diproses' => $totalDiproses,
                'selesai'  => $totalSelesai,
            ],
        ]);
    }

    public function buat()
    {
        return view('warga/pengaduan/buat', [
            'title'        => 'Buat Pengaduan Baru - SiPelayan Desa',
            'kategoriList' => PengaduanModel::KATEGORI_LIST,
        ]);
    }

    public function simpan()
    {
        $rules = [
            'kategori'    => 'required|in_list[' . implode(',', array_keys(PengaduanModel::KATEGORI_LIST)) . ']',
            'judul'       => 'required|min_length[5]|max_length[255]',
            'isi_laporan' => 'required|min_length[10]',
            'lokasi'      => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = session('user_id');
        $villageId = session('village_id') ?? 1;

        // Generate No Tiket: PGD-[village_id]-[YYYYMMDD]-[random 4 chars]
        $noTiket = sprintf(
            'PGD-%02d-%s-%s',
            $villageId,
            date('Ymd'),
            strtoupper(substr(bin2hex(random_bytes(2)), 0, 4))
        );

        // Handle uploaded images
        $fotoPaths = [];
        $files = $this->request->getFiles();

        if (isset($files['fotos']) && is_array($files['fotos'])) {
            $uploadPath = FCPATH . 'uploads/pengaduan';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files['fotos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $ext = strtolower($file->getClientExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'heic'])) {
                        $newName = 'pgd_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $file->move($uploadPath, $newName);
                        $fotoPaths[] = 'uploads/pengaduan/' . $newName;
                    }
                }
            }
        }

        $model = new PengaduanModel();
        $model->insert([
            'village_id'   => $villageId,
            'pelapor_id'   => $userId,
            'no_tiket'     => $noTiket,
            'kategori'     => $this->request->getPost('kategori'),
            'judul'        => trim($this->request->getPost('judul')),
            'isi_laporan'  => trim($this->request->getPost('isi_laporan')),
            'lokasi'       => trim($this->request->getPost('lokasi')),
            'foto_paths'   => !empty($fotoPaths) ? json_encode($fotoPaths) : null,
            'status'       => 'open',
            'is_anonymous' => $this->request->getPost('is_anonymous') ? 1 : 0,
        ]);

        return redirect()->to('/warga/pengaduan')->with('success', 'Pengaduan berhasil diajukan dengan Nomor Tiket: ' . $noTiket);
    }

    public function detail(int $id)
    {
        $userId = session('user_id');
        $model = new PengaduanModel();

        $pengaduan = $model->where('pelapor_id', $userId)->find($id);

        if (!$pengaduan) {
            return redirect()->to('/warga/pengaduan')->with('error', 'Pengaduan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Get responder info if available
        $petugas = null;
        if (!empty($pengaduan['ditangani_oleh'])) {
            $userModel = new \App\Models\UserModel();
            $petugas = $userModel->find($pengaduan['ditangani_oleh']);
        }

        return view('warga/pengaduan/detail', [
            'title'        => 'Detail Pengaduan #' . $pengaduan['no_tiket'] . ' - SiPelayan Desa',
            'pengaduan'    => $pengaduan,
            'petugas'      => $petugas,
            'statusLabels' => PengaduanModel::STATUS_LABELS,
            'statusColors' => PengaduanModel::STATUS_COLORS,
        ]);
    }
}
