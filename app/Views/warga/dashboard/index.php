<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 font-weight-bold mb-0">Selamat Datang, Warga!</h2>
    <div class="text-muted"><?= date('d F Y') ?></div>
</div>

<div class="row g-4 mb-5">
    <!-- Card Ajukan Surat -->
    <div class="col-sm-6 col-xl-3">
        <a href="/warga/permohonan/buat" class="text-decoration-none">
            <div class="card h-100 bg-primary text-white p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-white text-primary">
                            <i class="bi bi-envelope-plus"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold">Ajukan Surat</h5>
                    <p class="card-text text-white-50">Buat permohonan surat keterangan baru secara online.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card Status Permohonan -->
    <div class="col-sm-6 col-xl-3">
        <a href="/warga/permohonan" class="text-decoration-none">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-light text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <span class="badge bg-warning text-dark"><?= ($processCount ?? 0) ?> Diproses</span>
                    </div>
                    <h5 class="card-title fw-bold text-dark">Status Permohonan</h5>
                    <p class="card-text text-muted">Cek status surat yang sedang diproses.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card Layanan Pengaduan -->
    <div class="col-sm-6 col-xl-3">
        <a href="<?= base_url('warga/pengaduan') ?>" class="text-decoration-none">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-light text-danger">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <?php if (($pengaduanCount ?? 0) > 0): ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= $pengaduanCount ?> Aktif</span>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title fw-bold text-dark">Layanan Pengaduan</h5>
                    <p class="card-text text-muted">Laporkan masalah infrastruktur atau layanan.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card Tanya Kades / Keuchik -->
    <div class="col-sm-6 col-xl-3">
        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalTanyaKades" class="text-decoration-none">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-chat-dots me-1"></i>Konsultasi
                        </span>
                    </div>
                    <h5 class="card-title fw-bold text-dark">Tanya <?= sebutan_kades() ?></h5>
                    <p class="card-text text-muted">Konsultasi langsung dengan <?= strtolower(sebutan_kades()) ?> via WhatsApp.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold text-dark">Informasi <?= sebutan_desa() ?> Terbaru</h4>
    <a href="<?= base_url('warga/pengumuman') ?>" class="text-decoration-none small fw-semibold text-primary">
        Lihat Semua Pengumuman <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Card Pengumuman Resmi -->
    <div class="col-md-6">
        <?php if (!empty($latestPengumuman)): ?>
            <?php
                $sifatColor = \App\Models\PengumumanModel::SIFAT_BADGES[$latestPengumuman['sifat']] ?? 'primary';
                $sifatIcon = \App\Models\PengumumanModel::SIFAT_ICONS[$latestPengumuman['sifat']] ?? 'bi-info-circle';
            ?>
            <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header Card Pengumuman -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-shield-check me-1"></i>Pengumuman Resmi
                                </span>
                                <span class="badge bg-<?= $sifatColor ?>-subtle text-<?= $sifatColor ?> border border-<?= $sifatColor ?>-subtle px-2 py-1">
                                    <i class="bi <?= $sifatIcon ?> me-1"></i><?= esc($latestPengumuman['sifat']) ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($latestPengumuman['published_at'] ?? $latestPengumuman['created_at'])) ?>
                            </small>
                        </div>

                        <?php if (!empty($latestPengumuman['nomor_pengumuman'])): ?>
                            <div class="text-muted font-monospace small mb-2" style="font-size: 0.78rem;">
                                <i class="bi bi-file-earmark-text me-1"></i>No: <?= esc($latestPengumuman['nomor_pengumuman']) ?>
                            </div>
                        <?php endif; ?>

                        <h5 class="card-title fw-bold text-dark mb-2 lh-base">
                            <?= esc($latestPengumuman['judul']) ?>
                        </h5>

                        <p class="card-text text-muted small lh-base mb-4">
                            <?= esc($latestPengumuman['ringkasan'] ?: mb_substr(strip_tags($latestPengumuman['konten']), 0, 150) . '...') ?>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalPengumuman">
                            <i class="bi bi-book-half"></i> Baca Selengkapnya
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($latestPengumuman['lampiran_path'])): ?>
                                <span class="badge bg-light text-secondary border" title="Ada berkas lampiran">
                                    <i class="bi bi-paperclip me-1"></i>Lampiran
                                </span>
                            <?php endif; ?>
                            <a href="<?= base_url('warga/pengumuman') ?>" class="btn btn-sm btn-outline-secondary">
                                Arsip (<?= (count($otherPengumuman ?? []) + 1) ?>)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card h-100 border-0 shadow-sm bg-light">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="icon-box bg-white text-muted rounded-circle shadow-sm mb-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-bell-slash fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Pengumuman Resmi</h6>
                    <p class="text-muted small mb-0">Pemerintah <?= sebutan_desa() ?> belum mempublikasikan pengumuman terbaru untuk warga.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Card Transparansi / APBDes -->
    <div class="col-md-6">
        <?php if (!empty($latestAnggaran)): ?>
            <?php
                $tB = (float) ($latestAnggaran['total_belanja'] ?? 0);
                $rB = (float) ($latestAnggaran['realisasi_belanja'] ?? 0);
                $pct = ($tB > 0) ? round(($rB / $tB) * 100, 1) : 0;
                $pctColor = $pct >= 75 ? 'success' : ($pct >= 40 ? 'warning' : 'primary');
                $isAceh = is_aceh();
                $istilah = $isAceh ? 'APBG' : 'APBDes';
            ?>
            <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-top: 4px solid #198754 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-bank me-1"></i>Transparansi <?= $istilah ?>
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    TA <?= esc($latestAnggaran['tahun_anggaran']) ?> · <?= esc($latestAnggaran['periode_anggaran'] ?? 'Tahap I') ?>
                                </span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-shield-check me-1"></i>Terverifikasi
                            </span>
                        </div>

                        <h5 class="card-title fw-bold text-dark mb-2 lh-base">
                            <?= esc($latestAnggaran['judul']) ?>
                        </h5>

                        <p class="card-text text-muted small lh-base mb-3">
                            <?= esc($latestAnggaran['ringkasan'] ?: 'Laporan realisasi penyerapan anggaran belanja dan pendapatan ' . strtolower(sebutan_desa()) . ' periode berjalan.') ?>
                        </p>

                        <!-- Visual Progress Bar Anggaran -->
                        <div class="bg-light p-3 rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Realisasi Belanja:</span>
                                <span class="small fw-bold text-success">Rp <?= number_format($rB, 0, ',', '.') ?> <span class="text-muted fw-normal">/ Rp <?= number_format($tB, 0, ',', '.') ?></span></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?= $pctColor ?>" role="progressbar" style="width: <?= min(100, $pct) ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted" style="font-size: 0.72rem;">Capaian Penyerapan</small>
                                <span class="small fw-bold text-<?= $pctColor ?>"><?= $pct ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-outline-success btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTransparansi">
                            <i class="bi bi-pie-chart"></i> Lihat Rincian
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($latestAnggaran['lampiran_path'])): ?>
                                <span class="badge bg-light text-secondary border" title="Ada dokumen SK/Perdes resmi">
                                    <i class="bi bi-paperclip me-1"></i>Dokumen PDF
                                </span>
                            <?php endif; ?>
                            <a href="<?= base_url('warga/transparansi') ?>" class="btn btn-sm btn-outline-secondary">
                                Arsip Keuangan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card h-100 border-0 shadow-sm bg-light">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="icon-box bg-white text-muted rounded-circle shadow-sm mb-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Laporan Transparansi</h6>
                    <p class="text-muted small mb-0">Pemerintah <?= sebutan_desa() ?> belum mempublikasikan laporan APBDes/APBG terbaru.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Quick View Pengumuman Resmi -->
<?= $this->include('warga/dashboard/partials/modal_pengumuman') ?>

<!-- Modal Quick View Transparansi Anggaran -->
<?= $this->include('warga/dashboard/partials/modal_transparansi') ?>
<?= $this->endSection() ?>
