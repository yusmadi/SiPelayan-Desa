<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// ─────────────────────────────────────────────────────────
// PUBLIC ROUTES (tanpa auth)
// ─────────────────────────────────────────────────────────
$routes->get('/', 'Publik\BerandaController::index');
$routes->get('(:segment)', 'Publik\BerandaController::desa/$1');  // Slug desa
$routes->get('(:segment)/informasi', 'Publik\InformasiController::index/$1');
$routes->get('(:segment)/informasi/(:segment)', 'Publik\InformasiController::detail/$1/$2');

// Auth
$routes->group('auth', static function ($routes) {
    $routes->get('login',    'Auth\LoginController::index');
    $routes->post('login',   'Auth\LoginController::proses', ['filter' => 'ratelimit:10:60']); // Max 10x POST login / menit
    $routes->get('register',      'Auth\RegisterController::index');
    $routes->get('register-desa', 'Auth\RegisterController::registerDesa');
    $routes->get('search-desa',   'Auth\RegisterController::searchDesa');
    $routes->post('register',     'Auth\RegisterController::proses', ['filter' => 'ratelimit:5:60']); // Max 5x register / menit
    $routes->post('register-desa','Auth\RegisterController::prosesDesa', ['filter' => 'ratelimit:5:60']); // Max 5x register / menit
    $routes->get('logout',   'Auth\LoginController::logout');
});

// ─────────────────────────────────────────────────────────
// SUPER ADMIN ROUTES
// ─────────────────────────────────────────────────────────
$routes->group('super-admin', ['filter' => ['auth', 'role:super_admin']], static function ($routes) {
    $routes->get('dashboard',          'SuperAdmin\DashboardController::index');
    
    // Master Provinsi
    $routes->get('provinsi',               'SuperAdmin\ProvinsiController::index');
    $routes->get('provinsi/tambah',        'SuperAdmin\ProvinsiController::create');
    $routes->post('provinsi/tambah',       'SuperAdmin\ProvinsiController::store');
    $routes->get('provinsi/edit/(:num)',   'SuperAdmin\ProvinsiController::edit/$1');
    $routes->post('provinsi/edit/(:num)',  'SuperAdmin\ProvinsiController::update/$1');
    $routes->post('provinsi/toggle/(:num)','SuperAdmin\ProvinsiController::toggleStatus/$1');
    $routes->post('provinsi/delete/(:num)','SuperAdmin\ProvinsiController::delete/$1');

    // Master Kabupaten
    $routes->get('kabupaten',               'SuperAdmin\KabupatenController::index');
    $routes->get('kabupaten/tambah',        'SuperAdmin\KabupatenController::create');
    $routes->post('kabupaten/tambah',       'SuperAdmin\KabupatenController::store');
    $routes->get('kabupaten/edit/(:num)',   'SuperAdmin\KabupatenController::edit/$1');
    $routes->post('kabupaten/edit/(:num)',  'SuperAdmin\KabupatenController::update/$1');
    $routes->post('kabupaten/toggle/(:num)','SuperAdmin\KabupatenController::toggleStatus/$1');
    $routes->post('kabupaten/delete/(:num)','SuperAdmin\KabupatenController::delete/$1');

    // Master Kecamatan
    $routes->get('kecamatan',               'SuperAdmin\KecamatanController::index');
    $routes->get('kecamatan/tambah',        'SuperAdmin\KecamatanController::create');
    $routes->post('kecamatan/tambah',       'SuperAdmin\KecamatanController::store');
    $routes->get('kecamatan/edit/(:num)',   'SuperAdmin\KecamatanController::edit/$1');
    $routes->post('kecamatan/edit/(:num)',  'SuperAdmin\KecamatanController::update/$1');
    $routes->post('kecamatan/toggle/(:num)','SuperAdmin\KecamatanController::toggleStatus/$1');
    $routes->post('kecamatan/delete/(:num)','SuperAdmin\KecamatanController::delete/$1');

    // Master Desa
    $routes->get('desa',               'SuperAdmin\DesaController::index');
    $routes->get('desa/tambah',        'SuperAdmin\DesaController::create');
    $routes->post('desa/tambah',       'SuperAdmin\DesaController::store');
    $routes->get('desa/edit/(:num)',   'SuperAdmin\DesaController::edit/$1');
    $routes->post('desa/edit/(:num)',  'SuperAdmin\DesaController::update/$1');
    $routes->post('desa/toggle/(:num)','SuperAdmin\DesaController::toggleStatus/$1');
    $routes->post('desa/delete/(:num)','SuperAdmin\DesaController::delete/$1');

    // Manajemen Pengguna (Users)
    $routes->get('users',               'SuperAdmin\UserController::index');
    $routes->get('users/search-desa',   'SuperAdmin\UserController::searchDesa');
    $routes->get('users/tambah',        'SuperAdmin\UserController::create');
    $routes->post('users/tambah',       'SuperAdmin\UserController::store');
    $routes->get('users/edit/(:num)',   'SuperAdmin\UserController::edit/$1');
    $routes->post('users/edit/(:num)',  'SuperAdmin\UserController::update/$1');
    $routes->post('users/toggle/(:num)','SuperAdmin\UserController::toggleStatus/$1');
    $routes->post('users/delete/(:num)','SuperAdmin\UserController::delete/$1');
});

