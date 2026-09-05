<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h4 fw-bold mb-1">Detail Data Penduduk</h2>
        <p class="text-muted mb-0">Informasi lengkap kependudukan: <?= esc($penduduk['nama_lengkap']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url($baseRoute) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="<?= base_url($baseRoute . '/edit/' . $penduduk['id']) ?>" class="btn btn-warning">
            <i class="bi bi-pencil-square me-1"></i> Edit Data
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- Card Profil Singkat -->
        <div class="card bg-white p-4 text-center mb-4">
            <div class="mb-3">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($penduduk['nama_lengkap']) ?>&background=4f46e5&color=fff&size=128" alt="Avatar" class="rounded-circle shadow-sm" width="100" height="100">
            </div>
            <h5 class="fw-bold mb-1"><?= esc($penduduk['nama_lengkap']) ?></h5>
            <div class="text-muted font-monospace small mb-2">NIK: <?= esc($penduduk['nik']) ?></div>
            <div>
                <?php if ($penduduk['status_penduduk'] === 'Tetap'): ?>
                    <span class="badge bg-success">Warga Tetap</span>
                <?php elseif ($penduduk['status_penduduk'] === 'Sementara'): ?>
                    <span class="badge bg-warning text-dark">Sementara</span>
                <?php elseif ($penduduk['status_penduduk'] === 'Pindah'): ?>
                    <span class="badge bg-secondary">Pindah</span>
                <?php elseif ($penduduk['status_penduduk'] === 'Meninggal'): ?>
                    <span class="badge bg-dark">Meninggal Dunia</span>
                <?php endif; ?>
                <span class="badge bg-primary ms-1"><?= esc($penduduk['status_hubungan_kk'] ?: 'Anggota') ?></span>
            </div>
        </div>

        <!-- Info Tambahan -->
        <div class="card bg-white p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Data</h6>
            <div class="small text-muted mb-2">
                <strong>Terdaftar:</strong><br>
                <?= !empty($penduduk['created_at']) ? date('d F Y, H:i', strtotime($penduduk['created_at'])) : '-' ?>
            </div>
            <div class="small text-muted">
                <strong>Terakhir Diperbarui:</strong><br>
                <?= !empty($penduduk['updated_at']) ? date('d F Y, H:i', strtotime($penduduk['updated_at'])) : '-' ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card bg-white p-4">
            <!-- 1. Biodata Pribadi -->
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-person-lines-fill me-2"></i>1. Biodata Kependudukan
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Nomor Induk Kependudukan (NIK)</span>
                    <span class="fw-semibold font-monospace"><?= esc($penduduk['nik']) ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Nomor Kartu Keluarga (No. KK)</span>
                    <span class="fw-semibold font-monospace"><?= esc($penduduk['no_kk']) ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tempat, Tanggal Lahir</span>
                    <span class="fw-semibold">
                        <?= esc($penduduk['tempat_lahir']) ?>, 
                        <?= !empty($penduduk['tanggal_lahir']) ? date('d F Y', strtotime($penduduk['tanggal_lahir'])) : '-' ?>
                        <?php 
                            if (!empty($penduduk['tanggal_lahir'])) {
                                $age = (new DateTime($penduduk['tanggal_lahir']))->diff(new DateTime())->y;
                                echo "({$age} Tahun)";
                            }
                        ?>
                    </span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Jenis Kelamin</span>
                    <span class="fw-semibold"><?= $penduduk['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Golongan Darah</span>
                    <span class="fw-semibold"><?= esc($penduduk['golongan_darah'] ?: '-') ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Agama</span>
                    <span class="fw-semibold"><?= esc($penduduk['agama']) ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Status Perkawinan</span>
                    <span class="fw-semibold"><?= esc($penduduk['status_perkawinan']) ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Kewarganegaraan</span>
                    <span class="fw-semibold"><?= esc($penduduk['kewarganegaraan'] ?: 'WNI') ?></span>
                </div>
            </div>

            <!-- 2. Pendidikan & Pekerjaan -->
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-briefcase me-2"></i>2. Pendidikan & Pekerjaan
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Pendidikan Terakhir</span>
                    <span class="fw-semibold"><?= esc($penduduk['pendidikan'] ?: '-') ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Pekerjaan</span>
                    <span class="fw-semibold"><?= esc($penduduk['pekerjaan'] ?: '-') ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Hubungan Keluarga (KK)</span>
                    <span class="fw-semibold"><?= esc($penduduk['status_hubungan_kk'] ?: '-') ?></span>
                </div>
            </div>

            <!-- 3. Domisili & Alamat -->
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-geo-alt me-2"></i>3. Alamat Domisili
            </h6>
            <div class="row g-3">
                <div class="col-sm-4">
                    <span class="text-muted small d-block">Dusun</span>
                    <span class="fw-semibold"><?= esc($penduduk['dusun'] ?: '-') ?></span>
                </div>
                <div class="col-sm-4">
                    <span class="text-muted small d-block">RT / RW</span>
                    <span class="fw-semibold">RT <?= esc($penduduk['rt'] ?: '0') ?> / RW <?= esc($penduduk['rw'] ?: '0') ?></span>
                </div>
                <div class="col-sm-4">
                    <span class="text-muted small d-block">Status Keberadaan</span>
                    <span class="fw-semibold"><?= esc($penduduk['status_penduduk']) ?></span>
                </div>
                <div class="col-12">
                    <span class="text-muted small d-block">Alamat Lengkap</span>
                    <p class="fw-semibold mb-0"><?= nl2br(esc($penduduk['alamat_lengkap'] ?: '-')) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
