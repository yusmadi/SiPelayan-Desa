<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Transparansi Anggaran (<?= is_aceh() ? 'APBG' : 'APBDes' ?>)</h2>
        <p class="text-muted mb-0">Kelola dan publikasikan laporan keuangan, realisasi pendapatan, dan alokasi belanja untuk masyarakat <?= sebutan_desa() ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('operator/transparansi/tambah') ?>" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Laporan Anggaran</span>
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
                    <div class="text-muted small fw-semibold text-uppercase">Total Belanja (<?= $latest['tahun_anggaran'] ?? date('Y') ?>)</div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp <?= number_format($nominalBelanja, 0, ',', '.') ?></div>
                </div>
                <div class="icon-box bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Realisasi Belanja</div>
                    <div class="fs-5 fw-bold text-success mt-1">Rp <?= number_format($nominalRealisasi, 0, ',', '.') ?></div>
                </div>
                <div class="icon-box bg-success-subtle text-success rounded-3">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #ffc107 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Persentase Realisasi</div>
                    <div class="fs-4 fw-bold text-warning mt-1"><?= $persenRealisasi ?>%</div>
                </div>
                <div class="icon-box bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-pie-chart"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 stat-card" style="border-left-color: #6c757d !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Laporan Aktif</div>
                    <div class="fs-4 fw-bold text-dark mt-1"><?= $totalPublished ?> <small class="fs-6 text-muted">/ <?= $totalAll ?></small></div>
                </div>
                <div class="icon-box bg-secondary-subtle text-secondary rounded-3">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('operator/transparansi') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
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
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" <?= ($status === '1') ? 'selected' : '' ?>>Aktif Terbit</option>
                    <option value="0" <?= ($status === '0') ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <?php if (!empty($search) || !empty($tahun) || $status !== null && $status !== ''): ?>
                    <a href="<?= base_url('operator/transparansi') ?>" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                        <th>Laporan & Periode</th>
                        <th style="width: 170px;">Anggaran Belanja</th>
                        <th style="width: 170px;">Realisasi</th>
                        <th style="width: 150px;">Capaian (%)</th>
                        <th style="width: 120px;">Status</th>
                        <th class="text-end pe-4" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="bi bi-wallet2 text-secondary" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="fw-bold">Belum Ada Data Transparansi Anggaran</h6>
                                <p class="small text-muted mb-3">Tambahkan data laporan APBDes/APBG untuk transparansi publik kepada masyarakat desa.</p>
                                <a href="<?= base_url('operator/transparansi/tambah') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Laporan Pertama
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $idx => $item): ?>
                            <?php
                                $bAnggaran  = (float) ($item['total_belanja'] ?? 0);
                                $bRealisasi = (float) ($item['realisasi_belanja'] ?? 0);
                                $persen     = ($bAnggaran > 0) ? round(($bRealisasi / $bAnggaran) * 100, 1) : 0;
                                $pColor     = $persen >= 75 ? 'success' : ($persen >= 40 ? 'warning' : 'info');
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?= base_url('operator/transparansi/detail/' . $item['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary mb-1">
                                            <?= esc($item['judul']) ?>
                                        </a>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                TA <?= esc($item['tahun_anggaran']) ?> · <?= esc($item['periode_anggaran'] ?? 'Tahap I') ?>
                                            </span>
                                            <?php if (!empty($item['nomor_pengumuman'])): ?>
                                                <span class="font-monospace"><?= esc($item['nomor_pengumuman']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['lampiran_path'])): ?>
                                                <span class="badge bg-light text-secondary border"><i class="bi bi-paperclip me-1"></i>PDF</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">Rp <?= number_format($bAnggaran, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-success">Rp <?= number_format($bRealisasi, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $pColor ?>" role="progressbar" style="width: <?= min(100, $persen) ?>%"></div>
                                        </div>
                                        <span class="small fw-bold text-<?= $pColor ?>"><?= $persen ?>%</span>
                                    </div>
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
                                <td class="pe-4 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('operator/transparansi/detail/' . $item['id']) ?>" class="btn btn-outline-secondary" title="Pratinjau Infografis">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('operator/transparansi/edit/' . $item['id']) ?>" class="btn btn-outline-primary" title="Edit Laporan">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="post" action="<?= base_url('operator/transparansi/toggle/' . $item['id']) ?>" class="d-inline" onsubmit="return confirm('<?= $item['is_published'] ? 'Tarik laporan ini ke status draft?' : 'Terbitkan laporan anggaran ini ke warga?' ?>')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn <?= $item['is_published'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $item['is_published'] ? 'Arsipkan ke Draft' : 'Terbitkan Sekarang' ?>">
                                                <i class="bi <?= $item['is_published'] ? 'bi-eye-slash' : 'bi-send-check' ?>"></i>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= base_url('operator/transparansi/delete/' . $item['id']) ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data transparansi anggaran ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Data">
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
