<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Dashboard Super Admin</h2>
        <p class="text-muted mb-0">Overview sistem multi-tenant seluruh kabupaten & desa.</p>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i><?= date('d F Y') ?></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= base_url('super-admin/provinsi') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #6366f1; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="text-muted small">Provinsi <i class="bi bi-arrow-right-short text-primary"></i></div>
                <h4 class="fw-bold text-dark mb-0"><?= $totalProvinsi ?? 0 ?></h4>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= base_url('super-admin/kabupaten') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #3b82f6; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="text-muted small">Kabupaten <i class="bi bi-arrow-right-short text-primary"></i></div>
                <h4 class="fw-bold text-dark mb-0"><?= $totalKabupaten ?></h4>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= base_url('super-admin/kecamatan') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #06b6d4; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="text-muted small">Kecamatan <i class="bi bi-arrow-right-short text-primary"></i></div>
                <h4 class="fw-bold text-dark mb-0"><?= $totalKecamatan ?? 0 ?></h4>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= base_url('super-admin/desa') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #0ea5e9; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="text-muted small">Desa/Tenant <i class="bi bi-arrow-right-short text-primary"></i></div>
                <h4 class="fw-bold text-dark mb-0"><?= $totalDesa ?></h4>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= base_url('super-admin/users') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #10b981; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="text-muted small">Pengguna <i class="bi bi-arrow-right-short text-primary"></i></div>
                <h4 class="fw-bold text-dark mb-0"><?= $totalUsers ?></h4>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 bg-white stat-card h-100" style="border-left-color: #f59e0b;">
            <div class="text-muted small">Permohonan</div>
            <h4 class="fw-bold text-dark mb-0"><?= $totalSurat ?></h4>
        </div>
    </div>
</div>

<div class="card bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Daftar Desa Terdaftar</h5>
        <a href="<?= base_url('super-admin/desa') ?>" class="btn btn-sm btn-outline-primary">
            Lihat Semua Desa <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Desa</th>
                    <th>Kecamatan</th>
                    <th>Kabupaten</th>
                    <th>Kepala Desa / Admin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($recentDesa)): ?>
                    <?php foreach($recentDesa as $d): ?>
                        <tr>
                            <td>#<?= $d['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($d['nama_desa']) ?></div>
                                <?php if (!empty($d['tenant_slug'])): ?>
                                    <div class="text-muted small">
                                        <span class="badge bg-light text-secondary border">/<?= esc($d['tenant_slug']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($d['nama_kecamatan']) ?></td>
                            <td><?= esc($d['nama_kabupaten'] ?? '-') ?></td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc(!empty($d['nama_admin_desa']) ? $d['nama_admin_desa'] : ($d['nama_kepala_desa'] ?: '-')) ?></div>
                                <?php if(!empty($d['email_admin'])): ?>
                                    <div class="text-muted small"><?= esc($d['email_admin']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($d['is_active'] && (!isset($d['admin_is_active']) || $d['admin_is_active'] == 1)): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                <?php elseif (isset($d['admin_is_active']) && $d['admin_is_active'] == 0): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada desa aktif yang memiliki akun Kepala Desa / Admin Desa.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
