<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
    </a>
    <h2 class="h3 font-weight-bold mb-1">Edit Pengumuman Resmi</h2>
    <p class="text-muted mb-0">Perbarui rincian atau berkas lampiran pengumuman resmi <?= sebutan_desa() ?>.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan Input:</h6>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Ubah Data Pengumuman
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('operator/pengumuman/update/' . $pengumuman['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="nomor_pengumuman" class="form-label fw-semibold">Nomor Pengumuman / Surat Edaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control font-monospace" id="nomor_pengumuman" name="nomor_pengumuman" value="<?= old('nomor_pengumuman', $pengumuman['nomor_pengumuman'] ?? '') ?>" placeholder="Contoh: 005/PG-GAMPONG/IX/2026">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="sifat" class="form-label fw-semibold">Sifat / Tingkat Urgensi <span class="text-danger">*</span></label>
                            <select class="form-select" id="sifat" name="sifat" required>
                                <?php foreach ($sifatList as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (old('sifat', $pengumuman['sifat']) === $val) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-6" id="judul" name="judul" value="<?= old('judul', $pengumuman['judul']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan" class="form-label fw-semibold">Ringkasan Singkat</label>
                        <textarea class="form-control" id="ringkasan" name="ringkasan" rows="2"><?= old('ringkasan', $pengumuman['ringkasan']) ?></textarea>
                        <small class="text-muted">Ringkasan yang ditampilkan pada kartu dashboard.</small>
                    </div>

                    <div class="mb-4">
                        <label for="konten" class="form-label fw-semibold">Isi Lengkap Pengumuman <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="konten" name="konten" rows="10" required><?= old('konten', $pengumuman['konten']) ?></textarea>
                    </div>

                    <!-- Lampiran Berkas -->
                    <div class="card bg-light border-0 p-3 mb-4">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-paperclip me-1 text-primary"></i> Lampiran Berkas Resmi</h6>
                        <?php if (!empty($pengumuman['lampiran_path'])): ?>
                            <div class="alert alert-white bg-white border d-flex align-items-center justify-content-between p-2 mb-2">
                                <div class="d-flex align-items-center text-truncate me-2">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                                    <div>
                                        <span class="fw-semibold text-dark d-block"><?= esc($pengumuman['lampiran_nama'] ?? 'Berkas Lampiran') ?></span>
                                        <a href="<?= base_url($pengumuman['lampiran_path']) ?>" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Berkas Saat Ini
                                        </a>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hapus_lampiran" value="1" id="hapus_lampiran">
                                    <label class="form-check-label text-danger small" for="hapus_lampiran">
                                        Hapus Berkas
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <p class="small text-muted mb-2">Unggah lampiran baru untuk mengganti file (PDF / Gambar maks 5 MB):</p>
                        <input type="file" class="form-control" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>

                    <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 p-3 mb-4">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-send-check me-1 text-primary"></i> Status Publikasi</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_published" id="status1" value="1" <?= old('is_published', (string) $pengumuman['is_published']) === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-success" for="status1">
                                <i class="bi bi-broadcast me-1"></i> Terbitkan Sekarang (Dapat dilihat warga)
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_published" id="status0" value="0" <?= old('is_published', (string) $pengumuman['is_published']) === '0' ? 'checked' : '' ?>>
                            <label class="form-check-label text-muted" for="status0">
                                <i class="bi bi-file-earmark-lock me-1"></i> Simpan Sebagai Draft (Belum dipublikasikan)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Data</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Dibuat Oleh:</small>
                    <span class="fw-semibold small"><?= esc($pengumuman['author_nama'] ?? 'Operator') ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Tanggal Dibuat:</small>
                    <span class="small"><?= date('d M Y, H:i', strtotime($pengumuman['created_at'])) ?> WIB</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Terakhir Diperbarui:</small>
                    <span class="small"><?= date('d M Y, H:i', strtotime($pengumuman['updated_at'])) ?> WIB</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Total Dibaca Warga:</small>
                    <span class="badge bg-light text-dark border"><?= (int) $pengumuman['views'] ?> Kali</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
