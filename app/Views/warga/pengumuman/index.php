<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Papan Pengumuman Resmi <?= sebutan_desa() ?></h2>
        <p class="text-muted mb-0">Informasi kedinasan, surat edaran, kegiatan sosial, dan pemberitahuan penting dari Pemerintah <?= sebutan_desa() ?> <?= esc($desa['nama_desa'] ?? '') ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('warga/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filter & Pencarian -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('warga/pengumuman') ?>" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>" class="form-control border-start-0" placeholder="Cari judul pengumuman, nomor surat, atau kata kunci...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="sifat" class="form-select">
                    <option value="">Semua Tingkat Urgensi</option>
                    <?php foreach ($sifatList as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($sifat === $k) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                <?php if (!empty($search) || !empty($sifat)): ?>
                    <a href="<?= base_url('warga/pengumuman') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Pengumuman Grid -->
<?php if (empty($list)): ?>
    <div class="card border-0 shadow-sm bg-white p-5 text-center">
        <div class="mb-3">
            <i class="bi bi-inbox text-muted" style="font-size: 3.5rem;"></i>
        </div>
        <h5 class="fw-bold text-dark">Tidak Ada Pengumuman Ditemukan</h5>
        <p class="text-muted small mb-0">Belum ada pengumuman yang sesuai dengan kriteria pencarian Anda.</p>
        <?php if (!empty($search) || !empty($sifat)): ?>
            <div class="mt-3">
                <a href="<?= base_url('warga/pengumuman') ?>" class="btn btn-sm btn-outline-primary">Tampilkan Semua Pengumuman</a>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($list as $item): ?>
            <?php
                $sifatColor = $sifatBadges[$item['sifat']] ?? 'primary';
                $sifatIcon = \App\Models\PengumumanModel::SIFAT_ICONS[$item['sifat']] ?? 'bi-info-circle';
            ?>
            <div class="col-md-6 col-lg-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-top: 4px solid var(--bs-<?= $sifatColor ?>) !important;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <span class="badge bg-<?= $sifatColor ?>-subtle text-<?= $sifatColor ?> border border-<?= $sifatColor ?>-subtle px-2 py-1">
                                    <i class="bi <?= $sifatIcon ?> me-1"></i><?= esc($item['sifat']) ?>
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?>
                                </small>
                            </div>

                            <?php if (!empty($item['nomor_pengumuman'])): ?>
                                <div class="font-monospace text-muted small mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-file-earmark-text me-1"></i>No: <?= esc($item['nomor_pengumuman']) ?>
                                </div>
                            <?php endif; ?>

                            <h5 class="card-title fw-bold text-dark mb-2">
                                <a href="<?= base_url('warga/pengumuman/' . ($item['slug'] ?: $item['id'])) ?>" class="text-decoration-none text-dark hover-primary">
                                    <?= esc($item['judul']) ?>
                                </a>
                            </h5>

                            <p class="card-text text-muted small lh-base mb-4">
                                <?= esc($item['ringkasan'] ?: mb_substr(strip_tags($item['konten']), 0, 160) . '...') ?>
                            </p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="<?= base_url('warga/pengumuman/' . ($item['slug'] ?: $item['id'])) ?>" class="btn btn-sm btn-primary shadow-sm">
                                <i class="bi bi-book-half me-1"></i> Baca Lengkap
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($item['lampiran_path'])): ?>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-paperclip me-1"></i>Lampiran
                                    </span>
                                <?php endif; ?>
                                <span class="text-muted small">
                                    <i class="bi bi-eye me-1"></i><?= (int) $item['views'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
