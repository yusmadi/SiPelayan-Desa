<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1"><i class="bi bi-briefcase text-primary me-2"></i>Data Referensi Pekerjaan</h2>
        <p class="text-muted mb-0">Kelola daftar klasifikasi pekerjaan resmi untuk keperluan pencatatan data penduduk dan administrasi surat.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahPekerjaan">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pekerjaan Baru
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 mb-4 shadow-sm border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 align-middle text-success"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm border-0" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle text-danger"></i>
        <?= session()->getFlashdata('error') ?>
        <?php if ($validation && $validation->getErrors()): ?>
            <ul class="mb-0 mt-2 small">
                <?php foreach ($validation->getErrors() as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter & Search Card -->
<div class="card bg-white p-3 mb-4 shadow-sm border-0 rounded-3">
    <form action="<?= base_url($baseRoute) ?>" method="get" class="row g-2 align-items-center">
        <div class="col-md-9 col-lg-10">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Ketik nama pekerjaan atau keterangan..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3 col-lg-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url($baseRoute) ?>" class="btn btn-outline-secondary" title="Reset Pencarian"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card bg-white p-4 shadow-sm border-0 rounded-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Daftar Pekerjaan <span class="badge bg-primary rounded-pill ms-1"><?= $total ?> Total</span></h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;" class="text-center">No.</th>
                    <th>Nama Pekerjaan</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 160px;">Digunakan</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pekerjaan)): ?>
                    <?php 
                        $page = (int) ($pager->getCurrentPage() ?? 1);
                        $perPage = (int) ($pager->getPerPage() ?? 15);
                        $no = ($page - 1) * $perPage + 1;
                    ?>
                    <?php foreach ($pekerjaan as $p): ?>
                        <tr>
                            <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                            <td>
                                <span class="fw-bold text-dark fs-6 d-block"><?= esc($p['nama']) ?></span>
                            </td>
                            <td>
                                <span class="text-muted small"><?= esc($p['keterangan'] ?: '-') ?></span>
                            </td>
                            <td class="text-center">
                                <?php $count = $usageCounts[$p['nama']] ?? 0; ?>
                                <?php if ($count > 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                        <i class="bi bi-people-fill me-1"></i><?= $count ?> Warga
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                        0 Warga
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditPekerjaan<?= $p['id'] ?>" title="Edit Pekerjaan">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusPekerjaan<?= $p['id'] ?>" title="Hapus Pekerjaan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT PEKERJAAN -->
                        <div class="modal fade" id="modalEditPekerjaan<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $p['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold text-dark" id="modalEditLabel<?= $p['id'] ?>">
                                            <i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Pekerjaan
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= base_url($baseRoute . '/edit/' . $p['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Pekerjaan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control" value="<?= esc($p['nama']) ?>" required placeholder="Contoh: Petani, Wiraswasta, PNS">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Keterangan (Opsional)</label>
                                                <input type="text" name="keterangan" class="form-control" value="<?= esc($p['keterangan'] ?? '') ?>" placeholder="Contoh: Sektor Pertanian / Formal">
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL HAPUS PEKERJAAN -->
                        <div class="modal fade" id="modalHapusPekerjaan<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalHapusLabel<?= $p['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title fw-bold" id="modalHapusLabel<?= $p['id'] ?>">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 text-center">
                                        <div class="icon-box bg-danger-subtle text-danger rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-trash fs-2"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-2">Hapus Pekerjaan "<?= esc($p['nama']) ?>" ?</h6>
                                        <p class="text-muted small mb-0">Apakah Anda yakin ingin menghapus data pekerjaan ini dari referensi database?</p>
                                    </div>
                                    <div class="modal-footer bg-light border-0 justify-content-center">
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                        <form action="<?= base_url($baseRoute . '/delete/' . $p['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i> Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-3">
                                <i class="bi bi-briefcase text-muted fs-1 d-block mb-2"></i>
                                <h6 class="fw-bold text-secondary">Tidak ada data pekerjaan</h6>
                                <p class="text-muted small mb-3">Silakan tambahkan data pekerjaan baru atau sesuaikan filter pencarian.</p>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPekerjaan">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Pekerjaan
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <small class="text-muted">Menampilkan halaman <?= $pager->getCurrentPage() ?> dari <?= $pager->getPageCount() ?></small>
            <?= $pager->links('default', 'bootstrap_full') ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL TAMBAH PEKERJAAN -->
<div class="modal fade" id="modalTambahPekerjaan" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTambahLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pekerjaan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($baseRoute . '/tambah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Petani, Wiraswasta, PNS, Pengrajin" value="<?= old('nama') ?>" required autofocus>
                        <div class="form-text text-muted">Masukkan nama profesi atau klasifikasi pekerjaan.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Sektor Pertanian / Sektor Jasa" value="<?= old('keterangan') ?>">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pekerjaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
