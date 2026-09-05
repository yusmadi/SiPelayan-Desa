<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Edit Pengguna</h2>
        <p class="text-muted mb-0">Perbarui profil, role hak akses, atau password pengguna.</p>
    </div>
    <div>
        <a href="<?= base_url('super-admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white p-4 shadow-sm border-0">
            <form action="<?= base_url('super-admin/users/edit/' . $user['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control <?= (isset($validation) && $validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" placeholder="e.g. John Doe" value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('nama_lengkap')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_lengkap') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" placeholder="user@desa.id" value="<?= old('email', $user['email']) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control <?= (isset($validation) && $validation->hasError('no_hp')) ? 'is-invalid' : '' ?>" placeholder="08xxxxxxxxxx" value="<?= old('no_hp', $user['no_hp']) ?>">
                        <?php if (isset($validation) && $validation->hasError('no_hp')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('no_hp') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role Hak Akses <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-select <?= (isset($validation) && $validation->hasError('role_id')) ? 'is-invalid' : '' ?>" required onchange="toggleDesaRequirement()">
                            <option value="">-- Pilih Role --</option>
                            <?php foreach($roles as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= old('role_id', $user['role_id']) == $r['id'] ? 'selected' : '' ?>>
                                    <?= esc($r['name']) ?> (Level <?= $r['level'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($validation) && $validation->hasError('role_id')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('role_id') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6" id="desa_wrapper">
                        <label class="form-label fw-semibold">Wilayah Desa / Tenant <span id="desa_req_star" class="text-danger">*</span></label>
                        <select name="village_id" id="village_select" class="form-select <?= (isset($validation) && $validation->hasError('village_id')) ? 'is-invalid' : '' ?>">
                            <option value="">-- Ketik untuk mencari desa / kecamatan --</option>
                            <?php if (!empty($selectedDesa)): ?>
                                <option value="<?= esc($selectedDesa['id']) ?>" selected>
                                    Desa <?= esc($selectedDesa['nama_desa']) ?><?= !empty($selectedDesa['nama_kecamatan']) ? ' (Kec. ' . esc($selectedDesa['nama_kecamatan']) . (!empty($selectedDesa['nama_kabupaten']) ? ', ' . esc($selectedDesa['nama_kabupaten']) : '') . ')' : '' ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted" id="desa_help">Wajib dipilih untuk Admin Desa, Operator, dan Warga.</small>
                        <?php if (isset($validation) && $validation->hasError('village_id')): ?>
                            <div class="invalid-feedback d-block"><?= $validation->getError('village_id') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIK KTP (Khusus Warga)</label>
                        <input type="text" name="nik" class="form-control <?= (isset($validation) && $validation->hasError('nik')) ? 'is-invalid' : '' ?>" placeholder="16 digit NIK" maxlength="16" value="<?= old('nik', $user['nik']) ?>">
                        <?php if (isset($validation) && $validation->hasError('nik')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted">Biarkan kosong jika tidak diubah.</small>
                        <?php if (isset($validation) && $validation->hasError('password')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">Status Akun Aktif</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_nik_verified" id="is_nik_verified" value="1" <?= old('is_nik_verified', $user['is_nik_verified']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_nik_verified">Verifikasi NIK (Khusus Warga)</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('super-admin/users') ?>" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
let villageTomSelect;

function toggleDesaRequirement() {
    const roleSelect = document.getElementById('role_id');
    const star = document.getElementById('desa_req_star');
    const help = document.getElementById('desa_help');

    if (roleSelect.value === '1') { // Super Admin
        star.style.display = 'none';
        if (villageTomSelect) {
            villageTomSelect.clear();
        }
        help.textContent = 'Super Admin memiliki hak akses global lintas seluruh desa.';
    } else {
        star.style.display = 'inline';
        help.textContent = 'Wajib dipilih untuk Admin Desa, Operator, dan Warga.';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    villageTomSelect = new TomSelect('#village_select', {
        valueField: 'id',
        labelField: 'text',
        searchField: ['text'],
        placeholder: "-- Ketik nama desa atau kecamatan --",
        allowEmptyOption: true,
        loadThrottle: 300,
        preload: 'focus',
        load: function(query, callback) {
            const url = '<?= base_url('super-admin/users/search-desa') ?>?q=' + encodeURIComponent(query);
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response error');
                    return response.json();
                })
                .then(data => callback(data))
                .catch(() => callback());
        }
    });

    toggleDesaRequirement();
});
</script>
<?= $this->endSection() ?>
