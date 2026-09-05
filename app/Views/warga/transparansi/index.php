<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<?php
    $isAceh = is_aceh();
    $istilah = $isAceh ? 'APBG' : 'APBDes';
    $sebutan = $isAceh ? 'Gampong' : 'Desa';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Papan Transparansi Anggaran (<?= $istilah ?>)</h2>
        <p class="text-muted mb-0">Informasi resmi keterbukaan anggaran pendapatan, alokasi pembiayaan, dan realisasi belanja Pemerintah <?= $sebutan ?> <?= esc($desa['nama_desa'] ?? '') ?>.</p>
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
        <form method="get" action="<?= base_url('warga/transparansi') ?>" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>" class="form-control border-start-0" placeholder="Cari judul laporan, perdes, atau periode...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun Anggaran</option>
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($tahun === $y) ? 'selected' : '' ?>>Tahun Anggaran <?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                <?php if (!empty($search) || !empty($tahun)): ?>
                    <a href="<?= base_url('warga/transparansi') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Grid Laporan Transparansi -->
<?php if (empty($list)): ?>
    <div class="card border-0 shadow-sm bg-white p-5 text-center">
        <div class="mb-3">
            <i class="bi bi-wallet2 text-muted" style="font-size: 3.5rem;"></i>
        </div>
        <h5 class="fw-bold text-dark">Tidak Ada Laporan Transparansi Ditemukan</h5>
        <p class="text-muted small mb-0">Belum ada data publikasi keuangan yang sesuai dengan pencarian Anda.</p>
        <?php if (!empty($search) || !empty($tahun)): ?>
            <div class="mt-3">
                <a href="<?= base_url('warga/transparansi') ?>" class="btn btn-sm btn-outline-primary">Tampilkan Semua Data</a>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($list as $item): ?>
            <?php
                $tBelanja   = (float) ($item['total_belanja'] ?? 0);
                $rBelanjaTotal = (float) ($item['realisasi_belanja'] ?? 0);
                $pct        = ($tBelanja > 0) ? round(($rBelanjaTotal / $tBelanja) * 100, 1) : 0;
                $pctColor   = $pct >= 75 ? 'success' : ($pct >= 40 ? 'warning' : 'primary');
            ?>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-top: 4px solid var(--bs-<?= $pctColor ?>) !important;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-bank me-1"></i>TA <?= esc($item['tahun_anggaran']) ?> · <?= esc($item['periode_anggaran'] ?? 'Tahap I') ?>
                                </span>
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-shield-check text-success me-1"></i>Resmi Terverifikasi
                                </span>
                            </div>

                            <?php if (!empty($item['nomor_pengumuman'])): ?>
                                <div class="font-monospace text-muted small mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-file-earmark-ruled me-1"></i>Dasar Hukum: <?= esc($item['nomor_pengumuman']) ?>
                                </div>
                            <?php endif; ?>

                            <h5 class="card-title fw-bold text-dark mb-2 lh-base">
                                <a href="<?= base_url('warga/transparansi/' . ($item['slug'] ?: $item['id'])) ?>" class="text-decoration-none text-dark hover-primary">
                                    <?= esc($item['judul']) ?>
                                </a>
                            </h5>

                            <p class="card-text text-muted small lh-base mb-3">
                                <?= esc($item['ringkasan'] ?: 'Laporan realisasi belanja dan pendapatan periode berjalan.') ?>
                            </p>

                            <!-- Progress Bar Card -->
                            <div class="bg-light p-3 rounded-3 border mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Realisasi Belanja:</span>
                                    <span class="small fw-bold text-success">Rp <?= number_format($rBelanjaTotal, 0, ',', '.') ?> <span class="text-muted fw-normal">/ Rp <?= number_format($tBelanja, 0, ',', '.') ?></span></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $pctColor ?>" role="progressbar" style="width: <?= min(100, $pct) ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted" style="font-size: 0.72rem;">Capaian Realisasi</small>
                                    <span class="small fw-bold text-<?= $pctColor ?>"><?= $pct ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="<?= base_url('warga/transparansi/' . ($item['slug'] ?: $item['id'])) ?>" class="btn btn-sm btn-outline-success shadow-sm">
                                <i class="bi bi-pie-chart me-1"></i> Lihat Infografis Rincian
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($item['lampiran_path'])): ?>
                                    <span class="badge bg-light text-secondary border"><i class="bi bi-paperclip me-1"></i>Dokumen PDF</span>
                                <?php endif; ?>
                                <span class="text-muted small"><i class="bi bi-eye me-1"></i><?= (int) $item['views'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
