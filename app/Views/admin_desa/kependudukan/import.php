<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h4 fw-bold mb-1">Import Data Kependudukan</h2>
        <p class="text-muted mb-0">Impor data penduduk secara massal melalui file CSV untuk Desa <?= esc(session('nama_desa')) ?>.</p>
    </div>
    <div>
        <a href="<?= base_url($baseRoute) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>
                <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show p-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
            <div>
                <strong>Perhatian:</strong> <?= session()->getFlashdata('warning') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-x-circle-fill fs-4 me-2"></i>
            <div>
                <strong>Gagal:</strong> <?= session()->getFlashdata('error') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php $summary = session()->getFlashdata('import_summary'); ?>
<?php if ($summary): ?>
    <!-- Rangkuman Hasil Import -->
    <div class="card bg-white p-4 mb-4 border-0 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Rangkuman Hasil Import Terakhir</h5>
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="p-3 bg-light rounded text-center border">
                    <div class="text-muted small">Total Baris Dibaca</div>
                    <div class="fs-4 fw-bold text-dark"><?= number_format($summary['total'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 bg-success-subtle text-success-emphasis rounded text-center border border-success-subtle">
                    <div class="small">Data Baru Masuk</div>
                    <div class="fs-4 fw-bold text-success"><?= number_format($summary['imported'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 bg-info-subtle text-info-emphasis rounded text-center border border-info-subtle">
                    <div class="small">Data Diperbarui</div>
                    <div class="fs-4 fw-bold text-info"><?= number_format($summary['updated'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded text-center border border-warning-subtle">
                    <div class="small">Dilewati / Gagal</div>
                    <div class="fs-4 fw-bold text-warning"><?= number_format($summary['skipped'] ?? 0) ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($summary['errors'])): ?>
            <div class="mt-2">
                <a class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" href="#collapseErrors" role="button">
                    <i class="bi bi-exclamation-octagon me-1"></i> Lihat Catatan Baris Bermasalah (<?= count($summary['errors']) ?> baris)
                </a>
                <div class="collapse mt-3" id="collapseErrors">
                    <div class="card card-body bg-light border-danger-subtle p-3" style="max-height: 250px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0 small text-danger font-monospace">
                            <?php foreach ($summary['errors'] as $err): ?>
                                <li class="mb-1"><i class="bi bi-dot"></i> <?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form Upload File -->
    <div class="col-lg-6">
        <div class="card bg-white p-4 h-100 border-0 shadow-sm">
            <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-cloud-arrow-up me-2"></i>Unggah File Data Penduduk
            </h5>

            <form action="<?= base_url($baseRoute . '/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Pilih File CSV / TXT <span class="text-danger">*</span></label>
                    <input type="file" name="file_import" class="form-control form-control-lg" accept=".csv, .txt" required>
                    <div class="form-text text-muted">
                        Mendukung file format <code>.csv</code> (Comma/Semicolon-Separated) atau <code>.txt</code>. Maksimal 10 MB.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Tindakan Jika NIK Sudah Ada</label>
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="duplicate_action" id="actionSkip" value="skip" checked>
                            <label class="form-check-label" for="actionSkip">
                                <strong>Lewati (Skip)</strong> <span class="badge bg-secondary-subtle text-secondary ms-1">Disarankan</span>
                                <div class="text-muted small">Jangan ubah data lama, abaikan baris yang NIK-nya sudah tercatat di desa ini.</div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="duplicate_action" id="actionUpdate" value="update">
                            <label class="form-check-label" for="actionUpdate">
                                <strong>Perbarui (Update)</strong>
                                <div class="text-muted small">Timpa dan perbarui data penduduk yang ada sesuai isi file import terbaru.</div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-upload me-1"></i> Mulai Proses Import
                    </button>
                    <a href="<?= base_url($baseRoute) ?>" class="btn btn-light px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Panduan & Download Template -->
    <div class="col-lg-6">
        <div class="card bg-white p-4 h-100 border-0 shadow-sm">
            <h5 class="fw-bold text-success mb-3 pb-2 border-bottom">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Panduan & Template Import
            </h5>

            <div class="mb-3">
                <p class="text-muted small mb-3">
                    Gunakan template resmi agar susunan kolom sesuai dengan sistem SiPelayan Desa. Anda dapat membuka dan mengedit file ini di <strong>Microsoft Excel</strong>, <strong>Google Sheets</strong>, atau aplikasi spreadsheet lainnya.
                </p>
                <a href="<?= base_url($baseRoute . '/template-import') ?>" class="btn btn-success w-100 py-2 fw-semibold">
                    <i class="bi bi-download me-2"></i> Download Template CSV Penduduk
                </a>
            </div>

            <div class="mt-4">
                <h6 class="fw-bold text-dark"><i class="bi bi-info-circle me-1 text-primary"></i> Aturan Pengisian Kolom:</h6>
                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                    <li><strong>NIK & No_KK</strong>: Wajib 16 digit angka.</li>
                    <li><strong>Nama_Lengkap</strong>: Nama lengkap warga sesuai KTP (tanpa gelar disarankan).</li>
                    <li><strong>Tanggal_Lahir</strong>: Format <code>YYYY-MM-DD</code> (contoh: <code>1990-05-15</code>) atau <code>DD/MM/YYYY</code>.</li>
                    <li><strong>Jenis_Kelamin</strong>: Isi dengan <code>L</code> (Laki-laki) atau <code>P</code> (Perempuan).</li>
                    <li><strong>Agama</strong>: <code>Islam</code>, <code>Kristen Protestan</code>, <code>Kristen Katolik</code>, <code>Hindu</code>, <code>Buddha</code>, <code>Konghucu</code>, <code>Lainnya</code>.</li>
                    <li><strong>Status_Perkawinan</strong>: <code>Belum Kawin</code>, <code>Kawin</code>, <code>Cerai Hidup</code>, <code>Cerai Mati</code>.</li>
                    <li><strong>Status_Penduduk</strong>: <code>Tetap</code>, <code>Sementara</code>, <code>Pindah</code>, <code>Meninggal</code>.</li>
                    <li><strong>Status_Hubungan_KK</strong>: <code>Kepala Keluarga</code>, <code>Istri</code>, <code>Anak</code>, <code>Menantu</code>, <code>Cucu</code>, <code>Orang Tua</code>, dll.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
