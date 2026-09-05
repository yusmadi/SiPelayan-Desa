<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Dashboard Operator <?= sebutan_desa() ?></h2>
        <p class="text-muted mb-0">Pelayanan operasional harian & verifikasi surat <?= sebutan_desa() ?> <?= esc(session('nama_desa')) ?>.</p>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i><?= date('d F Y') ?></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #0ea5e9;">
            <div class="text-muted small">Total Data Penduduk</div>
            <h3 class="fw-bold text-dark mb-0"><?= $totalPenduduk ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #f59e0b;">
            <div class="text-muted small">Surat Masuk (Perlu Verifikasi)</div>
            <h3 class="fw-bold text-dark mb-0"><?= $draftSurat ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-white stat-card" style="border-left-color: #10b981;">
            <div class="text-muted small">Surat Sedang Diproses</div>
            <h3 class="fw-bold text-dark mb-0"><?= $diprosesSurat ?></h3>
        </div>
    </div>
</div>

<div class="card bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Antrean Verifikasi Surat</h5>
        <a href="/operator/permohonan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Permohonan</th>
                    <th>Nama Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal Masuk</th>
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
                                <?php if($s['status'] === 'submitted' || $s['status'] === 'diajukan'): ?>
                                    <span class="badge bg-warning text-dark">Perlu Verifikasi</span>
                                <?php elseif($s['status'] === 'verified_operator' || $s['status'] === 'diproses'): ?>
                                    <span class="badge bg-primary">Diteruskan ke <?= sebutan_kades() ?></span>
                                <?php elseif(in_array($s['status'], ['approved_admin', 'completed', 'selesai'])): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php elseif(in_array($s['status'], ['rejected', 'ditolak'])): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc(ucfirst($s['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada antrean surat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
