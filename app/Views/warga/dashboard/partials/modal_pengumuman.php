<?php if (!empty($latestPengumuman)): ?>
<div class="modal fade" id="modalPengumuman" tabindex="-1" aria-labelledby="modalPengumumanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header bg-light border-bottom px-4 py-3 align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-primary text-white rounded-3 shadow-sm" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalPengumumanLabel">Pengumuman Resmi <?= sebutan_desa() ?></h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Pemerintah <?= sebutan_desa() ?> <?= esc($desa['nama_desa'] ?? '') ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-4 p-md-5" id="printablePengumuman">
                <!-- Kop Surat Resmi -->
                <div class="text-center pb-3 mb-4" style="border-bottom: 3px double #333;">
                    <div class="row align-items-center">
                        <div class="col-2 text-center">
                            <?php if (!empty($desa['logo_path'])): ?>
                                <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo Desa" class="img-fluid" style="max-height: 70px;">
                            <?php else: ?>
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle border" style="width: 60px; height: 60px;">
                                    <i class="bi bi-shield-check fs-3"></i>
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
                                PEMERINTAH <?= strtoupper(sebutan_desa()) ?> <?= esc(strtoupper($desa['nama_desa'] ?? 'PULO DRIEN')) ?>
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
                    <h5 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.08em; text-decoration: underline;">
                        P E N G U M U M A N
                    </h5>
                    <?php if (!empty($latestPengumuman['nomor_pengumuman'])): ?>
                        <div class="font-monospace text-muted fw-semibold small">
                            Nomor: <?= esc($latestPengumuman['nomor_pengumuman']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                        <?php
                            $sifatColor = \App\Models\PengumumanModel::SIFAT_BADGES[$latestPengumuman['sifat']] ?? 'primary';
                            $sifatIcon = \App\Models\PengumumanModel::SIFAT_ICONS[$latestPengumuman['sifat']] ?? 'bi-info-circle';
                        ?>
                        <span class="badge bg-<?= $sifatColor ?>-subtle text-<?= $sifatColor ?> border border-<?= $sifatColor ?>-subtle px-2 py-1">
                            <i class="bi <?= $sifatIcon ?> me-1"></i>Sifat: <?= esc($latestPengumuman['sifat']) ?>
                        </span>
                        <span class="badge bg-light text-muted border">
                            <i class="bi bi-calendar-event me-1"></i><?= date('d F Y', strtotime($latestPengumuman['published_at'] ?? $latestPengumuman['created_at'])) ?>
                        </span>
                    </div>
                </div>

                <!-- Perihal / Judul -->
                <div class="bg-light p-3 rounded-3 mb-4 text-center border">
                    <h5 class="fw-bold text-dark mb-0"><?= esc($latestPengumuman['judul']) ?></h5>
                </div>

                <!-- Isi Pengumuman -->
                <div class="pengumuman-content mb-5" style="line-height: 1.8; font-size: 1rem; color: #2d3748;">
                    <?php if (strpos($latestPengumuman['konten'], '<p>') !== false || strpos($latestPengumuman['konten'], '<br>') !== false): ?>
                        <?= $latestPengumuman['konten'] ?>
                    <?php else: ?>
                        <?= nl2br(esc($latestPengumuman['konten'])) ?>
                    <?php endif; ?>
                </div>

                <!-- Lampiran Jika Ada -->
                <?php if (!empty($latestPengumuman['lampiran_path'])): ?>
                    <div class="card border-0 bg-primary-subtle bg-opacity-10 border border-primary-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-white text-primary rounded-3 me-3 shadow-sm" style="width: 42px; height: 42px;">
                                    <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= esc($latestPengumuman['lampiran_nama'] ?? 'Dokumen Surat Resmi') ?></h6>
                                    <small class="text-muted">Berkas resmi bertanda tangan dan berstempel gampong/desa.</small>
                                </div>
                            </div>
                            <a href="<?= base_url($latestPengumuman['lampiran_path']) ?>" target="_blank" download class="btn btn-sm btn-primary shadow-sm px-3">
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
                            <?= esc($desa['nama_desa'] ?? 'Gampong') ?>, <?= date('d F Y', strtotime($latestPengumuman['published_at'] ?? $latestPengumuman['created_at'])) ?>
                        </small>
                        <div class="fw-bold text-dark text-uppercase small"><?= sebutan_kades() ?> <?= esc($desa['nama_desa'] ?? '') ?></div>
                        <div style="height: 65px;" class="d-flex align-items-center justify-content-center">
                            <!-- Cap / Stempel Digital atau TTD -->
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">[ TTD Resmi ]</span>
                        </div>
                        <div class="fw-bold text-dark text-decoration-underline small text-uppercase">
                            <?= esc($desa['nama_kepala_desa'] ?? 'Pemerintah ' . sebutan_desa()) ?>
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
                    <a href="<?= base_url('warga/pengumuman') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-archive me-1"></i> Arsip Pengumuman
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <?php
                        $waText = "📢 *PENGUMUMAN RESMI " . strtoupper(sebutan_desa()) . " " . strtoupper($desa['nama_desa'] ?? '') . "*\n\n"
                                . "*" . $latestPengumuman['judul'] . "*\n"
                                . (!empty($latestPengumuman['nomor_pengumuman']) ? "No: " . $latestPengumuman['nomor_pengumuman'] . "\n" : "")
                                . "Tanggal: " . date('d F Y', strtotime($latestPengumuman['published_at'] ?? $latestPengumuman['created_at'])) . "\n\n"
                                . strip_tags($latestPengumuman['ringkasan'] ?? '') . "\n\n"
                                . "Info selengkapnya: " . base_url('warga/pengumuman/' . $latestPengumuman['slug']);
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
