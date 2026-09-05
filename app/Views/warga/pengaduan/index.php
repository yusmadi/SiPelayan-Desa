<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Layanan Pengaduan & Aspirasi</h2>
        <p class="text-muted mb-0">Sampaikan keluhan, aspirasi, atau laporan masalah infrastruktur dan layanan di <?= sebutan_desa() ?>.</p>
    </div>
    <a href="<?= base_url('warga/pengaduan/buat') ?>" class="btn btn-primary shadow-sm px-3 py-2 fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Buat Pengaduan Baru
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-light text-secondary me-3 rounded-circle" style="width: 44px; height: 44px;">
                        <i class="bi bi-megaphone fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Aduan</div>
                        <div class="h4 mb-0 fw-bold"><?= $stats['total'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning-subtle text-warning me-3 rounded-circle" style="width: 44px; height: 44px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Menunggu</div>
                        <div class="h4 mb-0 fw-bold text-warning"><?= $stats['menunggu'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 rounded-circle" style="width: 44px; height: 44px;">
                        <i class="bi bi-gear-wide-connected fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Diproses</div>
                        <div class="h4 mb-0 fw-bold text-primary"><?= $stats['diproses'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success-subtle text-success me-3 rounded-circle" style="width: 44px; height: 44px;">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Selesai</div>
                        <div class="h4 mb-0 fw-bold text-success"><?= $stats['selesai'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('warga/pengaduan') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>" class="form-control border-start-0 ps-0" placeholder="Cari judul, tiket, atau isi laporan...">
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
                <button type="submit" class="btn btn-outline-primary flex-fill">Filter</button>
                <?php if (!empty($search) || !empty($kategoriFilter) || !empty($statusFilter)): ?>
                    <a href="<?= base_url('warga/pengaduan') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- List of Pengaduan -->
<?php if (empty($pengaduanList)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <div class="text-muted mb-3" style="font-size: 3.5rem;">
                <i class="bi bi-chat-left-dots text-secondary opacity-50"></i>
            </div>
            <h5 class="fw-bold">Belum Ada Pengaduan</h5>
            <p class="text-muted mb-3">Anda belum pernah mengajukan pengaduan atau tidak ada data yang sesuai filter.</p>
            <a href="<?= base_url('warga/pengaduan/buat') ?>" class="btn btn-primary px-4">
                <i class="bi bi-plus-lg me-1"></i> Ajukan Pengaduan Sekarang
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($pengaduanList as $item): ?>
            <?php 
                $badgeColor = $statusColors[$item['status']] ?? 'secondary';
                $statusName = $statusLabels[$item['status']] ?? ucfirst($item['status']);
                $fotos = !empty($item['foto_paths']) ? json_decode($item['foto_paths'], true) : [];
            ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-<?= $badgeColor ?> px-2 py-1"><?= $statusName ?></span>
                                    <span class="badge bg-light text-dark border"><?= esc($item['kategori']) ?></span>
                                    <?php if ($item['is_anonymous']): ?>
                                        <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-incognito me-1"></i>Anonim</span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-1">
                                    <a href="<?= base_url('warga/pengaduan/' . $item['id']) ?>" class="text-decoration-none text-dark hover-primary">
                                        <?= esc($item['judul']) ?>
                                    </a>
                                </h5>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-muted border font-monospace"><?= esc($item['no_tiket']) ?></span>
                                <div class="small text-muted mt-1"><?= date('d M Y, H:i', strtotime($item['created_at'])) ?></div>
                            </div>
                        </div>

                        <p class="card-text text-muted mb-3 text-truncate-2">
                            <?= esc($item['isi_laporan']) ?>
                        </p>

                        <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top gap-2">
                            <div class="small text-muted">
                                <?php if (!empty($item['lokasi'])): ?>
                                    <i class="bi bi-geo-alt me-1 text-danger"></i> <?= esc($item['lokasi']) ?>
                                <?php endif; ?>
                                <?php if (!empty($fotos)): ?>
                                    <span class="ms-2"><i class="bi bi-images me-1 text-primary"></i> <?= count($fotos) ?> Foto</span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($item['tanggapan'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-reply-fill me-1"></i> Telah Ditanggapi
                                    </span>
                                <?php endif; ?>
                                <a href="<?= base_url('warga/pengaduan/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary fw-medium px-3">
                                    Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hover-primary:hover {
    color: #0d6efd !important;
}
</style>
<?= $this->endSection() ?>
