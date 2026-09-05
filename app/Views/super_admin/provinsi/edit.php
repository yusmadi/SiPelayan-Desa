<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/provinsi') ?>" class="text-decoration-none">Master Provinsi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Provinsi</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0">Edit Provinsi: <?= esc($provinsi['nama']) ?></h2>
    </div>
    <a href="<?= base_url('super-admin/provinsi') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if(session()->getFlashdata('validation') && session()->getFlashdata('validation')->getErrors()): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat kesalahan:</h6>
        <ul class="mb-0 ps-3">
            <?php foreach(session()->getFlashdata('validation')->getErrors() as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card bg-white p-4 shadow-sm border-0">
            <form action="<?= base_url('super-admin/provinsi/edit/' . $provinsi['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Kemendagri <span class="text-danger">*</span></label>
                    <input type="text" name="kode_kemendagri" class="form-control <?= (isset($validation) && $validation->hasError('kode_kemendagri')) ? 'is-invalid' : '' ?>" placeholder="Contoh: 33" value="<?= old('kode_kemendagri', $provinsi['kode_kemendagri']) ?>" required>
                    <small class="text-muted">Kode 2 digit provinsi sesuai standar Kemendagri.</small>
                    <div class="invalid-feedback"><?= $validation->getError('kode_kemendagri') ?? '' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Provinsi <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control <?= (isset($validation) && $validation->hasError('nama')) ? 'is-invalid' : '' ?>" placeholder="Contoh: Jawa Tengah" value="<?= old('nama', $provinsi['nama']) ?>" required>
                    <div class="invalid-feedback"><?= $validation->getError('nama') ?? '' ?></div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= (old('is_active', $provinsi['is_active']) == '1') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="isActive">Status Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold flex-grow-1">
                        <i class="bi bi-save me-1"></i> Update Provinsi
                    </button>
                    <a href="<?= base_url('super-admin/provinsi') ?>" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
