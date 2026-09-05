<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Warga - SiPelayan Desa' ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?= $this->renderSection('styles') ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        .nav-link {
            color: #495057;
            font-weight: 500;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.2rem;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #e9ecef;
            color: #0d6efd;
        }
        .nav-link i {
            margin-right: 10px;
        }
        .content-area {
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/warga/dashboard">
                <i class="bi bi-shield-check me-2"></i>SiPelayan-<?= sebutan_desa() ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-3">
                        <span class="text-muted"><i class="bi bi-bell"></i></span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('nama_lengkap') ?? 'User') ?>&background=0d6efd&color=fff" alt="User" class="rounded-circle me-2" width="32">
                            <div class="text-start d-none d-sm-inline-block">
                                <span class="d-block fw-semibold text-dark lh-1"><?= esc(session('nama_lengkap') ?? 'Warga') ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= esc(session('role_name') ?? 'Warga') ?> <?= session('nama_desa') ? '· ' . esc(session('nama_desa')) : '' ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text text-muted small"><?= esc(session('email')) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
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
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == site_url('warga/dashboard')) ? 'active' : '' ?>" href="/warga/dashboard">
                                <i class="bi bi-grid-1x2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'warga/permohonan') !== false) ? 'active' : '' ?>" href="/warga/permohonan">
                                <i class="bi bi-envelope-paper"></i> Permohonan Surat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'warga/pengaduan') !== false) ? 'active' : '' ?>" href="<?= base_url('warga/pengaduan') ?>">
                                <i class="bi bi-megaphone"></i> Pengaduan & Aspirasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'warga/pengumuman') !== false) ? 'active' : '' ?>" href="<?= base_url('warga/pengumuman') ?>">
                                <i class="bi bi-bell-fill"></i> Pengumuman <?= sebutan_desa() ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'warga/transparansi') !== false) ? 'active' : '' ?>" href="<?= base_url('warga/transparansi') ?>">
                                <i class="bi bi-bank"></i> Transparansi <?= is_aceh() ? 'APBG' : 'APBDes' ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalTanyaKades">
                                <i class="bi bi-chat-dots"></i> Tanya <?= sebutan_kades() ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 content-area">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Modal Tanya Kades / Keuchik -->
    <?= $this->include('warga/partials/modal_tanya_kades') ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
