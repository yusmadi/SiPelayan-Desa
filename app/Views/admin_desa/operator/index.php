<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">
            <i class="bi bi-people-fill text-primary me-2"></i>Daftar Operator <?= sebutan_desa() ?>
        </h2>
        <p class="text-muted mb-0">Kelola akun staf operator yang melayani administrasi di <?= esc(session('nama_desa') ?? 'Desa') ?>.</p>
    </div>
    <div>
        <a href="<?= base_url('admin-desa/operator/tambah') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Buat Akun Operator <?= sebutan_desa() ?>
        </a>
    </div>
</div>

<div class="card bg-white p-4 shadow-sm border-0 mb-4">
    <form method="get" action="<?= base_url('admin-desa/operator') ?>" class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari nama operator, email, atau no. HP..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark flex-grow-1">Filter</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin-desa/operator') ?>" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card bg-white shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Operator</th>
                    <th>Kontak</th>
                    <th>NIK</th>
                    <th>Status</th>
                    <th>Terdaftar Pada</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($operators)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-2">Belum ada akun Operator <?= sebutan_desa() ?> yang terdaftar.</p>
                            <a href="<?= base_url('admin-desa/operator/tambah') ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-person-plus-fill me-1"></i> Buat Akun Operator Sekarang
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($operators as $op): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($op['nama_lengkap']) ?>&background=0d6efd&color=fff" alt="Avatar" class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-semibold text-dark"><?= esc($op['nama_lengkap']) ?></div>
                                        <div class="small text-muted"><?= esc($op['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($op['no_hp'])): ?>
                                    <a href="<?= format_wa_url($op['no_hp']) ?>" target="_blank" class="text-decoration-none text-success small fw-medium">
                                        <i class="bi bi-whatsapp me-1"></i><?= esc($op['no_hp']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-dark font-monospace"><?= esc($op['nik'] ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if ($op['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-muted"><?= date('d M Y', strtotime($op['created_at'])) ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="<?= base_url('admin-desa/operator/edit/' . $op['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Data Operator">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= base_url('admin-desa/operator/toggle/' . $op['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm <?= $op['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $op['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi <?= $op['is_active'] ? 'bi-slash-circle' : 'bi-check2-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <form action="<?= base_url('admin-desa/operator/delete/' . $op['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun operator ini? Akun yang dihapus tidak dapat login lagi.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
