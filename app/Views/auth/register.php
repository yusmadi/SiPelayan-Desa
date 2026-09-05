<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Daftar - SiPelayan Desa' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Tom Select CSS (Bootstrap 5 theme) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .auth-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .ts-control {
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            border-color: #dee2e6 !important;
            min-height: 38px;
            display: flex;
            align-items: center;
        }
        .ts-dropdown {
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            border-color: #dee2e6 !important;
        }
        .ts-dropdown .active {
            background-color: #0d6efd !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-light" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="text-center mb-4">
                    <a href="/" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="bi bi-shield-check me-2"></i>SiPelayan-Desa</h2>
                    </a>
                </div>

                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-1">Daftar Akun Baru</h4>
                        <p class="text-muted mb-4">Lengkapi form di bawah untuk membuat akun warga.</p>

                        <?php if(session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger p-2 mb-4">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/auth/register" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap sesuai KTP" required maxlength="150" value="<?= old('nama_lengkap') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" required maxlength="16" minlength="16" pattern="[0-9]{16}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="NIK harus terdiri dari 16 digit angka" value="<?= old('nik') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Pilih Desa Domisili <span class="text-danger">*</span></label>
                                    <select name="village_id" id="village_select" class="form-select" required>
                                        <option value="">-- Ketik nama desa atau kecamatan --</option>
                                        <?php if (!empty($selectedDesa)): ?>
                                            <option value="<?= esc($selectedDesa['id']) ?>" selected>
                                                Desa <?= esc($selectedDesa['nama_desa']) ?><?= !empty($selectedDesa['nama_kecamatan']) ? ' (Kec. ' . esc($selectedDesa['nama_kecamatan']) . (!empty($selectedDesa['nama_kabupaten']) ? ', ' . esc($selectedDesa['nama_kabupaten']) : '') . ')' : '' ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label fw-medium">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= old('email') ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="8 karakter" required minlength="8" maxlength="8">
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    Saya setuju dengan <a href="#">Syarat dan Ketentuan</a> serta menyatakan bahwa data yang diisi adalah benar.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">Daftar Sekarang</button>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center py-3 border-0">
                        <span class="text-muted small">Sudah punya akun? <a href="/auth/login" class="text-decoration-none fw-semibold">Login di sini</a></span>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Pencarian Desa Asinkron (Remote AJAX)
            new TomSelect('#village_select', {
                valueField: 'id',
                labelField: 'text',
                searchField: ['text'],
                placeholder: "-- Ketik nama desa atau kecamatan --",
                allowEmptyOption: true,
                loadThrottle: 300,
                preload: 'focus',
                load: function(query, callback) {
                    var url = '/auth/search-desa?q=' + encodeURIComponent(query);
                    fetch(url)
                        .then(function(response) {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(function(data) {
                            callback(data);
                        })
                        .catch(function() {
                            callback();
                        });
                },
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results p-2 text-muted small">Desa tidak ditemukan</div>';
                    },
                    loading: function(data, escape) {
                        return '<div class="p-2 text-muted small"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Mencari desa...</div>';
                    }
                }
            });
        });
    </script>

    <?php if(session()->getFlashdata('swal_error')): ?>
        <?php 
            $swalError = session()->getFlashdata('swal_error'); 
            $title = is_array($swalError) ? ($swalError['title'] ?? 'Registrasi Gagal') : 'Registrasi Gagal';
            $text  = is_array($swalError) ? ($swalError['text'] ?? '') : $swalError;
            $icon  = is_array($swalError) ? ($swalError['icon'] ?? 'error') : 'error';
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: <?= json_encode($icon) ?>,
                    title: <?= json_encode($title) ?>,
                    text: <?= json_encode($text) ?>,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Mengerti'
                });
            });
        </script>
    <?php elseif(session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: <?= json_encode(session()->getFlashdata('error')) ?>,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Tutup'
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
