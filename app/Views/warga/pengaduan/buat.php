<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('warga/pengaduan') ?>" class="text-decoration-none text-muted small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Pengaduan
    </a>
    <h2 class="h3 font-weight-bold mt-2 mb-1">Formulir Pengaduan / Aspirasi Warga</h2>
    <p class="text-muted">Laporkan permasalahan fasilitas umum, pelayanan, ketertiban atau sampaikan aspirasi Anda kepada pemerintah <?= strtolower(sebutan_desa()) ?>.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada isian form:</div>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="<?= base_url('warga/pengaduan/buat') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Kategori -->
                    <div class="mb-4">
                        <label for="kategori" class="form-label fw-bold">Kategori Laporan <span class="text-danger">*</span></label>
                        <select name="kategori" id="kategori" class="form-select <?= session('errors.kategori') ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategoriList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= old('kategori') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih bidang atau lingkup dari keluhan/laporan yang diajukan.</div>
                    </div>

                    <!-- Judul -->
                    <div class="mb-4">
                        <label for="judul" class="form-label fw-bold">Judul / Ringkasan Laporan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control <?= session('errors.judul') ? 'is-invalid' : '' ?>" 
                               value="<?= old('judul') ?>" placeholder="Contoh: Lampu Penerangan Jalan Rusak di RT 02 / RW 01" required>
                        <div class="form-text">Tuliskan judul laporan yang singkat, padat, dan jelas.</div>
                    </div>

                    <!-- Isi Laporan -->
                    <div class="mb-4">
                        <label for="isi_laporan" class="form-label fw-bold">Rincian / Isi Pengaduan <span class="text-danger">*</span></label>
                        <textarea name="isi_laporan" id="isi_laporan" rows="6" class="form-control <?= session('errors.isi_laporan') ? 'is-invalid' : '' ?>" 
                                  placeholder="Jelaskan secara mendalam permasalahan yang dialami, waktu kejadian, dampak, dan harapan penanganan..." required><?= old('isi_laporan') ?></textarea>
                    </div>

                    <!-- Lokasi Kejadian -->
                    <div class="mb-4">
                        <label for="lokasi" class="form-label fw-bold">Lokasi Kejadian / Alamat Spesifik</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-danger"></i></span>
                            <input type="text" name="lokasi" id="lokasi" class="form-control <?= session('errors.lokasi') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('lokasi') ?>" placeholder="Contoh: Jl. Mawar No. 12, Depan Pos Ronda RT 03">
                        </div>
                        <div class="form-text">Sebutkan patokan tempat atau alamat jelas agar petugas dapat mengecek ke lokasi.</div>
                    </div>

                    <!-- Upload Foto / Bukti -->
                    <div class="mb-4">
                        <label for="fotos" class="form-label fw-bold">Foto Bukti / Dokumen Pendukung</label>
                        <input type="file" name="fotos[]" id="fotos" class="form-control" multiple accept="image/png, image/jpeg, image/jpg, image/webp">
                        <div class="form-text">Bisa pilih lebih dari 1 foto (JPG, PNG, WEBP, maks 5MB per file). Foto bukti akan mempercepat verifikasi petugas.</div>
                    </div>

                    <!-- Opsi Anonim -->
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_anonymous" name="is_anonymous" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="is_anonymous">
                                Sampaikan Secara Anonim (Rahasiakan Identitas)
                            </label>
                        </div>
                        <div class="small text-muted mt-1">
                            Jika diaktifkan, nama Anda tidak akan ditampilkan ke publik atau petugas pelaksana lapangan, namun status pengaduan tetap dapat dipantau di akun Anda.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="<?= base_url('warga/pengaduan') ?>" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-send me-1"></i> Kirim Pengaduan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Informasi & Panduan -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 shadow-sm bg-primary text-white rounded-3 mb-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-check fs-3 me-2"></i>
                    <h5 class="fw-bold mb-0">Komitmen Layanan</h5>
                </div>
                <p class="small text-white-50 mb-0">
                    Setiap laporan yang masuk akan ditinjau oleh operator dan perangkat <?= sebutan_desa() ?> dalam waktu 1x24 jam kerja. Kami menjaga kerahasiaan data pelapor.
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Alur Penanganan Aduan</h6>
                <ol class="small text-muted ps-3 mb-0 d-flex flex-column gap-2">
                    <li><strong>Aduan Dikirim:</strong> Anda mendapatkan nomor tiket resmi.</li>
                    <li><strong>Verifikasi:</strong> Perangkat <?= sebutan_desa() ?> meninjau relevansi & kelengkapan bukti.</li>
                    <li><strong>Tindak Lanjut:</strong> Petugas lapangan melakukan koordinasi atau perbaikan di lokasi.</li>
                    <li><strong>Selesai:</strong> Tanggapan resmi dan bukti penanganan diunggah ke sistem.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
