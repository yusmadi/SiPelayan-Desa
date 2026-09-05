<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">
            <i class="bi bi-person-plus-fill text-primary me-2"></i>Buat Akun Operator <?= sebutan_desa() ?>
        </h2>
        <p class="text-muted mb-0">Tambahkan akun operator untuk membantu pelayanan administrasi di <?= esc(session('nama_desa') ?? 'Desa') ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('admin-desa/operator') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Daftar Operator
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white p-4 shadow-sm border-0">
            <div class="alert alert-info border-0 d-flex align-items-center mb-4" style="background-color: #e0f2fe; color: #0369a1;">
                <i class="bi bi-info-circle-fill fs-4 me-3 flex-shrink-0"></i>
                <div class="small">
                    Akun Operator <?= sebutan_desa() ?> memiliki kewenangan operasional untuk:
                    <ul class="mb-0 mt-1 ps-3">
                        <li>Memverifikasi dan memproses permohonan surat warga.</li>
                        <li>Mengelola data kependudukan (warga, keluarga, mutasi).</li>
                        <li>Merespons dan menindaklanjuti pengaduan warga.</li>
                    </ul>
                </div>
            </div>

            <form action="<?= base_url('admin-desa/operator/tambah') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Lengkap Operator <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control <?= (isset($validation) && $validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" placeholder="Contoh: Budi Santoso, S.Kom" value="<?= old('nama_lengkap') ?>" required autofocus>
                        <?php if (isset($validation) && $validation->hasError('nama_lengkap')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_lengkap') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" placeholder="operator@desa.id" value="<?= old('email') ?>" required>
                        <small class="text-muted">Digunakan sebagai username saat login.</small>
                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control <?= (isset($validation) && $validation->hasError('no_hp')) ? 'is-invalid' : '' ?>" placeholder="08xxxxxxxxxx" value="<?= old('no_hp') ?>">
                        <small class="text-muted">Untuk notifikasi dan koordinasi sistem.</small>
                        <?php if (isset($validation) && $validation->hasError('no_hp')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('no_hp') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="nik" class="form-control <?= (isset($validation) && $validation->hasError('nik')) ? 'is-invalid' : '' ?>" placeholder="16 digit NIK jika terdaftar di kependudukan desa" maxlength="16" value="<?= old('nik') ?>">
                        <?php if (isset($validation) && $validation->hasError('nik')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" placeholder="Minimal 8 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', 'togglePassIcon')">
                                <i class="bi bi-eye" id="togglePassIcon"></i>
                            </button>
                            <?php if (isset($validation) && $validation->hasError('password')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control <?= (isset($validation) && $validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirm', 'toggleConfirmIcon')">
                                <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                            </button>
                            <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">Aktifkan akun ini segera</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin-desa/operator') ?>" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-person-check-fill me-1"></i> Simpan & Buat Akun Operator
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
<?= $this->endSection() ?>
