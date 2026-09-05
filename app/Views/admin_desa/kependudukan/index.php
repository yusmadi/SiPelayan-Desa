<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h4 fw-bold mb-1">Data Kependudukan</h2>
        <p class="text-muted mb-0">Master data kependudukan dan administrasi warga Desa <?= esc(session('nama_desa')) ?>.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url($baseRoute . '/import') ?>" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import
        </a>
        <a href="<?= base_url($baseRoute . '/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Penduduk
        </a>
    </div>
</div>

<!-- Statistik Kependudukan -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #4f46e5;">
            <div class="text-muted small">Total Penduduk</div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['total'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #0ea5e9;">
            <div class="text-muted small">Laki-laki</div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['laki'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #ec4899;">
            <div class="text-muted small">Perempuan</div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['perempuan'] ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #10b981;">
            <div class="text-muted small">Kepala Keluarga</div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['kk'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<!-- Filter dan Pencarian -->
<div class="card bg-white p-3 mb-4">
    <form method="get" action="<?= base_url($baseRoute) ?>" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NIK, No KK..." value="<?= esc($search ?? '') ?>">
            </div>
        </div>
        <div class="col-md-2">
            <select name="gender" class="form-select">
                <option value="">-- Jenis Kelamin --</option>
                <option value="L" <?= ($gender ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= ($gender ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Status Penduduk --</option>
                <option value="Tetap" <?= ($statusPenduduk ?? '') === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                <option value="Sementara" <?= ($statusPenduduk ?? '') === 'Sementara' ? 'selected' : '' ?>>Sementara</option>
                <option value="Pindah" <?= ($statusPenduduk ?? '') === 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                <option value="Meninggal" <?= ($statusPenduduk ?? '') === 'Meninggal' ? 'selected' : '' ?>>Meninggal</option>
            </select>
        </div>
        <?php if (!empty($dusunList)): ?>
        <div class="col-md-2">
            <select name="dusun" class="form-select">
                <option value="">-- Semua Dusun --</option>
                <?php foreach ($dusunList as $d): ?>
                    <option value="<?= esc($d) ?>" <?= ($selectedDusun ?? '') === $d ? 'selected' : '' ?>><?= esc($d) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            <?php if (!empty($search) || !empty($gender) || !empty($statusPenduduk) || !empty($selectedDusun)): ?>
                <a href="<?= base_url($baseRoute) ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Daftar Penduduk -->
<div class="card bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>NIK / No. KK</th>
                    <th>Nama Lengkap</th>
                    <th>L/P</th>
                    <th>TTL & Umur</th>
                    <th>Alamat / Dusun</th>
                    <th>Hubungan KK</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pendudukList)): ?>
                    <?php $no = 1; foreach ($pendudukList as $p): ?>
                        <?php 
                            $birthDate = !empty($p['tanggal_lahir']) ? new DateTime($p['tanggal_lahir']) : null;
                            $age = $birthDate ? $birthDate->diff(new DateTime())->y : '-';
                        ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td>
                                <span class="fw-bold text-dark font-monospace"><?= esc($p['nik']) ?></span>
                                <div class="text-muted small font-monospace">KK: <?= esc($p['no_kk']) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($p['nama_lengkap']) ?></div>
                                <small class="text-muted"><?= esc($p['pekerjaan'] ?: 'Belum Bekerja') ?></small>
                            </td>
                            <td>
                                <span class="badge <?= $p['jenis_kelamin'] === 'L' ? 'bg-info text-dark' : 'bg-danger-subtle text-danger' ?>">
                                    <?= $p['jenis_kelamin'] === 'L' ? 'L' : 'P' ?>
                                </span>
                            </td>
                            <td>
                                <div class="small"><?= esc($p['tempat_lahir']) ?>, <?= !empty($p['tanggal_lahir']) ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-' ?></div>
                                <span class="text-muted small"><?= $age ?> thn</span>
                            </td>
                            <td>
                                <div class="small fw-medium"><?= esc($p['dusun'] ?: '-') ?></div>
                                <div class="text-muted small">RT <?= esc($p['rt'] ?: '0') ?> / RW <?= esc($p['rw'] ?: '0') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= esc($p['status_hubungan_kk'] ?: '-') ?></span>
                            </td>
                            <td>
                                <?php if ($p['status_penduduk'] === 'Tetap'): ?>
                                    <span class="badge bg-success">Tetap</span>
                                <?php elseif ($p['status_penduduk'] === 'Sementara'): ?>
                                    <span class="badge bg-warning text-dark">Sementara</span>
                                <?php elseif ($p['status_penduduk'] === 'Pindah'): ?>
                                    <span class="badge bg-secondary">Pindah</span>
                                <?php elseif ($p['status_penduduk'] === 'Meninggal'): ?>
                                    <span class="badge bg-dark">Meninggal</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc($p['status_penduduk']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url($baseRoute . '/detail/' . $p['id']) ?>" class="btn btn-outline-info" title="Detail Penduduk">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url($baseRoute . '/edit/' . $p['id']) ?>" class="btn btn-outline-warning" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="<?= base_url($baseRoute . '/delete/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penduduk ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                            Belum ada data penduduk yang sesuai dengan kriteria pencarian.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
