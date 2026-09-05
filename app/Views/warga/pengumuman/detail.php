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
    .konten-pengumuman {
        font-size: 1.05rem;
        line-height: 1.85;
        color: #2b2f38;
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
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 no-print">
    <div>
        <a href="<?= base_url('warga/pengumuman') ?>" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
        </a>
        <h2 class="h3 font-weight-bold mb-0">Dokumen Pengumuman Resmi</h2>
    </div>
    <div class="d-flex gap-2">
        <?php
            $waText = "📢 *PENGUMUMAN RESMI " . strtoupper(sebutan_desa()) . " " . strtoupper($desa['nama_desa'] ?? '') . "*\n\n"
                    . "*" . $pengumuman['judul'] . "*\n"
                    . (!empty($pengumuman['nomor_pengumuman']) ? "No: " . $pengumuman['nomor_pengumuman'] . "\n" : "")
                    . "Tanggal: " . date('d F Y', strtotime($pengumuman['published_at'] ?? $pengumuman['created_at'])) . "\n\n"
                    . strip_tags($pengumuman['ringkasan'] ?? '') . "\n\n"
                    . "Baca selengkapnya di: " . current_url();
            $waUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waText);
        ?>
        <a href="<?= $waUrl ?>" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-whatsapp me-1"></i> Bagikan ke Warga
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
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
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle border" style="width: 70px; height: 70px;">
                                <i class="bi bi-shield-check fs-2"></i>
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
                            PEMERINTAH <?= strtoupper(sebutan_desa()) ?> <?= esc(strtoupper($desa['nama_desa'] ?? 'PULO DRIEN')) ?>
                        </h4>
                        <small class="text-muted d-block" style="font-size: 0.76rem;">
                            <?= esc($desa['alamat_kantor'] ?? 'Kantor Keuchik') ?> <?= !empty($desa['kode_pos']) ? '| Kode Pos: ' . esc($desa['kode_pos']) : '' ?> <?= !empty($desa['email']) ? '| Email: ' . esc($desa['email']) : '' ?>
                        </small>
                    </div>
                    <div class="col-2 text-center">
                        <?php if (!empty($desa['logo_path'])): ?>
                            <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo" class="img-fluid opacity-0" style="max-height: 80px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Judul & Nomor Dokumen -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.08em; text-decoration: underline;">
                    P E N G U M U M A N
                </h4>
                <?php if (!empty($pengumuman['nomor_pengumuman'])): ?>
                    <div class="font-monospace text-muted fw-semibold">
                        Nomor: <?= esc($pengumuman['nomor_pengumuman']) ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                    <?php
                        $sifatColor = \App\Models\PengumumanModel::SIFAT_BADGES[$pengumuman['sifat']] ?? 'primary';
                        $sifatIcon = \App\Models\PengumumanModel::SIFAT_ICONS[$pengumuman['sifat']] ?? 'bi-info-circle';
                    ?>
                    <span class="badge bg-<?= $sifatColor ?>-subtle text-<?= $sifatColor ?> border border-<?= $sifatColor ?>-subtle px-2 py-1">
                        <i class="bi <?= $sifatIcon ?> me-1"></i>Sifat: <?= esc($pengumuman['sifat']) ?>
                    </span>
                    <span class="badge bg-light text-muted border">
                        <i class="bi bi-calendar-check me-1"></i><?= date('d F Y', strtotime($pengumuman['published_at'] ?? $pengumuman['created_at'])) ?>
                    </span>
                </div>
            </div>

            <!-- Header Perihal -->
            <div class="bg-light p-3 rounded-3 mb-4 text-center border">
                <h4 class="fw-bold text-dark mb-0"><?= esc($pengumuman['judul']) ?></h4>
            </div>

            <!-- Isi Pengumuman -->
            <div class="konten-pengumuman mb-5">
                <?php if (strpos($pengumuman['konten'], '<p>') !== false || strpos($pengumuman['konten'], '<br>') !== false): ?>
                    <?= $pengumuman['konten'] ?>
                <?php else: ?>
                    <?= nl2br(esc($pengumuman['konten'])) ?>
                <?php endif; ?>
            </div>

            <!-- Lampiran Jika Ada -->
            <?php if (!empty($pengumuman['lampiran_path'])): ?>
                <div class="card border-0 bg-primary-subtle bg-opacity-10 border border-primary-subtle rounded-3 p-3 mb-5 no-print">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-white text-danger rounded-3 me-3 shadow-sm" style="width: 44px; height: 44px;">
                                <i class="bi bi-file-earmark-pdf fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($pengumuman['lampiran_nama'] ?? 'Dokumen Surat Resmi') ?></h6>
                                <small class="text-muted">Berkas resmi bertanda tangan dan berstempel gampong/desa.</small>
                            </div>
                        </div>
                        <a href="<?= base_url($pengumuman['lampiran_path']) ?>" target="_blank" download class="btn btn-primary shadow-sm px-3">
                            <i class="bi bi-download me-1"></i> Unduh Berkas Lampiran
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tanda Tangan Resmi Pengesahan -->
            <div class="row mt-4 pt-2">
                <div class="col-6 col-sm-7"></div>
                <div class="col-6 col-sm-5 text-center">
                    <div class="small text-muted mb-1">
                        <?= esc($desa['nama_desa'] ?? 'Gampong') ?>, <?= date('d F Y', strtotime($pengumuman['published_at'] ?? $pengumuman['created_at'])) ?>
                    </div>
                    <div class="fw-bold text-dark text-uppercase"><?= sebutan_kades() ?> <?= esc($desa['nama_desa'] ?? '') ?></div>
                    <div style="height: 75px;" class="d-flex align-items-center justify-content-center">
                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">[ TTD Resmi ]</span>
                    </div>
                    <div class="fw-bold text-dark text-decoration-underline text-uppercase">
                        <?= esc($desa['nama_kepala_desa'] ?? 'Pemerintah ' . sebutan_desa()) ?>
                    </div>
                    <?php if (!empty($desa['nip_kepala_desa'])): ?>
                        <div class="small text-muted font-monospace">NIP. <?= esc($desa['nip_kepala_desa']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
