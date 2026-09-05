<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Dashboard Admin <?= sebutan_desa() ?></h2>
        <p class="text-muted mb-0">Panel administrasi dan approval surat <?= sebutan_desa() ?> <?= esc(session('nama_desa')) ?>.</p>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i><?= date('d F Y') ?></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="<?= base_url('admin-desa/kependudukan') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100 shadow-sm" style="border-left-color: #0ea5e9; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small">Total Penduduk Terdata</div>
                    <i class="bi bi-people text-info fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $totalPenduduk ?></h3>
                <small class="text-primary mt-2 d-inline-block"><i class="bi bi-arrow-right-circle me-1"></i>Kelola Data Penduduk</small>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= base_url('admin-desa/permohonan') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100 shadow-sm" style="border-left-color: #f59e0b; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small">Menunggu Approval Surat</div>
                    <i class="bi bi-clock text-warning fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $pendingSurat ?></h3>
                <small class="text-warning mt-2 d-inline-block"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Antrean Surat</small>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= base_url('admin-desa/permohonan') ?>" class="text-decoration-none">
            <div class="card p-3 bg-white stat-card h-100 shadow-sm" style="border-left-color: #10b981; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-muted small">Total Pengajuan Surat</div>
                    <i class="bi bi-envelope-check text-success fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $totalSurat ?></h3>
                <small class="text-success mt-2 d-inline-block"><i class="bi bi-arrow-right-circle me-1"></i>Riwayat Semua Surat</small>
            </div>
        </a>
    </div>
</div>

<div class="card bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Permohonan Surat Terbaru</h5>
        <a href="/admin-desa/permohonan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Permohonan</th>
                    <th>Nama Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($recentSurat)): ?>
                    <?php foreach($recentSurat as $s): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($s['no_permohonan'] ?? $s['nomor_permohonan'] ?? $s['id']) ?></td>
                            <td><?= esc($s['nama_pemohon']) ?></td>
                            <td><?= esc(format_teks_wilayah($s['nama_surat'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                            <td>
                                <?php if($s['status'] === 'submitted'): ?>
                                    <span class="badge bg-warning text-dark">Diajukan</span>
                                <?php elseif($s['status'] === 'verified_operator'): ?>
                                    <span class="badge bg-info text-white">Diverifikasi Operator</span>
                                <?php elseif(in_array($s['status'], ['approved_admin', 'completed', 'selesai'])): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php elseif(in_array($s['status'], ['rejected', 'ditolak'])): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc(ucfirst($s['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada permohonan surat masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
