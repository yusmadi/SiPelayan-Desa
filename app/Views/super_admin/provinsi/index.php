<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Master Data Provinsi</h2>
        <p class="text-muted mb-0">Kelola master data provinsi di Indonesia.</p>
    </div>
    <div>
        <a href="<?= base_url('super-admin/provinsi/tambah') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Provinsi Baru
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter & Search Card -->
<div class="card bg-white p-3 mb-4 shadow-sm border-0">
    <form action="<?= base_url('super-admin/provinsi') ?>" method="get" class="row g-2 align-items-center">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari nama provinsi atau kode kemendagri..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-search me-1"></i> Cari</button>
            <?php if(!empty($search)): ?>
                <a href="<?= base_url('super-admin/provinsi') ?>" class="btn btn-outline-secondary" title="Reset Pencarian"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Total Provinsi: <span class="badge bg-primary rounded-pill"><?= $totalProvinsi ?></span></h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Kemendagri</th>
                    <th>Nama Provinsi</th>
                    <th>Jumlah Kabupaten</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($provinsiList)): ?>
                    <?php $no = 1; foreach($provinsiList as $p): ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td><code class="text-dark bg-light px-2 py-1 rounded"><?= esc($p['kode_kemendagri']) ?></code></td>
                            <td class="fw-bold text-dark"><?= esc($p['nama']) ?></td>
                            <td>
                                <a href="<?= base_url('super-admin/kabupaten?provinsi_id=' . $p['id']) ?>" class="badge bg-info-subtle text-info border border-info-subtle text-decoration-none px-2 py-1">
                                    <i class="bi bi-buildings me-1"></i> <?= $p['total_kabupaten'] ?? 0 ?> Kabupaten/Kota
                                </a>
                            </td>
                            <td>
                                <?php if($p['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('super-admin/provinsi/edit/' . $p['id']) ?>" class="btn btn-outline-primary" title="Edit Provinsi">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="<?= base_url('super-admin/provinsi/toggle/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Ubah status aktif provinsi ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning" title="<?= $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi <?= $p['is_active'] ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' ?>"></i>
                                        </button>
                                    </form>

                                    <form action="<?= base_url('super-admin/provinsi/delete/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus provinsi <?= esc($p['nama']) ?>?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada data provinsi.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
