<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Master Data Kabupaten / Kota</h2>
        <p class="text-muted mb-0">Kelola master data kabupaten dan kota terdaftar.</p>
    </div>
    <div>
        <a href="<?= base_url('super-admin/kabupaten/tambah') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kabupaten Baru
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
    <form action="<?= base_url('super-admin/kabupaten') ?>" method="get" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari nama kabupaten, kode kemendagri..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="provinsi_id" class="form-select">
                <option value="">-- Semua Provinsi --</option>
                <?php foreach($provinsiList as $prov): ?>
                    <option value="<?= $prov['id'] ?>" <?= ($provinsiId == $prov['id']) ? 'selected' : '' ?>>
                        <?= esc($prov['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-filter me-1"></i> Filter</button>
            <?php if(!empty($search) || !empty($provinsiId)): ?>
                <a href="<?= base_url('super-admin/kabupaten') ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            Daftar Kabupaten / Kota
            <?php if(!empty($pager)): ?>
                <span class="badge bg-primary rounded-pill ms-2"><?= number_format($pager->getTotal(), 0, ',', '.') ?> Total</span>
            <?php endif; ?>
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Kemendagri</th>
                    <th>Nama Kabupaten / Kota</th>
                    <th>Provinsi</th>
                    <th>Total Wilayah</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($kabupatenList)): ?>
                    <?php 
                        $currentPage = !empty($pager) ? $pager->getCurrentPage() : 1;
                        $perPage = $perPage ?? 25;
                        $no = ($currentPage - 1) * $perPage + 1;
                        foreach($kabupatenList as $k): 
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td><code class="text-dark bg-light px-2 py-1 rounded"><?= esc($k['kode_kemendagri']) ?></code></td>
                            <td class="fw-bold text-dark"><?= esc($k['nama']) ?></td>
                            <td><i class="bi bi-map me-1 text-muted"></i><?= esc($k['nama_provinsi'] ?? ($k['provinsi'] ?? '-')) ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= base_url('super-admin/kecamatan?kabupaten_id=' . $k['id']) ?>" class="badge bg-secondary-subtle text-secondary border text-decoration-none" title="Lihat Kecamatan">
                                        <?= $k['total_kecamatan'] ?? 0 ?> Kec
                                    </a>
                                    <a href="<?= base_url('super-admin/desa?kabupaten_id=' . $k['id']) ?>" class="badge bg-primary-subtle text-primary border text-decoration-none" title="Lihat Desa">
                                        <?= $k['total_desa'] ?? 0 ?> Desa
                                    </a>
                                </div>
                            </td>
                            <td>
                                <?php if($k['is_active']): ?>
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
                                    <a href="<?= base_url('super-admin/kabupaten/edit/' . $k['id']) ?>" class="btn btn-outline-primary" title="Edit Kabupaten">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="<?= base_url('super-admin/kabupaten/toggle/' . $k['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Ubah status aktif kabupaten ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning" title="<?= $k['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi <?= $k['is_active'] ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' ?>"></i>
                                        </button>
                                    </form>

                                    <form action="<?= base_url('super-admin/kabupaten/delete/' . $k['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kabupaten <?= esc($k['nama']) ?>?');">
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada data kabupaten/kota.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pager)): ?>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
            <div class="text-muted small">
                Menampilkan <b><?= count($kabupatenList) ?></b> kabupaten/kota dari total <b><?= number_format($pager->getTotal(), 0, ',', '.') ?></b> data
            </div>
            <div>
                <?= $pager->links('default', 'bootstrap_full') ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
