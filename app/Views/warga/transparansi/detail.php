<?= $this->extend('layouts/warga') ?>

<?= $this->section('styles') ?>
<style>
    .kop-surat-resmi {
        border-bottom: 3px double #212529;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    .kop-surat-resmi h4, .kop-surat-resmi h5, .kop-surat-resmi h6 {
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    @media print {
        .navbar, .sidebar, .no-print, .btn, .alert {
            display: none !important;
        }
        .content-area {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $rincian = $anggaran['rincian'] ?? [];
    $rPendapatan = $rincian['pendapatan'] ?? [];
    $rBelanja    = $rincian['belanja'] ?? [];
    $rPembiayaan = $rincian['pembiayaan'] ?? [];

    $tBelanja   = (float) ($anggaran['total_belanja'] ?? 0);
    $rBelanjaTotal = (float) ($anggaran['realisasi_belanja'] ?? 0);
    $persenBelanja = ($tBelanja > 0) ? round(($rBelanjaTotal / $tBelanja) * 100, 1) : 0;

    $tPendapatan = (float) ($anggaran['total_pendapatan'] ?? 0);
    $rPendapatanTotal = (float) ($anggaran['realisasi_pendapatan'] ?? 0);
    $persenPendapatan = ($tPendapatan > 0) ? round(($rPendapatanTotal / $tPendapatan) * 100, 1) : 0;

    $isAceh = is_aceh();
    $istilah = $isAceh ? 'APBG' : 'APBDes';
    $sebutan = $isAceh ? 'Gampong' : 'Desa';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 no-print">
    <div>
        <a href="<?= base_url('warga/transparansi') ?>" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Papan Transparansi
        </a>
        <h2 class="h3 font-weight-bold mb-0">Dokumen Transparansi <?= $istilah ?></h2>
    </div>
    <div class="d-flex gap-2">
        <?php
            $waText = "📊 *TRANSPARANSI ANGGARAN " . strtoupper($istilah) . " " . strtoupper($desa['nama_desa'] ?? '') . "*\n\n"
                    . "*" . $anggaran['judul'] . "*\n"
                    . "Total Belanja: Rp " . number_format($tBelanja, 0, ',', '.') . "\n"
                    . "Realisasi: Rp " . number_format($rBelanjaTotal, 0, ',', '.') . " (" . $persenBelanja . "%)\n\n"
                    . "Baca selengkapnya di: " . current_url();
            $waUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waText);
        ?>
        <a href="<?= $waUrl ?>" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-whatsapp me-1"></i> Bagikan
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </button>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Lembar Dokumen Resmi -->
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white rounded-4">
            <!-- Kop Surat Resmi -->
            <div class="kop-surat-resmi text-center position-relative">
                <div class="row align-items-center">
                    <div class="col-2 text-center">
                        <?php if (!empty($desa['logo_path'])): ?>
                            <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo Desa" class="img-fluid" style="max-height: 80px;">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle border" style="width: 70px; height: 70px;">
                                <i class="bi bi-bank fs-2"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-8 text-center">
                        <div class="small fw-semibold text-secondary text-uppercase mb-1" style="letter-spacing: 0.05em;">
                            PEMERINTAH KABUPATEN <?= esc(strtoupper($desa['kabupaten_nama'] ?? 'ACEH UTARA')) ?>
                        </div>
                        <div class="small fw-bold text-dark text-uppercase mb-1">
                            KECAMATAN <?= esc(strtoupper($desa['nama_kecamatan'] ?? 'SAMUDERA')) ?>
                        </div>
                        <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.03em;">
                            PEMERINTAH <?= strtoupper($sebutan) ?> <?= esc(strtoupper($desa['nama_desa'] ?? 'PULO DRIEN')) ?>
                        </h4>
                        <small class="text-muted d-block" style="font-size: 0.76rem;">
                            <?= esc($desa['alamat_kantor'] ?? 'Kantor Keuchik') ?> <?= !empty($desa['kode_pos']) ? '| Kode Pos: ' . esc($desa['kode_pos']) : '' ?>
                        </small>
                    </div>
                    <div class="col-2 text-center">
                        <?php if (!empty($desa['logo_path'])): ?>
                            <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo" class="img-fluid opacity-0" style="max-height: 80px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Judul & Nomor Laporan -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.06em; text-decoration: underline;">
                    LAPORAN REALISASI <?= $istilah ?>
                </h4>
                <?php if (!empty($anggaran['nomor_pengumuman'])): ?>
                    <div class="font-monospace text-muted fw-semibold">
                        Dasar Hukum: <?= esc($anggaran['nomor_pengumuman']) ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                        <i class="bi bi-calendar-check me-1"></i>Tahun Anggaran <?= esc($anggaran['tahun_anggaran']) ?>
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-layers me-1"></i>Periode: <?= esc($anggaran['periode_anggaran'] ?? 'Tahap I') ?>
                    </span>
                    <span class="badge bg-light text-muted border">
                        <i class="bi bi-clock me-1"></i>Per <?= date('d F Y', strtotime($anggaran['published_at'] ?? $anggaran['created_at'])) ?>
                    </span>
                </div>
            </div>

            <div class="bg-light p-3 rounded-3 mb-4 text-center border">
                <h5 class="fw-bold text-dark mb-0"><?= esc($anggaran['judul']) ?></h5>
            </div>

            <!-- 3 Kartu Metrik Utama -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-success-subtle bg-opacity-10 border border-success-subtle p-3 rounded-3 h-100">
                        <small class="text-muted text-uppercase fw-semibold d-block">1. Pendapatan <?= $sebutan ?></small>
                        <div class="fs-5 fw-bold text-success mt-1">Rp <?= number_format($rPendapatanTotal, 0, ',', '.') ?></div>
                        <small class="text-muted">Target: Rp <?= number_format($tPendapatan, 0, ',', '.') ?> (<?= $persenPendapatan ?>%)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-primary-subtle bg-opacity-10 border border-primary-subtle p-3 rounded-3 h-100">
                        <small class="text-muted text-uppercase fw-semibold d-block">2. Belanja <?= $sebutan ?></small>
                        <div class="fs-5 fw-bold text-primary mt-1">Rp <?= number_format($rBelanjaTotal, 0, ',', '.') ?></div>
                        <small class="text-muted">Pagu: Rp <?= number_format($tBelanja, 0, ',', '.') ?> (<?= $persenBelanja ?>%)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-info-subtle bg-opacity-10 border border-info-subtle p-3 rounded-3 h-100">
                        <small class="text-muted text-uppercase fw-semibold d-block">3. Pembiayaan (SiLPA)</small>
                        <div class="fs-5 fw-bold text-info mt-1">Rp <?= number_format($anggaran['total_pembiayaan'] ?? 0, 0, ',', '.') ?></div>
                        <small class="text-muted">Sisa lebih perhitungan anggaran</small>
                    </div>
                </div>
            </div>

            <!-- Rincian Belanja 5 Bidang Infografis -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3">
                    <i class="bi bi-pie-chart text-primary me-2"></i>Rincian Realisasi Belanja per 5 Bidang
                </h6>

                <?php foreach ($bidangList as $key => $meta): ?>
                    <?php
                        $bData = $rBelanja[$key] ?? ['anggaran' => 0, 'realisasi' => 0, 'keterangan' => ''];
                        $bAng  = (float) ($bData['anggaran'] ?? 0);
                        $bReal = (float) ($bData['realisasi'] ?? 0);
                        $bPct  = ($bAng > 0) ? round(($bReal / $bAng) * 100, 1) : 0;
                    ?>
                    <div class="card bg-light border-0 p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> fs-5 me-2"></i>
                                <span class="fw-bold text-dark"><?= esc($meta['nama']) ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Rp <?= number_format($bReal, 0, ',', '.') ?></span>
                                <small class="text-muted">/ Rp <?= number_format($bAng, 0, ',', '.') ?></small>
                                <span class="badge bg-<?= $meta['color'] ?>-subtle text-<?= $meta['color'] ?> border border-<?= $meta['color'] ?>-subtle ms-2"><?= $bPct ?>%</span>
                            </div>
                        </div>
                        <div class="progress my-2" style="height: 8px;">
                            <div class="progress-bar bg-<?= $meta['color'] ?>" role="progressbar" style="width: <?= min(100, $bPct) ?>%"></div>
                        </div>
                        <?php if (!empty($bData['keterangan'])): ?>
                            <small class="text-muted"><i class="bi bi-arrow-right-short"></i> <?= esc($bData['keterangan']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Narasi Penjelasan Jika Ada -->
            <?php if (!empty($anggaran['konten'])): ?>
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3">
                        <i class="bi bi-info-circle text-secondary me-2"></i>Catatan & Penjelasan Kebijakan
                    </h6>
                    <div class="text-muted lh-base" style="font-size: 0.95rem;">
                        <?php if (strpos($anggaran['konten'], '<p>') !== false || strpos($anggaran['konten'], '<br>') !== false): ?>
                            <?= $anggaran['konten'] ?>
                        <?php else: ?>
                            <?= nl2br(esc($anggaran['konten'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lampiran Berkas Jika Ada -->
            <?php if (!empty($anggaran['lampiran_path'])): ?>
                <div class="card bg-light border p-3 mb-4 no-print">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($anggaran['lampiran_nama'] ?? 'Dokumen Laporan Keuangan Resmi') ?></h6>
                                <small class="text-muted">Salinan Perdes APBDes / Dokumen Pelaksanaan Anggaran (DPA)</small>
                            </div>
                        </div>
                        <a href="<?= base_url($anggaran['lampiran_path']) ?>" target="_blank" download class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Unduh Berkas
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tanda Tangan Resmi Pengesahan -->
            <div class="row mt-4 pt-2">
                <div class="col-6 col-sm-7"></div>
                <div class="col-6 col-sm-5 text-center">
                    <div class="small text-muted mb-1"><?= esc($desa['nama_desa'] ?? 'Gampong') ?>, <?= date('d F Y', strtotime($anggaran['published_at'] ?? $anggaran['created_at'])) ?></div>
                    <div class="fw-bold text-dark text-uppercase"><?= sebutan_kades() ?> <?= esc($desa['nama_desa'] ?? '') ?></div>
                    <div style="height: 75px;" class="d-flex align-items-center justify-content-center">
                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">[ TTD Resmi ]</span>
                    </div>
                    <div class="fw-bold text-dark text-decoration-underline text-uppercase"><?= esc($desa['nama_kepala_desa'] ?? 'Pemerintah ' . $sebutan) ?></div>
                    <?php if (!empty($desa['nip_kepala_desa'])): ?>
                        <div class="small text-muted font-monospace">NIP. <?= esc($desa['nip_kepala_desa']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
