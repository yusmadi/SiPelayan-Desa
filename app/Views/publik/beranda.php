<?php
// Ambil daftar gambar hero dari folder public/myassets/img
$imgDir = FCPATH . 'myassets/img/';
$sliderImages = [];
if (is_dir($imgDir)) {
    $files = scandir($imgDir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && strpos(strtolower($file), 'icon') === false && strpos(strtolower($file), 'sipelayan') === false) {
            $sliderImages[] = $file;
        }
    }
}
// Fallback jika folder kosong atau tidak terbaca
if (empty($sliderImages)) {
    $sliderImages = ['tugu-lobster-simeulue.jpg', 'tugu-mulieng-sigli.jpg', 'tugu-sp5-bna.jpg'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipelayan Desa</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('myassets/img/sipelayan-desa.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts (Inter sebagai default, mirip dengan desain) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #EFEFEF; /* Warna latar belakang abu-abu terang */
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Styling Navbar */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .navbar-brand {
            font-weight: 700;
            color: #1a73e8; /* Warna biru sesuai teks logo */
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i {
            color: #6c757d;
        }

        .btn-login {
            background-color: #1a73e8;
            color: white;
            border-radius: 20px;
            padding: 8px 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .btn-login:hover {
            background-color: #1557b0;
            color: white;
        }

        /* Styling Konten Utama */
        .main-content {
            flex-grow: 1;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .page-title {
            text-align: center;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            font-size: 1.5rem;
            line-height: 1.4;
        }

        /* Styling Card Utama */
        .service-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin: 0 auto;
            max-width: 900px; /* Membatasi lebar card agar tidak terlalu lebar di desktop */
        }

        .service-card-img {
            width: 100%;
            height: 350px;
            object-fit: cover; /* Memastikan gambar memenuhi area tanpa merusak rasio */
        }

        /* Carousel Customization */
        #heroCarousel .carousel-control-prev,
        #heroCarousel .carousel-control-next {
            width: 42px;
            height: 42px;
            background: rgba(0, 0, 0, 0.35);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        #heroCarousel .carousel-control-prev:hover,
        #heroCarousel .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        #heroCarousel .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 4px;
            background-color: #ffffff;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        #heroCarousel .carousel-indicators .active {
            opacity: 1;
            width: 24px;
            border-radius: 10px;
            background-color: #1a73e8;
        }

        .service-card-body {
            padding: 40px 20px;
            text-align: center;
        }

        .service-subtitle {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 20px;
        }

        /* Garis bawah biru pendek */
        .service-divider {
            width: 50px;
            height: 2px;
            background-color: #1a73e8;
            margin: 0 auto 40px auto;
        }

        /* Styling Ikon Layanan */
        .service-icons-wrapper {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .icon-item:hover {
            transform: translateY(-5px);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .icon-text {
            font-weight: 500;
            font-size: 0.95rem;
            color: #495057;
        }

        /* Warna-warni untuk lingkaran ikon */
        .bg-icon-green { background-color: #e6f4ea; color: #1e8e3e; }
        .bg-icon-blue { background-color: #e8f0fe; color: #1a73e8; }
        .bg-icon-purple { background-color: #f3e8fd; color: #9334e6; }
        .bg-icon-teal { background-color: #e0f2f1; color: #00897b; }

        /* Styling Footer */
        .footer {
            background-color: #ffffff;
            padding: 15px 0;
            border-top: 1px solid #dee2e6;
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Responsivitas Tambahan */
        @media (max-width: 768px) {
            .service-card-img {
                height: 200px;
            }
            .page-title {
                font-size: 1.2rem;
                padding: 0 15px;
            }
            .service-icons-wrapper {
                gap: 20px;
            }
            .icon-circle {
                width: 60px;
                height: 60px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header/Navbar Section -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4 px-lg-5">
            <!-- Logo dan Judul -->
            <a class="navbar-brand text-uppercase" href="<?= base_url() ?>">
                <img src="<?= base_url('myassets/img/sipelayan-desa.ico') ?>" alt="SIPELAYAN-DESA" width="28" height="28" class="d-inline-block align-text-top me-2">
                SIPELAYAN-DESA
            </a>
            
            <!-- Tombol Login / Dashboard (Di kanan pada layar besar) -->
            <div class="d-flex ms-auto">
                <?php if (session()->get('is_logged_in')): ?>
                    <?php 
                        $role = session()->get('role_slug');
                        $dashUrl = match($role) {
                            'super_admin' => base_url('super-admin/dashboard'),
                            'admin_desa'  => base_url('admin-desa/dashboard'),
                            'operator'    => base_url('operator/dashboard'),
                            default       => base_url('warga/dashboard'),
                        };
                    ?>
                    <a href="<?= $dashUrl ?>" class="btn btn-login" id="loginBtn">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-login" id="loginBtn">
                        <i class="fa-solid fa-user-circle"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Section -->
    <main class="main-content container">
        
        <!-- Judul Halaman -->
        <h1 class="page-title">
            SELAMAT DATANG DI SISTEM INFORMASI PELAYANAN DESA<br>
            (SIPELAYAN-DESA)
        </h1>

        <!-- Card Utama -->
        <div class="service-card">
            <!-- Carousel Hero -->
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                <?php if (count($sliderImages) > 1): ?>
                <div class="carousel-indicators">
                    <?php foreach ($sliderImages as $index => $img): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="carousel-inner">
                    <?php foreach ($sliderImages as $index => $img): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= base_url('myassets/img/' . $img) ?>" class="d-block w-100 service-card-img" alt="Monumen <?= esc($img) ?>" onerror="this.onerror=null; this.src='https://placehold.co/900x400/1a73e8/ffffff?text=SIPELAYAN+DESA';">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($sliderImages) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Selanjutnya</span>
                </button>
                <?php endif; ?>
            </div>
            
            <div class="service-card-body">
                <p class="service-subtitle">Silakan Pilih Layanan Yang Ingin Digunakan.</p>
                <div class="service-divider"></div>

                <!-- Ikon Layanan -->
                <div class="service-icons-wrapper">
                    
                    <!-- Item Penduduk -->
                    <div class="icon-item" data-service="Penduduk">
                        <div class="icon-circle bg-icon-green">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="icon-text">Penduduk</span>
                    </div>

                    <!-- Item E-Surat -->
                    <div class="icon-item" data-service="E-Surat">
                        <div class="icon-circle bg-icon-blue">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <span class="icon-text">E-Surat</span>
                    </div>

                    <!-- Item APBDes -->
                    <div class="icon-item" data-service="APBDes">
                        <div class="icon-circle bg-icon-purple">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <span class="icon-text">APBDes</span>
                    </div>

                    <!-- Item Tanya Kades -->
                    <div class="icon-item" data-service="Tanya Kades">
                        <div class="icon-circle bg-icon-teal">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <span class="icon-text">Tanya Kades</span>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="footer mt-auto">
        <div class="container-fluid px-4 px-lg-5 d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-md-0">
                &copy; <?= date('Y') ?> Copyright by <strong class="text-dark">YMDSOFT.COM</strong>
            </div>
            <div>
                V.1.0.0
            </div>
        </div>
    </footer>

    <!-- jQuery dan Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JavaScript Kustom (Interaktivitas sederhana) -->
    <script>
        $(document).ready(function() {
            // Variabel session login dari PHP
            const isLoggedIn = <?= session()->get('is_logged_in') ? 'true' : 'false' ?>;
            const userRole = '<?= session()->get('role_slug') ?? '' ?>';
            
            // Efek hover sederhana pada tombol login menggunakan jQuery
            $('#loginBtn').hover(
                function() { $(this).css('box-shadow', '0 4px 8px rgba(26, 115, 232, 0.4)'); },
                function() { $(this).css('box-shadow', 'none'); }
            );

            // Interaktivitas saat mengklik ikon layanan
            $('.icon-item').click(function() {
                const serviceName = $(this).data('service');
                
                // Menambahkan efek visual klik (sedikit mengecil)
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                }, 150);

                if (['Penduduk', 'E-Surat', 'Tanya Kades'].includes(serviceName)) {
                    if (isLoggedIn) {
                        let targetUrl = '<?= base_url() ?>';
                        if (serviceName === 'Penduduk') {
                            if (userRole === 'admin_desa') targetUrl += 'admin-desa/kependudukan';
                            else if (userRole === 'operator') targetUrl += 'operator/penduduk';
                            else if (userRole === 'super_admin') targetUrl += 'super-admin/dashboard';
                            else targetUrl += 'warga/dashboard';
                        } else if (serviceName === 'E-Surat') {
                            if (userRole === 'admin_desa') targetUrl += 'admin-desa/permohonan';
                            else if (userRole === 'operator') targetUrl += 'operator/permohonan';
                            else if (userRole === 'super_admin') targetUrl += 'super-admin/dashboard';
                            else targetUrl += 'warga/permohonan';
                        } else if (serviceName === 'Tanya Kades') {
                            if (userRole === 'admin_desa') targetUrl += 'admin-desa/pengaduan';
                            else if (userRole === 'operator') targetUrl += 'operator/pengaduan';
                            else if (userRole === 'super_admin') targetUrl += 'super-admin/dashboard';
                            else targetUrl += 'warga/pengaduan';
                        }
                        window.location.href = targetUrl;
                    } else {
                        // Jika belum login, tampilkan SweetAlert2
                        Swal.fire({
                            title: 'Anda Belum Login',
                            text: `Silakan login terlebih dahulu untuk mengakses layanan ${serviceName}.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1a73e8',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Login',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= base_url('auth/login') ?>';
                            }
                        });
                    }
                } else {
                    // Membuat toast informasi untuk menu lain
                    const toastHTML = `
                    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
                        <div id="serviceToast" class="toast align-items-center border-0 bg-dark text-white" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                            <div class="d-flex">
                                <div class="toast-body">
                                    Membuka modul: <strong>${serviceName}</strong> (Fitur ini sedang dalam pengembangan)
                                </div>
                            </div>
                        </div>
                    </div>`;

                    // Hapus toast lama jika ada
                    $('.toast-container').remove();
                    
                    $('body').append(toastHTML);
                    const toastElement = document.getElementById('serviceToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                }
            });
        });
    </script>
</body>
</html>