// ─────────────────────────────────────────────────────────
// ADMIN DESA ROUTES
// ─────────────────────────────────────────────────────────
$routes->group('admin-desa', ['filter' => ['auth', 'role:admin_desa,super_admin', 'tenant']], static function ($routes) {
    $routes->get('dashboard',                  'AdminDesa\DashboardController::index');
    // Permohonan surat — approval
    $routes->get('permohonan',                 'AdminDesa\PermohonanController::index');
    $routes->post('permohonan/approve/(:num)', 'AdminDesa\PermohonanController::approve/$1');
    $routes->post('permohonan/reject/(:num)',  'AdminDesa\PermohonanController::reject/$1');
    $routes->get('permohonan/generate/(:num)', 'AdminDesa\PermohonanController::generate/$1');
    $routes->post('permohonan/update-nomor/(:num)', 'AdminDesa\PermohonanController::updateNomorSurat/$1');
    $routes->post('permohonan/update-surat/(:num)', 'AdminDesa\PermohonanController::updateSurat/$1');
    $routes->post('permohonan/toggle-signed/(:num)', 'AdminDesa\PermohonanController::toggleSigned/$1');

    // Kependudukan
    $routes->get('kependudukan',                  'AdminDesa\KependudukanController::index');
    $routes->get('kependudukan/tambah',           'AdminDesa\KependudukanController::create');
    $routes->post('kependudukan/tambah',          'AdminDesa\KependudukanController::store');
    $routes->get('kependudukan/import',           'AdminDesa\KependudukanController::importForm');
    $routes->post('kependudukan/import',          'AdminDesa\KependudukanController::processImport');
    $routes->get('kependudukan/template-import',  'AdminDesa\KependudukanController::downloadTemplate');
    $routes->get('kependudukan/detail/(:num)',    'AdminDesa\KependudukanController::detail/$1');
    $routes->get('kependudukan/edit/(:num)',      'AdminDesa\KependudukanController::edit/$1');
    $routes->post('kependudukan/edit/(:num)',     'AdminDesa\KependudukanController::update/$1');
    $routes->post('kependudukan/delete/(:num)',   'AdminDesa\KependudukanController::delete/$1');

    // Data Pekerjaan
    $routes->get('pekerjaan',                     'AdminDesa\PekerjaanController::index');
    $routes->post('pekerjaan/tambah',             'AdminDesa\PekerjaanController::store');
    $routes->post('pekerjaan/edit/(:num)',        'AdminDesa\PekerjaanController::update/$1');
    $routes->post('pekerjaan/delete/(:num)',      'AdminDesa\PekerjaanController::delete/$1');

    // Pengaduan & Aspirasi Warga
    $routes->get('pengaduan',                     'AdminDesa\PengaduanController::index');
    $routes->get('pengaduan/(:num)',              'AdminDesa\PengaduanController::detail/$1');
    $routes->post('pengaduan/(:num)/tanggapi',    'AdminDesa\PengaduanController::tanggapi/$1');

    // Profil & Pengaturan Desa (Logo, Kontak, Kades)
    $routes->get('profil',                        'AdminDesa\ProfilDesaController::index');
    $routes->post('profil',                       'AdminDesa\ProfilDesaController::update');

    // Manajemen Operator Desa
    $routes->get('operator',                      'AdminDesa\OperatorController::index');
    $routes->get('operator/tambah',               'AdminDesa\OperatorController::create');
    $routes->post('operator/tambah',              'AdminDesa\OperatorController::store');
    $routes->get('operator/edit/(:num)',          'AdminDesa\OperatorController::edit/$1');
    $routes->post('operator/edit/(:num)',         'AdminDesa\OperatorController::update/$1');
    $routes->post('operator/toggle/(:num)',       'AdminDesa\OperatorController::toggleStatus/$1');
    $routes->post('operator/delete/(:num)',       'AdminDesa\OperatorController::delete/$1');

    // Pengumuman Desa
    $routes->get('pengumuman',                    'Operator\PengumumanController::index');
    $routes->get('pengumuman/tambah',             'Operator\PengumumanController::create');
    $routes->post('pengumuman/simpan',            'Operator\PengumumanController::store');
    $routes->get('pengumuman/edit/(:num)',        'Operator\PengumumanController::edit/$1');
    $routes->post('pengumuman/update/(:num)',     'Operator\PengumumanController::update/$1');
    $routes->post('pengumuman/toggle/(:num)',     'Operator\PengumumanController::togglePublish/$1');
    $routes->post('pengumuman/delete/(:num)',     'Operator\PengumumanController::delete/$1');
    $routes->get('pengumuman/detail/(:num)',      'Operator\PengumumanController::detail/$1');

    // Transparansi Anggaran (APBDes / APBG)
    $routes->get('transparansi',                  'Operator\TransparansiController::index');
    $routes->get('transparansi/tambah',           'Operator\TransparansiController::create');
    $routes->post('transparansi/simpan',          'Operator\TransparansiController::store');
    $routes->get('transparansi/edit/(:num)',      'Operator\TransparansiController::edit/$1');
    $routes->post('transparansi/update/(:num)',   'Operator\TransparansiController::update/$1');
    $routes->post('transparansi/toggle/(:num)',   'Operator\TransparansiController::togglePublish/$1');
    $routes->post('transparansi/delete/(:num)',   'Operator\TransparansiController::delete/$1');
    $routes->get('transparansi/detail/(:num)',    'Operator\TransparansiController::detail/$1');
});

