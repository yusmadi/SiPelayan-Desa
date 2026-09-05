<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Manajemen Pengguna</h2>
        <p class="text-muted mb-0">Kelola akun pengguna, level hak akses (Role), dan tenant desa terkait.</p>
    </div>
    <div>
        <a href="<?= base_url('super-admin/users/tambah') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter & Search Card -->
<div class="card bg-white p-3 mb-4 shadow-sm border-0">
    <form action="<?= base_url('super-admin/users') ?>" method="get" id="filterForm" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari nama, email, NIK, no HP, desa..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="role_id" class="form-select">
                <option value="">-- Semua Role --</option>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($roleId == $r['id']) ? 'selected' : '' ?>>
                        <?= esc($r['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <div class="position-relative">
                <input type="hidden" name="village_id" id="filter_village_id" value="<?= esc($villageId ?? '') ?>">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-muted"></i></span>
                    <input type="text" id="filter_village_search" class="form-control" placeholder="Pilih / cari desa..." 
                           value="<?= !empty($selectedDesa) ? esc('Desa ' . $selectedDesa['nama_desa'] . ' (Kec. ' . $selectedDesa['nama_kecamatan'] . ')') : '' ?>" 
                           autocomplete="off">
                    <?php if(!empty($villageId)): ?>
                        <button type="button" class="btn btn-outline-secondary" id="clearVillageFilter" title="Hapus filter desa"><i class="bi bi-x"></i></button>
                    <?php endif; ?>
                </div>
                <!-- Autocomplete Dropdown List -->
                <div id="village_suggestions" class="dropdown-menu w-100 shadow-sm border mt-1" style="max-height: 240px; overflow-y: auto; display: none;"></div>
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter me-1"></i> Filter</button>
            <?php if(!empty($search) || !empty($roleId) || !empty($villageId)): ?>
                <a href="<?= base_url('super-admin/users') ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Total Pengguna: <span class="badge bg-primary rounded-pill"><?= number_format($totalUsers, 0, ',', '.') ?></span></h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama & Email</th>
                    <th>Role</th>
                    <th>Wilayah Desa</th>
                    <th>NIK / No HP</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($userList)): ?>
                    <?php 
                        $no = (($currentPage ?? 1) - 1) * ($perPage ?? 15) + 1; 
                        foreach($userList as $u): 
                            // Hitung inisial nama untuk avatar lokal
                            $words = explode(' ', trim($u['nama_lengkap']));
                            $initials = '';
                            foreach(array_slice($words, 0, 2) as $w) {
                                if (!empty($w)) $initials .= mb_strtoupper(mb_substr($w, 0, 1));
                            }
                            if (empty($initials)) $initials = 'U';

                            // Warna acak konsisten berdasarkan id pengguna
                            $bgColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777', '#475569'];
                            $avatarBg = $bgColors[$u['id'] % count($bgColors)];
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm flex-shrink-0" 
                                         style="width: 36px; height: 36px; background-color: <?= $avatarBg ?>; font-size: 0.85rem; user-select: none;">
                                        <?= esc($initials) ?>
                                    </div>
                                    <div class="text-truncate" style="max-width: 220px;">
                                        <div class="fw-bold text-dark text-truncate"><?= esc($u['nama_lengkap']) ?></div>
                                        <small class="text-muted text-truncate d-block"><i class="bi bi-envelope me-1"></i><?= esc($u['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $badgeClass = match($u['role_slug']) {
                                        'super_admin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                        'admin_desa'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'operator'    => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                        'warga'       => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                        default       => 'bg-light text-dark'
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?> border px-2 py-1">
                                    <?= esc($u['role_name'] ?? 'User') ?>
                                </span>
                                <?php if(!empty($u['sk_kades_path'])): ?>
                                    <div class="mt-1">
                                        <a href="<?= base_url($u['sk_kades_path']) ?>" target="_blank" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none" title="Lihat Berkas SK Kepala Desa">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>Berkas SK
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($u['village_id'])): ?>
                                    <div><span class="fw-semibold"><?= esc($u['nama_desa'] ?? 'Desa #' . $u['village_id']) ?></span></div>
                                    <small class="text-muted">Kec. <?= esc($u['nama_kecamatan'] ?? '-') ?>, <?= esc($u['nama_kabupaten'] ?? '') ?></small>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-shield-check me-1"></i>Pusat / Global</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($u['nik'])): ?>
                                    <div><code class="text-dark bg-light px-1 rounded"><?= esc($u['nik']) ?></code></div>
                                <?php endif; ?>
                                <?php if(!empty($u['no_hp'])): ?>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= esc($u['no_hp']) ?></small>
                                <?php elseif(empty($u['nik'])): ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php if($u['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="width: fit-content;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                        </span>
                                    <?php elseif(!empty($u['sk_kades_path']) || $u['role_slug'] === 'admin_desa'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="width: fit-content;">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Aktivasi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="width: fit-content;">
                                            <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                        </span>
                                    <?php endif; ?>

                                    <?php if($u['role_slug'] === 'warga'): ?>
                                        <?php if($u['is_nik_verified']): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.68rem; width: fit-content;">
                                                <i class="bi bi-person-check-fill me-1"></i> NIK Verified
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem; width: fit-content;">
                                                <i class="bi bi-hourglass-split me-1"></i> NIK Unverified
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('super-admin/users/edit/' . $u['id']) ?>" class="btn btn-outline-primary" title="Edit Pengguna">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <?php if($u['id'] != session('user_id')): ?>
                                        <form action="<?= base_url('super-admin/users/toggle/' . $u['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Ubah status aktif pengguna ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-warning" title="<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <i class="bi <?= $u['is_active'] ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' ?>"></i>
                                            </button>
                                        </form>

                                        <form action="<?= base_url('super-admin/users/delete/' . $u['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pengguna <?= esc($u['nama_lengkap']) ?>?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            Tidak ada data pengguna yang sesuai.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pager) && $totalUsers > $perPage): ?>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
            <div class="text-muted small">
                Menampilkan <b><?= count($userList) ?></b> dari total <b><?= number_format($totalUsers, 0, ',', '.') ?></b> pengguna
            </div>
            <div>
                <?= $pager->links('default', 'bootstrap_full') ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const villageSearch = document.getElementById('filter_village_search');
    const villageIdInput = document.getElementById('filter_village_id');
    const suggestions = document.getElementById('village_suggestions');
    const clearBtn = document.getElementById('clearVillageFilter');
    let debounceTimer;

    if (villageSearch) {
        villageSearch.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                suggestions.style.display = 'none';
                suggestions.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`<?= base_url('super-admin/users/search-desa') ?>?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = '';
                        if (data && data.length > 0) {
                            data.forEach(item => {
                                const a = document.createElement('a');
                                a.className = 'dropdown-item py-2 border-bottom';
                                a.href = 'javascript:void(0)';
                                a.innerHTML = `<i class="bi bi-geo-alt me-1 text-muted"></i> ${item.text}`;
                                a.addEventListener('click', () => {
                                    villageSearch.value = item.text;
                                    villageIdInput.value = item.id;
                                    suggestions.style.display = 'none';
                                });
                                suggestions.appendChild(a);
                            });
                            suggestions.style.display = 'block';
                        } else {
                            suggestions.innerHTML = '<div class="dropdown-item text-muted disabled">Desa tidak ditemukan</div>';
                            suggestions.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Error searching desa:', err);
                    });
            }, 300);
        });

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', function (e) {
            if (!villageSearch.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            villageSearch.value = '';
            villageIdInput.value = '';
            document.getElementById('filterForm').submit();
        });
    }
});
</script>
<?= $this->endSection() ?>
