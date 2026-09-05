<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel - SiPelayan Desa' ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #ffffff;
            border-right: 1px solid #e9ecef;
        }
        .nav-link {
            color: #4b5563;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }
        .nav-link i {
            margin-right: 10px;
        }
        .content-area {
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-card {
            border-left: 4px solid #4f46e5;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i>SiPelayan-<?= sebutan_desa() ?> <span class="badge bg-dark ms-1" style="font-size: 0.7rem;"><?= esc(session('role_name') ?? 'Panel') ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-3 text-muted small">
                        <i class="bi bi-geo-alt me-1 text-primary"></i><?= esc(session('nama_desa') ?? 'Pusat') ?>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('nama_lengkap') ?? 'Admin') ?>&background=4f46e5&color=fff" alt="User" class="rounded-circle me-2" width="32">
                            <div class="text-start d-none d-sm-inline-block">
                                <span class="d-block fw-semibold text-dark lh-1"><?= esc(session('nama_lengkap')) ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= esc(session('email')) ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
                <div class="p-3">
                    <ul class="nav flex-column">
                        <?php 
                            $role = session('role_slug'); 
                            $uri = uri_string();
                        ?>
                        <?php if ($role === 'super_admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/dashboard') !== false || $uri === 'super-admin') ? 'active' : '' ?>" href="<?= base_url('super-admin/dashboard') ?>">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>

                            <li class="nav-header text-uppercase text-muted fw-bold px-3 mt-3 mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Master Wilayah</li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/provinsi') !== false) ? 'active' : '' ?>" href="<?= base_url('super-admin/provinsi') ?>">
                                    <i class="bi bi-globe-americas"></i> Master Provinsi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/kabupaten') !== false) ? 'active' : '' ?>" href="<?= base_url('super-admin/kabupaten') ?>">
                                    <i class="bi bi-building"></i> Master Kabupaten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/kecamatan') !== false) ? 'active' : '' ?>" href="<?= base_url('super-admin/kecamatan') ?>">
                                    <i class="bi bi-geo-alt"></i> Master Kecamatan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/desa') !== false) ? 'active' : '' ?>" href="<?= base_url('super-admin/desa') ?>">
                                    <i class="bi bi-houses"></i> Master Desa
                                </a>
                            </li>

                            <li class="nav-header text-uppercase text-muted fw-bold px-3 mt-3 mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Sistem</li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'super-admin/users') !== false) ? 'active' : '' ?>" href="<?= base_url('super-admin/users') ?>">
                                    <i class="bi bi-people"></i> Manajemen User
                                </a>
                            </li>
                        <?php elseif ($role === 'admin_desa'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/dashboard') !== false || $uri === 'admin-desa') ? 'active' : '' ?>" href="<?= base_url('admin-desa/dashboard') ?>">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/permohonan') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/permohonan') ?>">
                                    <i class="bi bi-check2-square"></i> Approval Surat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/kependudukan') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/kependudukan') ?>">
                                    <i class="bi bi-people"></i> Kependudukan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/pekerjaan') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/pekerjaan') ?>">
                                    <i class="bi bi-briefcase"></i> Pekerjaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/pengaduan') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/pengaduan') ?>">
                                    <i class="bi bi-megaphone"></i> Pengaduan Warga
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/operator') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/operator') ?>">
                                    <i class="bi bi-person-badge"></i> Operator <?= sebutan_desa() ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'pengumuman') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/pengumuman') ?>">
                                    <i class="bi bi-megaphone-fill"></i> Pengumuman <?= sebutan_desa() ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'transparansi') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/transparansi') ?>">
                                    <i class="bi bi-cash-coin"></i> Transparansi <?= is_aceh() ? 'APBG' : 'APBDes' ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'admin-desa/profil') !== false) ? 'active' : '' ?>" href="<?= base_url('admin-desa/profil') ?>">
                                    <i class="bi bi-building-gear"></i> Profil & Logo <?= sebutan_desa() ?>
                                </a>
                            </li>
                        <?php elseif ($role === 'operator'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/dashboard') !== false || $uri === 'operator') ? 'active' : '' ?>" href="<?= base_url('operator/dashboard') ?>">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/permohonan') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/permohonan') ?>">
                                    <i class="bi bi-envelope-paper"></i> Verifikasi Surat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/pengumuman') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/pengumuman') ?>">
                                    <i class="bi bi-megaphone-fill"></i> Pengumuman <?= sebutan_desa() ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/transparansi') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/transparansi') ?>">
                                    <i class="bi bi-cash-coin"></i> Transparansi <?= is_aceh() ? 'APBG' : 'APBDes' ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/pengaduan') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/pengaduan') ?>">
                                    <i class="bi bi-megaphone"></i> Pengaduan Warga
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/penduduk') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/penduduk') ?>">
                                    <i class="bi bi-person-lines-fill"></i> Data Penduduk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($uri, 'operator/pekerjaan') !== false) ? 'active' : '' ?>" href="<?= base_url('operator/pekerjaan') ?>">
                                    <i class="bi bi-briefcase"></i> Pekerjaan
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 content-area">
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show p-3 mb-4">
                        <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
