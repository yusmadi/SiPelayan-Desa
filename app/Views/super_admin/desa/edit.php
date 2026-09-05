<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('super-admin/desa') ?>" class="text-decoration-none">Master Desa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Desa</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0">Edit Desa: <?= esc($desa['nama_desa']) ?></h2>
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

<form action="<?= base_url('super-admin/desa/edit/' . $desa['id']) ?>" method="post" enctype="multipart/form-data">
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
                                <option value="<?= $kab['id'] ?>" <?= (old('kabupaten_id', $desa['kabupaten_id']) == $kab['id']) ? 'selected' : '' ?>>
                                    <?= esc($kab['nama']) ?> (<?= esc($kab['provinsi']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $validation->getError('kabupaten_id') ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Kemendagri <span class="text-danger">*</span></label>
                        <input type="text" name="kode_kemendagri" class="form-control <?= (isset($validation) && $validation->hasError('kode_kemendagri')) ? 'is-invalid' : '' ?>" placeholder="Contoh: 33.02.01.2001" value="<?= old('kode_kemendagri', $desa['kode_kemendagri']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->getError('kode_kemendagri') ?? '' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kecamatan" list="listKecamatan" class="form-control <?= (isset($validation) && $validation->hasError('nama_kecamatan')) ? 'is-invalid' : '' ?>" placeholder="Pilih / ketik nama kecamatan" value="<?= old('nama_kecamatan', $desa['nama_kecamatan']) ?>" required>
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
                        <input type="text" id="nama_desa" name="nama_desa" class="form-control <?= (isset($validation) && $validation->hasError('nama_desa')) ? 'is-invalid' : '' ?>" placeholder="Contoh: Sukamaju" value="<?= old('nama_desa', $desa['nama_desa']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->getError('nama_desa') ?? '' ?></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Tenant Slug (URL Desa) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><?= base_url() ?></span>
                            <input type="text" id="tenant_slug" name="tenant_slug" class="form-control <?= (isset($validation) && $validation->hasError('tenant_slug')) ? 'is-invalid' : '' ?>" placeholder="sukamaju" value="<?= old('tenant_slug', $desa['tenant_slug']) ?>" required>
                        </div>
                        <small class="text-muted">Portal publik desa dapat diakses melalui link <code>/<?= esc($desa['tenant_slug']) ?></code></small>
                        <div class="invalid-feedback d-block"><?= $validation->getError('tenant_slug') ?? '' ?></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Kantor Desa</label>
                        <textarea name="alamat_kantor" class="form-control" rows="2" placeholder="Jl. Raya Desa No..."><?= old('alamat_kantor', $desa['alamat_kantor']) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control" placeholder="53181" value="<?= old('kode_pos', $desa['kode_pos']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Website Resmi</label>
                        <input type="text" name="website" class="form-control" placeholder="https://..." value="<?= old('website', $desa['website']) ?>">
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
                    <textarea name="visi" class="form-control" rows="2" placeholder="Visi Desa..."><?= old('visi', $desa['visi']) ?></textarea>
                </div>
                <div>
                    <label class="form-label fw-semibold">Misi</label>
                    <textarea name="misi" class="form-control" rows="3" placeholder="Misi Desa..."><?= old('misi', $desa['misi']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Pemerintahan & Kontak -->
        <div class="col-lg-5">
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-person-badge me-2"></i>Pemerintahan & <?= sebutan_kades($desa['id']) ?>
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama <?= sebutan_kades($desa['id']) ?></label>
                    <input type="text" name="nama_kepala_desa" class="form-control" placeholder="Nama Lengkap <?= sebutan_kades($desa['id']) ?>" value="<?= old('nama_kepala_desa', $desa['nama_kepala_desa']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP <?= sebutan_kades($desa['id']) ?></label>
                    <input type="text" name="nip_kepala_desa" class="form-control" placeholder="NIP jika ASN / Kosongkan jika Non-ASN" value="<?= old('nip_kepala_desa', $desa['nip_kepala_desa']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">WhatsApp <?= sebutan_kades($desa['id']) ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="text" name="whatsapp_kades" class="form-control" placeholder="6281234567890" value="<?= old('whatsapp_kades', $desa['whatsapp_kades']) ?>">
                    </div>
                    <small class="text-muted">Untuk fitur konsultasi "Tanya <?= sebutan_kades($desa['id']) ?>" oleh warga.</small>
                </div>
            </div>

            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-image me-2"></i>Logo Resmi <?= sebutan_desa($desa['id']) ?>
                </h5>

                <?php if(!empty($desa['logo_path'])): ?>
                    <div class="mb-3 text-center p-3 bg-light rounded border">
                        <img src="<?= (str_starts_with($desa['logo_path'], 'http') ? $desa['logo_path'] : base_url($desa['logo_path'])) ?>" alt="Logo Desa" style="max-height: 90px; max-width: 90px; object-fit: contain;">
                        <small class="d-block text-muted mt-2">Logo Desa Saat Ini</small>
                    </div>
                <?php endif; ?>

                <div class="mb-2">
                    <label class="form-label fw-semibold"><?= !empty($desa['logo_path']) ? 'Ganti File Logo Desa' : 'Upload File Logo Desa' ?></label>
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
                        <input type="text" name="telepon" class="form-control" placeholder="0281-xxxxxx" value="<?= old('telepon', $desa['telepon']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Kantor Desa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" placeholder="desa@sukamaju.go.id" value="<?= old('email', $desa['email']) ?>">
                    </div>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?? '' ?></div>
                </div>

                <div class="form-check form-switch mt-3 pt-2 border-top">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" <?= (old('is_active', $desa['is_active']) == '1') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="isActiveSwitch">Aktifkan Status Desa</label>
                    <small class="d-block text-muted">Desa aktif dapat diakses warga dan login oleh aparatur desa.</small>
                </div>
            </div>

            <div class="card bg-white p-3 shadow-sm border-0">
                <button type="submit" class="btn btn-primary py-2 fw-semibold w-100 mb-2">
                    <i class="bi bi-save me-1"></i> Update Data Desa
                </button>
                <a href="<?= base_url('super-admin/desa') ?>" class="btn btn-light w-100 text-muted">Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
