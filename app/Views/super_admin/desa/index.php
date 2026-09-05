<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Master Data Desa</h2>
        <p class="text-muted mb-0">Kelola daftar desa / tenant yang terdaftar dalam sistem SiPelayan-Desa.</p>
    </div>
    <div>
        <a href="<?= base_url('super-admin/desa/tambah') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Desa Baru
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
    <form action="<?= base_url('super-admin/desa') ?>" method="get" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari nama desa, kecamatan, kode kemendagri, slug..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="kabupaten_id" class="form-select">
                <option value="">-- Semua Kabupaten --</option>
                <?php foreach($kabupatenList as $kab): ?>
                    <option value="<?= $kab['id'] ?>" <?= ($kabupatenId == $kab['id']) ? 'selected' : '' ?>>
                        <?= esc($kab['nama']) ?> (<?= esc($kab['provinsi']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-filter me-1"></i> Filter</button>
            <?php if(!empty($search) || !empty($kabupatenId)): ?>
                <a href="<?= base_url('super-admin/desa') ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card bg-white p-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            Daftar Desa / Kelurahan
            <?php if(!empty($pager)): ?>
                <span class="badge bg-primary rounded-pill ms-2"><?= number_format($pager->getTotal(), 0, ',', '.') ?> Total</span>
            <?php endif; ?>
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Desa & Slug</th>
                    <th>Kecamatan & Kabupaten</th>
                    <th>Kode Kemendagri</th>
                    <th>Kepala Desa & Kontak</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($desaList)): ?>
                    <?php 
                        $currentPage = !empty($pager) ? $pager->getCurrentPage() : 1;
                        $perPage = $perPage ?? 25;
                        $no = ($currentPage - 1) * $perPage + 1;
                        foreach($desaList as $d): 
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($d['nama_desa']) ?></div>
                                <div class="text-muted small">
                                    <span class="badge bg-light text-secondary border">/<?= esc($d['tenant_slug']) ?></span>
                                    <a href="<?= base_url(esc($d['tenant_slug'])) ?>" target="_blank" class="text-decoration-none ms-1 text-primary small" title="Buka Portal Warga Desa">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-geo-alt-fill text-danger me-1 small"></i><?= esc($d['nama_kecamatan']) ?></div>
                                <div class="text-muted small"><?= esc($d['nama_kabupaten'] ?? '-') ?></div>
                            </td>
                            <td>
                                <code class="text-dark bg-light px-2 py-1 rounded"><?= esc($d['kode_kemendagri']) ?></code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc($d['nama_kepala_desa'] ?: '-') ?></div>
                                <?php if(!empty($d['whatsapp_kades'])): ?>
                                    <div class="text-muted small"><i class="bi bi-whatsapp text-success me-1"></i><?= esc($d['whatsapp_kades']) ?></div>
                                <?php elseif(!empty($d['telepon'])): ?>
                                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= esc($d['telepon']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($d['is_active']): ?>
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
                                    <a href="<?= base_url('super-admin/desa/edit/' . $d['id']) ?>" class="btn btn-outline-primary" title="Edit Desa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="<?= base_url('super-admin/desa/toggle/' . $d['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif desa ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning" title="<?= $d['is_active'] ? 'Nonaktifkan Desa' : 'Aktifkan Desa' ?>">
                                            <i class="bi <?= $d['is_active'] ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' ?>"></i>
                                        </button>
                                    </form>

                                    <form action="<?= base_url('super-admin/desa/delete/' . $d['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data desa <?= esc($d['nama_desa']) ?>? Aksi ini tidak dapat dibatalkan.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Desa">
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
                            Tidak ada data desa yang cocok dengan pencarian.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pager)): ?>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
            <div class="text-muted small">
                Menampilkan <b><?= count($desaList) ?></b> desa dari total <b><?= number_format($pager->getTotal(), 0, ',', '.') ?></b> data
            </div>
            <div>
                <?= $pager->links('default', 'bootstrap_full') ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
