<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Daftar Pengaduan & Aspirasi Warga</h2>
        <p class="text-muted mb-0">Kelola, verifikasi, dan tindak lanjuti laporan serta aspirasi dari masyarakat <?= sebutan_desa() ?>.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <?php $prefix = session('role_slug') === 'operator' ? 'operator' : 'admin-desa'; ?>
        <form method="get" action="<?= base_url($prefix . '/pengaduan') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>" class="form-control border-start-0" placeholder="Cari judul, tiket, pelapor...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriList as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($kategoriFilter === $k) ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusLabels as $s => $label): ?>
                        <option value="<?= $s ?>" <?= ($statusFilter === $s) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <?php if (!empty($search) || !empty($kategoriFilter) || !empty($statusFilter)): ?>
                    <a href="<?= base_url($prefix . '/pengaduan') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 130px;">No. Tiket</th>
                        <th>Pelapor</th>
                        <th>Judul & Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pengaduanList)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                Tidak ada data pengaduan yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pengaduanList as $item): ?>
                            <?php 
                                $badgeColor = $statusColors[$item['status']] ?? 'secondary';
                                $statusName = $statusLabels[$item['status']] ?? ucfirst($item['status']);
                                $pelapor = $item['is_anonymous'] ? '<em>Anonim</em>' : esc($item['nama_pelapor'] ?? 'Warga');
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border font-monospace"><?= esc($item['no_tiket']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= $pelapor ?></div>
                                    <?php if (!$item['is_anonymous'] && !empty($item['email_pelapor'])): ?>
                                        <small class="text-muted"><?= esc($item['email_pelapor']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?= esc($item['judul']) ?></div>
                                    <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.75rem;"><?= esc($item['kategori']) ?></span>
                                    <?php if (!empty($item['tanggapan'])): ?>
                                        <span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.75rem;"><i class="bi bi-check2"></i> Ditanggapi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $badgeColor ?> px-2 py-1"><?= $statusName ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url($prefix . '/pengaduan/' . $item['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye me-1"></i> Periksa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
