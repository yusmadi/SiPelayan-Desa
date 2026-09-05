<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/desa') ?>" class="text-decoration-none">Master Desa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Desa</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0">Tambah Data Desa Baru</h2>
    </div>
    <a href="<?= base_url('super-admin/desa') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if(session()->getFlashdata('validation') && session()->getFlashdata('validation')->getErrors()): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat kesalahan pengisian formulir:</h6>
        <ul class="mb-0 ps-3">
            <?php foreach(session()->getFlashdata('validation')->getErrors() as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('super-admin/desa/tambah') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Wilayah & Identitas Desa -->
        <div class="col-lg-7">
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-geo-alt me-2"></i>Informasi Wilayah & Tenant
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kabupaten <span class="text-danger">*</span></label>
                        <select name="kabupaten_id" class="form-select <?= (isset($validation) && $validation->hasError('kabupaten_id')) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Kabupaten --</option>
                            <?php foreach($kabupatenList as $kab): ?>
                                <option value="<?= $kab['id'] ?>" <?= (old('kabupaten_id') == $kab['id']) ? 'selected' : '' ?>>
                                    <?= esc($kab['nama']) ?> (<?= esc($kab['provinsi']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $validation->getError('kabupaten_id') ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Kemendagri <span class="text-danger">*</span></label>
                        <input type="text" name="kode_kemendagri" class="form-control <?= (isset($validation) && $validation->hasError('kode_kemendagri')) ? 'is-invalid' : '' ?>" placeholder="Contoh: 33.02.01.2001" value="<?= old('kode_kemendagri') ?>" required>
                        <div class="invalid-feedback"><?= $validation->getError('kode_kemendagri') ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kecamatan" list="listKecamatan" class="form-control <?= (isset($validation) && $validation->hasError('nama_kecamatan')) ? 'is-invalid' : '' ?>" placeholder="Pilih / ketik nama kecamatan" value="<?= old('nama_kecamatan') ?>" required>
                        <datalist id="listKecamatan">
                            <?php if(!empty($kecamatanList)): ?>
                                <?php foreach($kecamatanList as $kc): ?>
                                    <option value="<?= esc($kc['nama']) ?>"><?= esc($kc['kode_kemendagri']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                        <div class="invalid-feedback"><?= $validation->getError('nama_kecamatan') ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Desa <span class="text-danger">*</span></label>
                        <input type="text" id="nama_desa" name="nama_desa" class="form-control <?= (isset($validation) && $validation->hasError('nama_desa')) ? 'is-invalid' : '' ?>" placeholder="Contoh: Sukamaju" value="<?= old('nama_desa') ?>" required>
                        <div class="invalid-feedback"><?= $validation->getError('nama_desa') ?? '' ?></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Tenant Slug (URL Desa) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><?= base_url() ?></span>
                            <input type="text" id="tenant_slug" name="tenant_slug" class="form-control <?= (isset($validation) && $validation->hasError('tenant_slug')) ? 'is-invalid' : '' ?>" placeholder="sukamaju" value="<?= old('tenant_slug') ?>" required>
                        </div>
                        <small class="text-muted">Digunakan untuk akses portal publik desa, contoh: <code>/sukamaju</code></small>
                        <div class="invalid-feedback d-block"><?= $validation->getError('tenant_slug') ?? '' ?></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Kantor Desa</label>
                        <textarea name="alamat_kantor" class="form-control" rows="2" placeholder="Jl. Raya Desa No..."><?= old('alamat_kantor') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control" placeholder="53181" value="<?= old('kode_pos') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Website Resmi</label>
                        <input type="text" name="website" class="form-control" placeholder="https://..." value="<?= old('website') ?>">
                    </div>
                </div>
            </div>

            <!-- Visi & Misi -->
            <div class="card bg-white p-4 shadow-sm border-0">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-card-text me-2"></i>Visi & Misi Desa
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Visi</label>
                    <textarea name="visi" class="form-control" rows="2" placeholder="Visi Desa..."><?= old('visi') ?></textarea>
                </div>
                <div>
                    <label class="form-label fw-semibold">Misi</label>
                    <textarea name="misi" class="form-control" rows="3" placeholder="Misi Desa..."><?= old('misi') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Pemerintahan & Kontak -->
        <div class="col-lg-5">
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-person-badge me-2"></i>Pemerintahan & Kades
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kepala Desa</label>
                    <input type="text" name="nama_kepala_desa" class="form-control" placeholder="Nama Lengkap Kades" value="<?= old('nama_kepala_desa') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP Kepala Desa</label>
                    <input type="text" name="nip_kepala_desa" class="form-control" placeholder="NIP jika ASN / Kosongkan jika Non-ASN" value="<?= old('nip_kepala_desa') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">WhatsApp Kepala Desa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="text" name="whatsapp_kades" class="form-control" placeholder="6281234567890" value="<?= old('whatsapp_kades') ?>">
                    </div>
                    <small class="text-muted">Untuk fitur konsultasi "Tanya Kades" oleh warga.</small>
                </div>
            </div>

            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-image me-2"></i>Logo Resmi Desa
                </h5>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Upload File Logo Desa</label>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml">
                    <small class="text-muted">Format: PNG, JPG, atau SVG (Maksimal 2MB). Disarankan berlatar belakang transparan.</small>
                </div>
            </div>

            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-telephone me-2"></i>Kontak Kantor
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Telepon Kantor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="telepon" class="form-control" placeholder="0281-xxxxxx" value="<?= old('telepon') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Kantor Desa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" placeholder="desa@sukamaju.go.id" value="<?= old('email') ?>">
                    </div>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?? '' ?></div>
                </div>

                <div class="form-check form-switch mt-3 pt-2 border-top">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" <?= (old('is_active', '1') == '1') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="isActiveSwitch">Aktifkan Status Desa</label>
                    <small class="d-block text-muted">Desa aktif dapat diakses warga dan login oleh aparatur desa.</small>
                </div>
            </div>

            <div class="card bg-white p-3 shadow-sm border-0">
                <button type="submit" class="btn btn-primary py-2 fw-semibold w-100 mb-2">
                    <i class="bi bi-save me-1"></i> Simpan Data Desa
                </button>
                <a href="<?= base_url('super-admin/desa') ?>" class="btn btn-light w-100 text-muted">Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaDesaInput = document.getElementById('nama_desa');
    const slugInput = document.getElementById('tenant_slug');

    namaDesaInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-');
        }
    });

    slugInput.addEventListener('input', function() {
        this.dataset.manual = "1";
    });
});
</script>
<?= $this->endSection() ?>
