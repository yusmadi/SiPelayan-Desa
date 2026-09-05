<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - SiPelayan Desa' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .auth-card { 
            border-radius: 18px; 
            border: none; 
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08), 0 5px 15px rgba(15, 23, 42, 0.04);
            background: #ffffff;
            overflow: hidden;
        }
        .nav-segment {
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            display: flex;
            gap: 4px;
        }
        .nav-segment .btn-segment {
            flex: 1;
            border: none;
            background: transparent;
            padding: 8px 12px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
            border-radius: 9px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
        }
        .nav-segment .btn-segment.active {
            background-color: #ffffff;
            color: #0d6efd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .nav-segment .btn-segment.active.whatsapp-active {
            color: #198754;
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #dee2e6;
            color: #64748b;
        }
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .btn-toggle-password {
            cursor: pointer;
            border-left: none;
            background-color: #f8fafc;
        }
        .btn-toggle-password:hover {
            color: #0d6efd;
        }
        .login-btn {
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 col-xl-4">
                
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <a href="/" class="text-decoration-none d-inline-flex align-items-center gap-2">
                        <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <span class="h3 fw-bold text-primary mb-0">SiPelayan-Desa</span>
                    </a>
                    <p class="text-muted small mt-2 mb-0">Sistem Pelayanan Administrasi Desa Terpadu</p>
                </div>

                <!-- Login Card -->
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-4">
                        <h4 class="fw-bold text-dark mb-1">Masuk Akun</h4>
                        <p class="text-muted small mb-4">Pilih metode login dan masukkan kredensial Anda.</p>

                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-3 border-0 bg-danger-subtle text-danger" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
                                <div class="small fw-medium">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(session()->getFlashdata('success')): ?>
                            <div class="alert alert-success d-flex align-items-center p-3 mb-4 rounded-3 border-0 bg-success-subtle text-success" role="alert">
                                <i class="bi bi-check-circle-fill fs-5 me-2 flex-shrink-0"></i>
                                <div class="small fw-medium">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Segmented Switcher for Login Method -->
                        <label class="form-label fw-semibold text-secondary small mb-2">Metode Login</label>
                        <div class="nav-segment mb-3" role="tablist">
                            <button type="button" class="btn-segment active" id="tabEmail" onclick="setLoginMethod('email')">
                                <i class="bi bi-envelope-at"></i> Email
                            </button>
                            <button type="button" class="btn-segment" id="tabWhatsapp" onclick="setLoginMethod('whatsapp')">
                                <i class="bi bi-whatsapp text-success"></i> WhatsApp
                            </button>
                        </div>

                        <?php
                            $oldIdentity = old('identity') ?? old('email') ?? old('whatsapp') ?? '';
                            // Cek apakah input sebelumnya mirip nomor telepon (hanya digit atau awalan +, 08, 62)
                            $isPhone = !empty($oldIdentity) && preg_match('/^(\+?62|08|\d{8,})/', trim($oldIdentity));
                        ?>

                        <form action="/auth/login" method="post" id="loginForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="login_method" id="loginMethodInput" value="<?= $isPhone ? 'whatsapp' : 'email' ?>">

                            <!-- Identity Field (Email / WhatsApp) -->
                            <div class="mb-3">
                                <label class="form-label fw-medium small" id="identityLabel">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="identityIconWrapper">
                                        <i class="bi bi-envelope" id="identityIcon"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="identity" 
                                        id="identityInput" 
                                        class="form-control" 
                                        placeholder="nama@email.com" 
                                        value="<?= esc($oldIdentity) ?>" 
                                        required 
                                        autocomplete="username"
                                    >
                                </div>
                                <div class="form-text text-muted small" id="identityHelper" style="font-size: 0.775rem;">
                                    Gunakan alamat email yang terdaftar.
                                </div>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-medium small mb-0">Password</label>
                                    <a href="#" class="text-decoration-none small text-primary" tabindex="-1">Lupa password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="passwordInput" 
                                        class="form-control" 
                                        placeholder="••••••••" 
                                        required 
                                        autocomplete="current-password"
                                    >
                                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" title="Tampilkan/Sembunyikan Password">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Remember Me -->
                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                                <label class="form-check-label text-muted small" for="rememberMe">Ingat saya di perangkat ini</label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 login-btn mb-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                            </button>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3 border-0">
                        <div class="small text-muted mb-2">
                            Belum punya akun warga? <a href="/auth/register" class="text-decoration-none fw-semibold text-primary">Daftar sekarang</a>
                        </div>
                        <div class="pt-2 border-top small text-muted">
                            Kepala Desa / Admin Desa? <a href="/auth/register-desa" class="text-decoration-none fw-bold text-success"><i class="bi bi-building-check me-1"></i>Daftarkan Desa</a>
                        </div>
                    </div>
                </div>

                <!-- Footer info -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> SiPelayan-Desa. Hak Cipta Dilindungi.</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function setLoginMethod(method) {
            const tabEmail = document.getElementById('tabEmail');
            const tabWhatsapp = document.getElementById('tabWhatsapp');
            const methodInput = document.getElementById('loginMethodInput');
            const identityLabel = document.getElementById('identityLabel');
            const identityInput = document.getElementById('identityInput');
            const identityIcon = document.getElementById('identityIcon');
            const identityHelper = document.getElementById('identityHelper');

            if (method === 'whatsapp') {
                tabEmail.classList.remove('active');
                tabWhatsapp.classList.add('active', 'whatsapp-active');
                methodInput.value = 'whatsapp';

                identityLabel.textContent = 'Nomor WhatsApp';
                identityInput.type = 'tel';
                identityInput.placeholder = 'Contoh: 081234567890 atau 62812...';
                identityIcon.className = 'bi bi-whatsapp text-success';
                identityHelper.innerHTML = 'Masukkan nomor WhatsApp aktif yang terdaftar (format: <span class="fw-semibold">08...</span> atau <span class="fw-semibold">628...</span>).';
            } else {
                tabWhatsapp.classList.remove('active', 'whatsapp-active');
                tabEmail.classList.add('active');
                methodInput.value = 'email';

                identityLabel.textContent = 'Alamat Email';
                identityInput.type = 'email';
                identityInput.placeholder = 'nama@email.com';
                identityIcon.className = 'bi bi-envelope';
                identityHelper.textContent = 'Gunakan alamat email yang terdaftar pada akun Anda.';
            }
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordIcon.classList.remove('bi-eye');
                togglePasswordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                togglePasswordIcon.classList.remove('bi-eye-slash');
                togglePasswordIcon.classList.add('bi-eye');
            }
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const initialMethod = '<?= $isPhone ? 'whatsapp' : 'email' ?>';
            setLoginMethod(initialMethod);
        });
    </script>
</body>
</html>
