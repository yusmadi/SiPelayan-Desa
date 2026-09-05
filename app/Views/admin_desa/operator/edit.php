<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Akun Operator <?= sebutan_desa() ?>
        </h2>
        <p class="text-muted mb-0">Perbarui informasi akun operator <?= esc($operator['nama_lengkap']) ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('admin-desa/operator') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white p-4 shadow-sm border-0">
            <form action="<?= base_url('admin-desa/operator/edit/' . $operator['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Lengkap Operator <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control <?= (isset($validation) && $validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" placeholder="Contoh: Budi Santoso, S.Kom" value="<?= old('nama_lengkap', $operator['nama_lengkap']) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('nama_lengkap')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_lengkap') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" placeholder="operator@desa.id" value="<?= old('email', $operator['email']) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control <?= (isset($validation) && $validation->hasError('no_hp')) ? 'is-invalid' : '' ?>" placeholder="08xxxxxxxxxx" value="<?= old('no_hp', $operator['no_hp']) ?>">
                        <?php if (isset($validation) && $validation->hasError('no_hp')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('no_hp') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="nik" class="form-control <?= (isset($validation) && $validation->hasError('nik')) ? 'is-invalid' : '' ?>" placeholder="16 digit NIK" maxlength="16" value="<?= old('nik', $operator['nik']) ?>">
                        <?php if (isset($validation) && $validation->hasError('nik')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-light border p-3 mt-2">
                            <h6 class="fw-semibold mb-1"><i class="bi bi-shield-lock me-1"></i> Ganti Kata Sandi (Opsional)</h6>
                            <p class="small text-muted mb-3">Kosongkan kolom kata sandi jika tidak ingin mengubah kata sandi akun ini.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" placeholder="Minimal 8 karakter">
                                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Ulangi Kata Sandi Baru</label>
                                    <input type="password" name="password_confirm" class="form-control <?= (isset($validation) && $validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" placeholder="Konfirmasi sandi baru">
                                    <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= old('is_active', $operator['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">Status Akun Aktif</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin-desa/operator') ?>" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Perbarui Data Operator
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
