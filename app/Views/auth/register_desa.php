<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Daftarkan Desa - SiPelayan Desa' ?></title>
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
        .upload-dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .upload-dropzone:hover, .upload-dropzone.dragover {
            border-color: #0d6efd;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-light" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                
                <div class="text-center mb-4">
                    <a href="/" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="bi bi-shield-check me-2"></i>SiPelayan-Desa</h2>
                    </a>
                </div>

                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="fw-bold mb-0">Pendaftaran Akun Desa</h4>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i class="bi bi-building me-1"></i> Kepala Desa / Admin Desa
                            </span>
                        </div>
                        <p class="text-muted small mb-3">Lengkapi formulir di bawah ini untuk mendaftarkan desa dan akun Administrator Desa Anda.</p>

                        <div class="alert alert-info d-flex align-items-start p-3 mb-4 rounded-3 border-0 bg-info-subtle text-info-emphasis" role="alert">
                            <i class="bi bi-info-circle-fill fs-5 me-2 flex-shrink-0 mt-1"></i>
                            <div class="small">
                                <strong>Informasi Verifikasi:</strong> Akun Kepala Desa yang didaftarkan akan berstatus <strong>Non-Aktif (is_active=0)</strong> sementara untuk proses verifikasi dokumen SK oleh <strong>Super Administrator</strong>.
                            </div>
                        </div>

                        <?php if(session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger p-2 mb-4">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/auth/register-desa" method="post" enctype="multipart/form-data" id="formRegisterDesa">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">Nama Lengkap Kepala Desa <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Drs. Bambang Sudarsono, M.Si" required maxlength="150" value="<?= old('nama_lengkap') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK Kepala Desa" required maxlength="16" minlength="16" pattern="[0-9]{16}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="NIK harus terdiri dari 16 digit angka" value="<?= old('nik') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Nomor HP / WhatsApp Aktif <span class="text-danger">*</span></label>
                                    <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" required maxlength="20" minlength="8" value="<?= old('no_hp') ?>">
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-medium">Pilih Wilayah Desa <span class="text-danger">*</span></label>
                                    <select name="village_id" id="village_select" class="form-select" required>
                                        <option value="">-- Ketik nama desa atau kecamatan --</option>
                                        <?php if (!empty($selectedDesa)): ?>
                                            <option value="<?= esc($selectedDesa['id']) ?>" selected>
                                                Desa <?= esc($selectedDesa['nama_desa']) ?><?= !empty($selectedDesa['nama_kecamatan']) ? ' (Kec. ' . esc($selectedDesa['nama_kecamatan']) . (!empty($selectedDesa['nama_kabupaten']) ? ', ' . esc($selectedDesa['nama_kabupaten']) : '') . ')' : '' ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text small text-muted">Pilih wilayah desa yang akan didaftarkan dan dikelola.</div>
                                </div>
                            </div>
                            
                            <!-- Box Upload SK Kepala Desa -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Upload SK Kepala Desa / Surat Keputusan Pengangkatan <span class="text-danger">*</span></label>
                                <div class="upload-dropzone" id="dropzoneBox" onclick="document.getElementById('sk_kades_input').click()">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-1 d-block mb-1"></i>
                                    <div class="fw-semibold text-dark mb-1" id="file_label_text">Klik untuk memilih berkas SK Kepala Desa</div>
                                    <div class="small text-muted" id="file_help_text">Format berkas: <strong>PDF, JPG, JPEG, atau PNG</strong> (Maksimal ukuran: <strong>5 MB</strong>)</div>
                                </div>
                                <input type="file" name="sk_kades" id="sk_kades_input" class="d-none" accept=".pdf,.jpg,.jpeg,.png" required onchange="handleFileSelected(this)">
                                <div id="selected_file_info" class="mt-2 text-success small fw-semibold d-none">
                                    <i class="bi bi-check-circle-fill me-1"></i> File terpilih: <span id="selected_file_name"></span>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Alamat Email Dinas / Resmi <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="kades@desa.id" required value="<?= old('email') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-medium">Password Akun <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                                </div>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    Saya menyatakan bahwa saya adalah Kepala Desa / Pejabat Desa yang berwenang, dan seluruh data serta dokumen SK yang diunggah adalah sah dan benar.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Daftarkan Desa & Ajukan Verifikasi
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center py-3 border-0">
                        <div class="small text-muted mb-1">
                            Ingin mendaftar sebagai warga biasa? <a href="/auth/register" class="text-decoration-none fw-semibold">Daftar Akun Warga</a>
                        </div>
                        <div class="small text-muted">
                            Sudah punya akun? <a href="/auth/login" class="text-decoration-none fw-semibold">Login di sini</a>
                        </div>
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
        function handleFileSelected(input) {
            const file = input.files[0];
            const infoDiv = document.getElementById('selected_file_info');
            const nameSpan = document.getElementById('selected_file_name');
            const labelText = document.getElementById('file_label_text');

            if (file) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                nameSpan.textContent = file.name + ' (' + fileSizeMB + ' MB)';
                infoDiv.classList.remove('d-none');
                labelText.textContent = 'Ganti berkas SK Kepala Desa';
            } else {
                infoDiv.classList.add('d-none');
                labelText.textContent = 'Klik untuk memilih berkas SK Kepala Desa';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Drag and drop dropzone enhancement
            const dropzone = document.getElementById('dropzoneBox');
            const fileInput = document.getElementById('sk_kades_input');

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelected(fileInput);
                }
            }, false);

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
