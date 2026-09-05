<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Tambah Data Penduduk</h2>
        <p class="text-muted mb-0">Masukkan data lengkap penduduk warga Desa <?= esc(session('nama_desa')) ?>.</p>
    </div>
    <a href="<?= base_url($baseRoute) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4">
        <i class="bi bi-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card bg-white p-4 shadow-sm">
    <form action="<?= base_url($baseRoute . '/tambah') ?>" method="post">
        <?= csrf_field() ?>

        <!-- SECTION 1: IDENTITAS UTAMA -->
        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
            <i class="bi bi-person-badge me-2"></i>1. Identitas Pribadi
        </h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                <input type="text" name="nik" maxlength="16" class="form-control <?= ($validation && $validation->hasError('nik')) ? 'is-invalid' : '' ?>" placeholder="16 Digit NIK KTP" value="<?= old('nik') ?>" required autofocus>
                <?php if ($validation && $validation->hasError('nik')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">No. Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                <input type="text" name="no_kk" maxlength="16" class="form-control <?= ($validation && $validation->hasError('no_kk')) ? 'is-invalid' : '' ?>" placeholder="16 Digit Nomor KK" value="<?= old('no_kk') ?>" required>
                <?php if ($validation && $validation->hasError('no_kk')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('no_kk') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control <?= ($validation && $validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" placeholder="Nama lengkap tanpa singkatan" value="<?= old('nama_lengkap') ?>" required>
                <?php if ($validation && $validation->hasError('nama_lengkap')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('nama_lengkap') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tempat_lahir" class="form-control <?= ($validation && $validation->hasError('tempat_lahir')) ? 'is-invalid' : '' ?>" placeholder="Kota/Kab Tempat Lahir" value="<?= old('tempat_lahir') ?>" required>
                <?php if ($validation && $validation->hasError('tempat_lahir')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('tempat_lahir') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lahir" class="form-control <?= ($validation && $validation->hasError('tanggal_lahir')) ? 'is-invalid' : '' ?>" value="<?= old('tanggal_lahir') ?>" required>
                <?php if ($validation && $validation->hasError('tanggal_lahir')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('tanggal_lahir') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select <?= ($validation && $validation->hasError('jenis_kelamin')) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
                <?php if ($validation && $validation->hasError('jenis_kelamin')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('jenis_kelamin') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Golongan Darah</label>
                <select name="golongan_darah" class="form-select">
                    <option value="">-- Tidak Diketahui --</option>
                    <?php foreach(['A', 'B', 'AB', 'O'] as $goldar): ?>
                        <option value="<?= $goldar ?>" <?= old('golongan_darah') === $goldar ? 'selected' : '' ?>><?= $goldar ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Agama <span class="text-danger">*</span></label>
                <select name="agama" class="form-select <?= ($validation && $validation->hasError('agama')) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Pilih Agama --</option>
                    <?php foreach(['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agm): ?>
                        <option value="<?= $agm ?>" <?= old('agama') === $agm ? 'selected' : '' ?>><?= $agm ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($validation && $validation->hasError('agama')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('agama') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status Perkawinan <span class="text-danger">*</span></label>
                <select name="status_perkawinan" class="form-select <?= ($validation && $validation->hasError('status_perkawinan')) ? 'is-invalid' : '' ?>" required>
                    <?php foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp): ?>
                        <option value="<?= $sp ?>" <?= old('status_perkawinan') === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- SECTION 2: STATUS & SOSIAL -->
        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
            <i class="bi bi-briefcase me-2"></i>2. Status Sosial & Keluarga
        </h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Hubungan dalam Keluarga</label>
                <select name="status_hubungan_kk" class="form-select">
                    <option value="">-- Pilih Hubungan --</option>
                    <?php foreach(['Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya'] as $hub): ?>
                        <option value="<?= $hub ?>" <?= old('status_hubungan_kk') === $hub ? 'selected' : '' ?>><?= $hub ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Pendidikan Terakhir</label>
                <select name="pendidikan" class="form-select">
                    <option value="">-- Pilih Pendidikan --</option>
                    <?php foreach(['Tidak Sekolah', 'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $pddk): ?>
                        <option value="<?= $pddk ?>" <?= old('pendidikan') === $pddk ? 'selected' : '' ?>><?= $pddk ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Pekerjaan</label>
                <select name="pekerjaan" id="pekerjaan" class="form-select" placeholder="Cari atau pilih pekerjaan...">
                    <option value="">-- Cari atau Pilih Pekerjaan --</option>
                    <?php if (!empty($listPekerjaan)): ?>
                        <?php foreach ($listPekerjaan as $pek): ?>
                            <option value="<?= esc($pek['nama']) ?>" <?= old('pekerjaan') === $pek['nama'] ? 'selected' : '' ?>>
                                <?= esc($pek['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div class="form-text text-muted" style="font-size: 0.78rem;">Ketik untuk mencari pekerjaan dengan cepat.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kewarganegaraan</label>
                <select name="kewarganegaraan" class="form-select">
                    <option value="WNI" <?= old('kewarganegaraan', 'WNI') === 'WNI' ? 'selected' : '' ?>>WNI (Warga Negara Indonesia)</option>
                    <option value="WNA" <?= old('kewarganegaraan') === 'WNA' ? 'selected' : '' ?>>WNA (Warga Negara Asing)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status Keberadaan Penduduk <span class="text-danger">*</span></label>
                <select name="status_penduduk" class="form-select" required>
                    <option value="Tetap" <?= old('status_penduduk') === 'Tetap' ? 'selected' : '' ?>>Tetap (Warga Asli)</option>
                    <option value="Sementara" <?= old('status_penduduk') === 'Sementara' ? 'selected' : '' ?>>Sementara (Pendatang / Kos)</option>
                    <option value="Pindah" <?= old('status_penduduk') === 'Pindah' ? 'selected' : '' ?>>Pindah Keluar</option>
                    <option value="Meninggal" <?= old('status_penduduk') === 'Meninggal' ? 'selected' : '' ?>>Meninggal Dunia</option>
                </select>
            </div>
        </div>

        <!-- SECTION 3: ALAMAT & DOMISILI -->
        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
            <i class="bi bi-geo-alt me-2"></i>3. Alamat & Wilayah Domisili
        </h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Dusun / Lingkungan</label>
                <input type="text" name="dusun" class="form-control" placeholder="Nama Dusun / Kampung" value="<?= old('dusun') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">RT</label>
                <input type="text" name="rt" maxlength="5" class="form-control" placeholder="001" value="<?= old('rt') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">RW</label>
                <input type="text" name="rw" maxlength="5" class="form-control" placeholder="001" value="<?= old('rw') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alamat Lengkap (Jalan, No Rumah, Patokan)</label>
                <textarea name="alamat_lengkap" class="form-control" rows="2" placeholder="Jl. Mawar No. 10..."><?= old('alamat_lengkap') ?></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="<?= base_url($baseRoute) ?>" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data Penduduk</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Tom Select CSS (Bootstrap 5) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper .ts-control {
        border-radius: 0.375rem !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 1rem;
    }
    .ts-dropdown {
        border-radius: 0.375rem !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        font-size: 0.95rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#pekerjaan', {
            create: true,
            placeholder: '-- Cari atau Pilih Pekerjaan --',
            allowEmptyOption: true,
            maxOptions: 200,
            sortField: {
                field: 'text',
                direction: 'asc'
            }
        });
    });
</script>
<?= $this->endSection() ?>
