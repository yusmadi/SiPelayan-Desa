<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Pengumuman Resmi <?= sebutan_desa() ?></h2>
        <p class="text-muted mb-0">Kelola informasi kedinasan, surat edaran, jadwal kegiatan, dan pengumuman resmi untuk masyarakat <?= sebutan_desa() ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('operator/pengumuman/tambah') ?>" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i>
            <span>Buat Pengumuman Baru</span>
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #0d6efd !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Pengumuman</div>
                    <div class="fs-4 fw-bold text-dark mt-1"><?= $totalAll ?></div>
                </div>
                <div class="icon-box bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-megaphone"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Aktif Terbit</div>
                    <div class="fs-4 fw-bold text-success mt-1"><?= $totalPublished ?></div>
                </div>
                <div class="icon-box bg-success-subtle text-success rounded-3">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #6c757d !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Draft / Diarsipkan</div>
                    <div class="fs-4 fw-bold text-secondary mt-1"><?= $totalDraft ?></div>
                </div>
                <div class="icon-box bg-secondary-subtle text-secondary rounded-3">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #dc3545 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Prioritas / Mendesak</div>
                    <div class="fs-4 fw-bold text-danger mt-1"><?= $totalPenting ?></div>
                </div>
                <div class="icon-box bg-danger-subtle text-danger rounded-3">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('operator/pengumuman') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>" class="form-control border-start-0" placeholder="Cari judul, nomor pengumuman, atau isi...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="sifat" class="form-select">
                    <option value="">Semua Sifat Pengumuman</option>
                    <?php foreach ($sifatList as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($sifat === $k) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" <?= ($status === '1') ? 'selected' : '' ?>>Aktif Terbit</option>
                    <option value="0" <?= ($status === '0') ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <?php if (!empty($search) || !empty($sifat) || $status !== null && $status !== ''): ?>
                    <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                        <th class="ps-4" style="width: 40px;">No</th>
                        <th>Pengumuman & Dokumen</th>
                        <th style="width: 140px;">Sifat Urgensi</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 150px;">Tanggal Terbit</th>
                        <th style="width: 100px;">Dibaca</th>
                        <th class="text-end pe-4" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="bi bi-inbox text-secondary" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="fw-bold">Belum Ada Pengumuman</h6>
                                <p class="small text-muted mb-3">Silakan tambahkan pengumuman baru untuk menyampaikan informasi resmi kepada warga.</p>
                                <a href="<?= base_url('operator/pengumuman/tambah') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Pengumuman Pertama
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $idx => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?= base_url('operator/pengumuman/detail/' . $item['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary mb-1">
                                            <?= esc($item['judul']) ?>
                                        </a>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <?php if (!empty($item['nomor_pengumuman'])): ?>
                                                <span class="badge bg-light text-secondary border font-monospace"><?= esc($item['nomor_pengumuman']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['lampiran_path'])): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                    <i class="bi bi-paperclip me-1"></i>Lampiran Berkas
                                                </span>
                                            <?php endif; ?>
                                            <span>Oleh: <?= esc($item['author_nama'] ?? 'Operator') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $sifatColor = $sifatBadges[$item['sifat']] ?? 'primary';
                                        $sifatIcon = \App\Models\PengumumanModel::SIFAT_ICONS[$item['sifat']] ?? 'bi-info-circle';
                                    ?>
                                    <span class="badge bg-<?= $sifatColor ?>-subtle text-<?= $sifatColor ?> border border-<?= $sifatColor ?>-subtle px-2 py-1">
                                        <i class="bi <?= $sifatIcon ?> me-1"></i><?= esc($item['sifat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['is_published']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-broadcast me-1"></i>Terbit
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                            <i class="bi bi-file-earmark-lock me-1"></i>Draft
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-dark d-block">
                                        <?= !empty($item['published_at']) ? date('d M Y', strtotime($item['published_at'])) : '-' ?>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <?= !empty($item['published_at']) ? date('H:i', strtotime($item['published_at'])) . ' WIB' : 'Belum diterbitkan' ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-eye me-1 text-muted"></i><?= (int) $item['views'] ?>x
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('operator/pengumuman/detail/' . $item['id']) ?>" class="btn btn-outline-secondary" title="Pratinjau Pengumuman">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('operator/pengumuman/edit/' . $item['id']) ?>" class="btn btn-outline-primary" title="Edit Pengumuman">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="post" action="<?= base_url('operator/pengumuman/toggle/' . $item['id']) ?>" class="d-inline" onsubmit="return confirm('<?= $item['is_published'] ? 'Tarik pengumuman ini ke draft?' : 'Terbitkan pengumuman ini ke warga?' ?>')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn <?= $item['is_published'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $item['is_published'] ? 'Arsipkan ke Draft' : 'Terbitkan Sekarang' ?>">
                                                <i class="bi <?= $item['is_published'] ? 'bi-eye-slash' : 'bi-send-check' ?>"></i>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= base_url('operator/pengumuman/delete/' . $item['id']) ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini secara permanen?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Pengumuman">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