// ─────────────────────────────────────────────────────────
// OPERATOR DESA ROUTES
// ─────────────────────────────────────────────────────────
$routes->group('operator', ['filter' => ['auth', 'role:operator,admin_desa,super_admin', 'tenant']], static function ($routes) {
    $routes->get('dashboard',                    'Operator\DashboardController::index');
    $routes->get('permohonan',                   'Operator\PermohonanController::index');
    $routes->post('permohonan/verify/(:num)',    'Operator\PermohonanController::verify/$1');
    $routes->get('permohonan/generate/(:num)',   'Operator\PermohonanController::generate/$1');
    $routes->post('permohonan/update-nomor/(:num)', 'Operator\PermohonanController::updateNomorSurat/$1');
    $routes->post('permohonan/update-surat/(:num)', 'Operator\PermohonanController::updateSurat/$1');
    $routes->post('permohonan/toggle-signed/(:num)', 'Operator\PermohonanController::toggleSigned/$1');

    // Data Penduduk
    $routes->get('penduduk',                     'AdminDesa\KependudukanController::index');
    $routes->get('penduduk/tambah',              'AdminDesa\KependudukanController::create');
    $routes->post('penduduk/tambah',             'AdminDesa\KependudukanController::store');
    $routes->get('penduduk/import',              'AdminDesa\KependudukanController::importForm');
    $routes->post('penduduk/import',             'AdminDesa\KependudukanController::processImport');
    $routes->get('penduduk/template-import',     'AdminDesa\KependudukanController::downloadTemplate');
    $routes->get('penduduk/detail/(:num)',       'AdminDesa\KependudukanController::detail/$1');
    $routes->get('penduduk/edit/(:num)',         'AdminDesa\KependudukanController::edit/$1');
    $routes->post('penduduk/edit/(:num)',        'AdminDesa\KependudukanController::update/$1');
    $routes->post('penduduk/delete/(:num)',      'AdminDesa\KependudukanController::delete/$1');

    // Data Pekerjaan
    $routes->get('pekerjaan',                     'AdminDesa\PekerjaanController::index');
    $routes->post('pekerjaan/tambah',             'AdminDesa\PekerjaanController::store');
    $routes->post('pekerjaan/edit/(:num)',        'AdminDesa\PekerjaanController::update/$1');
    $routes->post('pekerjaan/delete/(:num)',      'AdminDesa\PekerjaanController::delete/$1');

    // Pengaduan & Aspirasi Warga
    $routes->get('pengaduan',                     'AdminDesa\PengaduanController::index');
    $routes->get('pengaduan/(:num)',              'AdminDesa\PengaduanController::detail/$1');
    $routes->post('pengaduan/(:num)/tanggapi',    'AdminDesa\PengaduanController::tanggapi/$1');

    // Pengumuman Desa (CRUD Operator)
    $routes->get('pengumuman',                    'Operator\PengumumanController::index');
    $routes->get('pengumuman/tambah',             'Operator\PengumumanController::create');
    $routes->post('pengumuman/simpan',            'Operator\PengumumanController::store');
    $routes->get('pengumuman/edit/(:num)',        'Operator\PengumumanController::edit/$1');
    $routes->post('pengumuman/update/(:num)',     'Operator\PengumumanController::update/$1');
    $routes->post('pengumuman/toggle/(:num)',     'Operator\PengumumanController::togglePublish/$1');
    $routes->post('pengumuman/delete/(:num)',     'Operator\PengumumanController::delete/$1');
    $routes->get('pengumuman/detail/(:num)',      'Operator\PengumumanController::detail/$1');

    // Transparansi Anggaran (APBDes / APBG)
    $routes->get('transparansi',                  'Operator\TransparansiController::index');
    $routes->get('transparansi/tambah',           'Operator\TransparansiController::create');
    $routes->post('transparansi/simpan',          'Operator\TransparansiController::store');
    $routes->get('transparansi/edit/(:num)',      'Operator\TransparansiController::edit/$1');
    $routes->post('transparansi/update/(:num)',   'Operator\TransparansiController::update/$1');
    $routes->post('transparansi/toggle/(:num)',   'Operator\TransparansiController::togglePublish/$1');
    $routes->post('transparansi/delete/(:num)',   'Operator\TransparansiController::delete/$1');
    $routes->get('transparansi/detail/(:num)',    'Operator\TransparansiController::detail/$1');
});

