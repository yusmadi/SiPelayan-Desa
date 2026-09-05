<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1"><i class="bi bi-building-gear me-2 text-primary"></i>Pengaturan & Profil <?= sebutan_desa() ?></h2>
        <p class="text-muted mb-0">Kelola identitas <?= strtolower(sebutan_desa()) ?>, logo resmi, kontak, dan informasi pemerintahan <?= strtolower(sebutan_desa()) ?>.</p>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin-desa/profil') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Informasi Desa & Wilayah -->
        <div class="col-lg-7">
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-geo-alt me-2"></i>Informasi Umum <?= sebutan_desa() ?>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Kabupaten</label>
                        <input type="text" class="form-control bg-light" value="<?= esc($desa['nama_kabupaten'] ?? '-') ?>" readonly disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Kecamatan</label>
                        <input type="text" class="form-control bg-light" value="<?= esc($desa['nama_kecamatan'] ?? '-') ?>" readonly disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Nama <?= sebutan_desa() ?></label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" value="<?= esc($desa['nama_desa'] ?? '-') ?>" readonly disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Kode Kemendagri</label>
                        <input type="text" class="form-control bg-light font-monospace" value="<?= esc($desa['kode_kemendagri'] ?? '-') ?>" readonly disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Kantor <?= sebutan_desa() ?></label>
                        <textarea name="alamat_kantor" class="form-control" rows="2" placeholder="Jl. Raya..."><?= old('alamat_kantor', $desa['alamat_kantor']) ?></textarea>
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
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-card-text me-2"></i>Visi & Misi <?= sebutan_desa() ?>
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Visi <?= sebutan_desa() ?></label>
                    <textarea name="visi" class="form-control" rows="2" placeholder="Visi..."><?= old('visi', $desa['visi']) ?></textarea>
                </div>
                <div>
                    <label class="form-label fw-semibold">Misi <?= sebutan_desa() ?></label>
                    <textarea name="misi" class="form-control" rows="3" placeholder="Misi..."><?= old('misi', $desa['misi']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Logo & Kontak -->
        <div class="col-lg-5">
            <!-- Card Upload Logo Desa -->
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-image me-2"></i>Logo Resmi <?= sebutan_desa() ?> (Kop Surat)
                </h5>

                <div class="mb-3 text-center p-3 bg-light rounded border">
                    <?php 
                        $logoUrl = !empty($desa['logo_path']) 
                            ? (str_starts_with($desa['logo_path'], 'http') ? $desa['logo_path'] : base_url($desa['logo_path'])) 
                            : 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Coat_of_arms_of_Indonesia.svg/800px-Coat_of_arms_of_Indonesia.svg.png';
                    ?>
                    <img src="<?= esc($logoUrl) ?>" alt="Logo <?= sebutan_desa() ?>" style="max-height: 100px; max-width: 100px; object-fit: contain;">
                    <small class="d-block text-muted mt-2">
                        <?= !empty($desa['logo_path']) ? ('Logo ' . sebutan_desa() . ' Terpasang') : 'Logo Default (Garuda Pancasila)' ?>
                    </small>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold"><?= !empty($desa['logo_path']) ? ('Ganti File Logo ' . sebutan_desa()) : ('Upload File Logo ' . sebutan_desa()) ?></label>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml">
                    <small class="text-muted">Format: PNG, JPG, atau SVG (Maksimal 2MB). Disarankan format PNG transparan untuk tampilan kop surat terbaik.</small>
                </div>
            </div>

            <!-- Card Pemerintahan -->
            <div class="card bg-white p-4 shadow-sm border-0 mb-4">
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                    <i class="bi bi-person-badge me-2"></i>Pemerintahan & Kontak
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama <?= sebutan_kades() ?></label>
                    <input type="text" name="nama_kepala_desa" class="form-control" placeholder="Nama Lengkap <?= sebutan_kades() ?>" value="<?= old('nama_kepala_desa', $desa['nama_kepala_desa']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP <?= sebutan_kades() ?></label>
                    <input type="text" name="nip_kepala_desa" class="form-control" placeholder="NIP (Kosongkan jika Non-ASN)" value="<?= old('nip_kepala_desa', $desa['nip_kepala_desa']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">WhatsApp <?= sebutan_kades() ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="text" name="whatsapp_kades" class="form-control" placeholder="6281234567890" value="<?= old('whatsapp_kades', $desa['whatsapp_kades']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Telepon Kantor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="telepon" class="form-control" placeholder="0281-xxxxxx" value="<?= old('telepon', $desa['telepon']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Kantor <?= sebutan_desa() ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="kantor@desa.go.id" value="<?= old('email', $desa['email']) ?>">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card bg-white p-3 shadow-sm border-0">
                <button type="submit" class="btn btn-primary py-2 fw-semibold w-100 shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan Profil & Logo
                </button>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
