<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
    </a>
    <h2 class="h3 font-weight-bold mb-1">Buat Pengumuman Resmi Baru</h2>
    <p class="text-muted mb-0">Publikasikan informasi kedinasan, surat edaran, atau agenda resmi untuk masyarakat <?= sebutan_desa() ?>.</p>
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
                    <i class="bi bi-file-earmark-ruled text-primary me-2"></i>Formulir Pengumuman Resmi
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('operator/pengumuman/simpan') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="nomor_pengumuman" class="form-label fw-semibold">Nomor Pengumuman / Surat Edaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control font-monospace" id="nomor_pengumuman" name="nomor_pengumuman" value="<?= old('nomor_pengumuman', $suggestedNomor ?? '') ?>" placeholder="Contoh: 005/PG-GAMPONG/IX/2026">
                            </div>
                            <small class="text-muted">Nomor dokumen resmi kantor <?= strtolower(sebutan_desa()) ?>.</small>
                        </div>
                        <div class="col-md-5">
                            <label for="sifat" class="form-label fw-semibold">Sifat / Tingkat Urgensi <span class="text-danger">*</span></label>
                            <select class="form-select" id="sifat" name="sifat" required>
                                <?php foreach ($sifatList as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (old('sifat', 'Biasa') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-6" id="judul" name="judul" value="<?= old('judul') ?>" placeholder="Contoh: Jadwal Pelayanan Posyandu Balita & Lansia Bulan Ini" required>
                        <small class="text-muted">Gunakan judul yang jelas, ringkas, dan mewakili isi pengumuman.</small>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan" class="form-label fw-semibold">Ringkasan Singkat (Opsional)</label>
                        <textarea class="form-control" id="ringkasan" name="ringkasan" rows="2" placeholder="Ringkasan 1-2 kalimat untuk pratinjau di kartu dashboard..."><?= old('ringkasan') ?></textarea>
                        <small class="text-muted">Jika dikosongkan, sistem akan otomatis mengambil ringkasan dari bagian awal isi pengumuman.</small>
                    </div>

                    <div class="mb-4">
                        <label for="konten" class="form-label fw-semibold">Isi Lengkap Pengumuman <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="konten" name="konten" rows="10" placeholder="Tuliskan isi pengumuman lengkap secara terperinci (waktu pelaksanaan, tempat, ketentuan, dll)..." required><?= old('konten') ?></textarea>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i> Mendukung pemisahan paragraf biasa (Enter) atau format HTML sederhana seperti <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>.
                        </div>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-4">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-paperclip me-1 text-primary"></i> Lampiran Berkas Resmi (Opsional)</h6>
                        <p class="small text-muted mb-2">Unggah surat edaran bertanda tangan / berkas pendukung dalam format PDF atau Gambar (Maks 5 MB).</p>
                        <input type="file" class="form-control" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>

                    <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 p-3 mb-4">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-send-check me-1 text-primary"></i> Status Publikasi</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_published" id="status1" value="1" <?= old('is_published', '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-success" for="status1">
                                <i class="bi bi-broadcast me-1"></i> Terbitkan Sekarang (Dapat dilihat warga)
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_published" id="status0" value="0" <?= old('is_published') === '0' ? 'checked' : '' ?>>
                            <label class="form-check-label text-muted" for="status0">
                                <i class="bi bi-file-earmark-lock me-1"></i> Simpan Sebagai Draft (Belum dipublikasikan)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('operator/pengumuman') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Pengumuman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Panduan Penulisan</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2"><strong>Nomor Surat:</strong> Sesuaikan dengan buku agenda surat keluar gampong.</li>
                    <li class="mb-2"><strong>Sifat Urgensi:</strong>
                        <br><span class="badge bg-primary-subtle text-primary">Biasa</span> untuk pengumuman rutin.
                        <br><span class="badge bg-warning-subtle text-warning">Penting</span> untuk jadwal rapat / posyandu.
                        <br><span class="badge bg-danger-subtle text-danger">Segera</span> untuk himbauan cuaca / bansos mendesak.
                    </li>
                    <li class="mb-2"><strong>Bahasa:</strong> Gunakan bahasa resmi, sopan, jelas, dan mudah dipahami warga.</li>
                    <li><strong>Lampiran:</strong> Jika ada surat fisik berkop dan bertanda tangan Keuchik / Kades, sangat disarankan discan dan diunggah.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