// ─────────────────────────────────────────────────────────
// WARGA ROUTES
// ─────────────────────────────────────────────────────────
$routes->group('warga', ['filter' => ['auth', 'role:warga,operator,admin_desa,super_admin', 'tenant']], static function ($routes) {
    $routes->get('dashboard',          'Warga\DashboardController::index');
    // ★ Core: Permohonan Surat
    $routes->get('permohonan',                        'Warga\PermohonanController::index');
    $routes->get('permohonan/buat',                   'Warga\PermohonanController::buat');
    $routes->post('permohonan/buat',                  'Warga\PermohonanController::simpan', ['filter' => 'ratelimit:10:60']);
    $routes->get('permohonan/preview/(:num)',          'Warga\PermohonanController::preview/$1');
    $routes->get('permohonan/(:num)',                  'Warga\PermohonanController::detail/$1');
    $routes->post('permohonan/(:num)/cancel',          'Warga\PermohonanController::cancel/$1');
    $routes->get('permohonan/tracking/(:segment)',     'Warga\PermohonanController::tracking/$1');

    // Pengaduan & Aspirasi
    $routes->get('pengaduan',                         'Warga\PengaduanController::index');
    $routes->get('pengaduan/buat',                    'Warga\PengaduanController::buat');
    $routes->post('pengaduan/buat',                   'Warga\PengaduanController::simpan', ['filter' => 'ratelimit:10:60']);
    $routes->get('pengaduan/(:num)',                  'Warga\PengaduanController::detail/$1');

    // Pengumuman Resmi Warga
    $routes->get('pengumuman',                        'Warga\PengumumanController::index');
    $routes->get('pengumuman/(:segment)',             'Warga\PengumumanController::detail/$1');

    // Transparansi Anggaran Warga
    $routes->get('transparansi',                      'Warga\TransparansiController::index');
    $routes->get('transparansi/(:segment)',           'Warga\TransparansiController::detail/$1');
});
