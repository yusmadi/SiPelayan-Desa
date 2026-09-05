<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    .kop-surat {
        border-bottom: 3px double #000;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .kop-surat h4, .kop-surat h5, .kop-surat h6 {
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pengumuman-body {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #2b2b2b;
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
        <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h2 class="h3 font-weight-bold mb-0">Pratinjau Pengumuman Resmi</h2>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </button>
        <a href="<?= base_url('operator/pengumuman/edit/' . $pengumuman['id']) ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit Pengumuman
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Official Document Sheet -->
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
            <!-- Kop Surat Resmi -->
            <div class="kop-surat text-center position-relative">
                <div class="row align-items-center">
                    <div class="col-2 text-center">
                        <?php if (!empty($desa['logo_path'])): ?>
                            <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo Desa" class="img-fluid" style="max-height: 85px;">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle border" style="width: 75px; height: 75px;">
                                <i class="bi bi-shield-check text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-8 text-center">
                        <h6 class="fw-bold text-secondary mb-1">PEMERINTAH KABUPATEN <?= esc(strtoupper($desa['kabupaten_nama'] ?? 'ACEH UTARA')) ?></h6>
                        <h5 class="fw-bold text-dark mb-1">KECAMATAN <?= esc(strtoupper($desa['nama_kecamatan'] ?? 'SAMUDERA')) ?></h5>
                        <h4 class="fw-bold text-dark mb-1">PEMERINTAH <?= strtoupper(sebutan_desa()) ?> <?= esc(strtoupper($desa['nama_desa'] ?? 'PULO DRIEN')) ?></h4>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">
                            <?= esc($desa['alamat_kantor'] ?? 'Jalan Keuchik No. 1') ?> | Kode Pos: <?= esc($desa['kode_pos'] ?? '24374') ?> | Email: <?= esc($desa['email'] ?? '-') ?>
                        </small>
                    </div>
                    <div class="col-2 text-center">
                        <!-- Space balancing kop surat -->
                        <?php if (!empty($desa['logo_path'])): ?>
                            <img src="<?= base_url($desa['logo_path']) ?>" alt="Logo" class="img-fluid opacity-0" style="max-height: 85px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Header Pengumuman -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.08em; text-decoration: underline;">P E N G U M U M A N</h4>
                <?php if (!empty($pengumuman['nomor_pengumuman'])): ?>
                    <div class="font-monospace text-muted fw-semibold">Nomor: <?= esc($pengumuman['nomor_pengumuman']) ?></div>
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
                        <i class="bi bi-calendar3 me-1"></i><?= !empty($pengumuman['published_at']) ? date('d F Y', strtotime($pengumuman['published_at'])) : date('d F Y', strtotime($pengumuman['created_at'])) ?>
                    </span>
                </div>
            </div>

            <!-- Judul Pengumuman -->
            <div class="text-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold text-dark mb-0"><?= esc($pengumuman['judul']) ?></h4>
            </div>

            <!-- Isi Pengumuman -->
            <div class="pengumuman-body mb-5">
                <?php if (strpos($pengumuman['konten'], '<p>') !== false || strpos($pengumuman['konten'], '<br>') !== false): ?>
                    <?= $pengumuman['konten'] ?>
                <?php else: ?>
                    <?= nl2br(esc($pengumuman['konten'])) ?>
                <?php endif; ?>
            </div>

            <!-- Lampiran Berkas Jika Ada -->
            <?php if (!empty($pengumuman['lampiran_path'])): ?>
                <div class="card bg-light border p-3 mb-5 no-print">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($pengumuman['lampiran_nama'] ?? 'Dokumen Lampiran Resmi') ?></h6>
                                <small class="text-muted">Berkas pendukung / SK / Surat Pengumuman bertanda tangan</small>
                            </div>
                        </div>
                        <a href="<?= base_url($pengumuman['lampiran_path']) ?>" target="_blank" download class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Unduh Lampiran
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tanda Tangan Resmi / Pengesahan -->
            <div class="row mt-4">
                <div class="col-7"></div>
                <div class="col-5 text-center">
                    <div class="small text-muted mb-1"><?= esc($desa['nama_desa'] ?? 'Gampong') ?>, <?= date('d F Y', strtotime($pengumuman['published_at'] ?? $pengumuman['created_at'])) ?></div>
                    <div class="fw-bold text-dark text-uppercase"><?= sebutan_kades() ?> <?= esc($desa['nama_desa'] ?? '') ?></div>
                    <div style="height: 75px;"></div>
                    <div class="fw-bold text-dark text-decoration-underline text-uppercase"><?= esc($desa['nama_kepala_desa'] ?? 'Pemerintah Gampong') ?></div>
                    <?php if (!empty($desa['nip_kepala_desa'])): ?>
                        <div class="small text-muted font-monospace">NIP. <?= esc($desa['nip_kepala_desa']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
