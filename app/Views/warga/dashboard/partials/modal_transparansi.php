<?php if (!empty($latestAnggaran)): ?>
<?php
    $rincian = $latestAnggaran['rincian'] ?? [];
    $rPendapatan = $rincian['pendapatan'] ?? [];
    $rBelanja    = $rincian['belanja'] ?? [];
    $rPembiayaan = $rincian['pembiayaan'] ?? [];

    $tBelanja   = (float) ($latestAnggaran['total_belanja'] ?? 0);
    $rBelanjaTotal = (float) ($latestAnggaran['realisasi_belanja'] ?? 0);
    $persenBelanja = ($tBelanja > 0) ? round(($rBelanjaTotal / $tBelanja) * 100, 1) : 0;

    $tPendapatan = (float) ($latestAnggaran['total_pendapatan'] ?? 0);
    $rPendapatanTotal = (float) ($latestAnggaran['realisasi_pendapatan'] ?? 0);
    $persenPendapatan = ($tPendapatan > 0) ? round(($rPendapatanTotal / $tPendapatan) * 100, 1) : 0;

    $isAceh = is_aceh();
    $istilah = $isAceh ? 'APBG' : 'APBDes';
    $sebutan = $isAceh ? 'Gampong' : 'Desa';
?>
<div class="modal fade" id="modalTransparansi" tabindex="-1" aria-labelledby="modalTransparansiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header bg-light border-bottom px-4 py-3 align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-success text-white rounded-3 shadow-sm" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalTransparansiLabel">Transparansi Anggaran <?= $istilah ?></h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Pemerintah <?= $sebutan ?> <?= esc($desa['nama_desa'] ?? '') ?> · TA <?= esc($latestAnggaran['tahun_anggaran']) ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-4 p-md-5">
                <!-- Kop Surat Resmi -->
                <div class="text-center pb-3 mb-4" style="border-bottom: 3px double #333;">
                    <div class="row align-items-center">
                        <div class="col-2 text-center">
                            <?php if (!empty($desa['logo_path'])): ?>
                                <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo Desa" class="img-fluid" style="max-height: 70px;">
                            <?php else: ?>
                                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle border" style="width: 60px; height: 60px;">
                                    <i class="bi bi-bank fs-3"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-8 text-center">
                            <div class="small fw-semibold text-secondary text-uppercase mb-0" style="letter-spacing: 0.05em;">
                                PEMERINTAH KABUPATEN <?= esc(strtoupper($desa['kabupaten_nama'] ?? 'ACEH UTARA')) ?>
                            </div>
                            <div class="small fw-bold text-dark text-uppercase mb-0">
                                KECAMATAN <?= esc(strtoupper($desa['nama_kecamatan'] ?? 'SAMUDERA')) ?>
                            </div>
                            <h5 class="fw-bold text-dark text-uppercase mb-0" style="letter-spacing: 0.04em;">
                                PEMERINTAH <?= strtoupper($sebutan) ?> <?= esc(strtoupper($desa['nama_desa'] ?? 'PULO DRIEN')) ?>
                            </h5>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">
                                <?= esc($desa['alamat_kantor'] ?? 'Kantor Keuchik') ?> <?= !empty($desa['kode_pos']) ? '| Kode Pos: ' . esc($desa['kode_pos']) : '' ?>
                            </small>
                        </div>
                        <div class="col-2 text-center">
                            <?php if (!empty($desa['logo_path'])): ?>
                                <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo" class="img-fluid opacity-0" style="max-height: 70px;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Judul & Meta Dokumen -->
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.06em; text-decoration: underline;">
                        LAPORAN REALISASI <?= $istilah ?>
                    </h5>
                    <?php if (!empty($latestAnggaran['nomor_pengumuman'])): ?>
                        <div class="font-monospace text-muted fw-semibold small">
                            Dasar Hukum: <?= esc($latestAnggaran['nomor_pengumuman']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                            <i class="bi bi-calendar-check me-1"></i>Tahun Anggaran <?= esc($latestAnggaran['tahun_anggaran']) ?>
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-layers me-1"></i>Periode: <?= esc($latestAnggaran['periode_anggaran'] ?? 'Tahap I') ?>
                        </span>
                        <span class="badge bg-light text-muted border">
                            <i class="bi bi-shield-check text-success me-1"></i>Data Resmi Terverifikasi
                        </span>
                    </div>
                </div>

                <!-- Header Perihal -->
                <div class="bg-light p-3 rounded-3 mb-4 text-center border">
                    <h5 class="fw-bold text-dark mb-0"><?= esc($latestAnggaran['judul']) ?></h5>
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
                            <div class="fs-5 fw-bold text-info mt-1">Rp <?= number_format($latestAnggaran['total_pembiayaan'] ?? 0, 0, ',', '.') ?></div>
                            <small class="text-muted">Sisa lebih pembiayaan</small>
                        </div>
                    </div>
                </div>

                <!-- Rincian Belanja 5 Bidang Permendagri -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Rincian Realisasi Belanja per 5 Bidang
                    </h6>

                    <?php foreach (\App\Models\AnggaranModel::BIDANG_LIST as $key => $meta): ?>
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

                <!-- Lampiran Berkas Jika Ada -->
                <?php if (!empty($latestAnggaran['lampiran_path'])): ?>
                    <div class="card border-0 bg-primary-subtle bg-opacity-10 border border-primary-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-white text-danger rounded-3 me-3 shadow-sm" style="width: 42px; height: 42px;">
                                    <i class="bi bi-file-earmark-pdf fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= esc($latestAnggaran['lampiran_nama'] ?? 'Dokumen Perdes / Laporan APBDes') ?></h6>
                                    <small class="text-muted">Salinan resmi dokumen keuangan gampong/desa berstempel.</small>
                                </div>
                            </div>
                            <a href="<?= base_url($latestAnggaran['lampiran_path']) ?>" target="_blank" download class="btn btn-sm btn-primary shadow-sm px-3">
                                <i class="bi bi-download me-1"></i> Unduh Berkas
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tanda Tangan Resmi Pengesahan -->
                <div class="row mt-4 pt-2">
                    <div class="col-6 col-sm-7"></div>
                    <div class="col-6 col-sm-5 text-center">
                        <small class="text-muted d-block mb-1">
                            <?= esc($desa['nama_desa'] ?? 'Gampong') ?>, <?= date('d F Y', strtotime($latestAnggaran['published_at'] ?? $latestAnggaran['created_at'])) ?>
                        </small>
                        <div class="fw-bold text-dark text-uppercase small"><?= sebutan_kades() ?> <?= esc($desa['nama_desa'] ?? '') ?></div>
                        <div style="height: 65px;" class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">[ TTD Resmi ]</span>
                        </div>
                        <div class="fw-bold text-dark text-decoration-underline small text-uppercase">
                            <?= esc($desa['nama_kepala_desa'] ?? 'Pemerintah ' . $sebutan) ?>
                        </div>
                        <?php if (!empty($desa['nip_kepala_desa'])): ?>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">NIP. <?= esc($desa['nip_kepala_desa']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer Modal Actions -->
            <div class="modal-footer bg-light px-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <a href="<?= base_url('warga/transparansi') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-archive me-1"></i> Arsip Transparansi
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <?php
                        $waText = "📊 *TRANSPARANSI ANGGARAN " . strtoupper($istilah) . " " . strtoupper($desa['nama_desa'] ?? '') . "*\n\n"
                                . "*" . $latestAnggaran['judul'] . "*\n"
                                . "Total Belanja: Rp " . number_format($tBelanja, 0, ',', '.') . "\n"
                                . "Realisasi: Rp " . number_format($rBelanjaTotal, 0, ',', '.') . " (" . $persenBelanja . "%)\n\n"
                                . "Info selengkapnya: " . base_url('warga/transparansi/' . $latestAnggaran['slug']);
                        $waUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waText);
                    ?>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-whatsapp me-1"></i> Bagikan
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
