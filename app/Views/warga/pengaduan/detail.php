<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('warga/pengaduan') ?>" class="text-decoration-none text-muted small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengaduan
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-<?= $statusColors[$pengaduan['status']] ?? 'secondary' ?> px-2 py-1 fs-6">
                    <?= $statusLabels[$pengaduan['status']] ?? ucfirst($pengaduan['status']) ?>
                </span>
                <span class="badge bg-light text-dark border"><?= esc($pengaduan['kategori']) ?></span>
                <?php if ($pengaduan['is_anonymous']): ?>
                    <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-incognito me-1"></i>Anonim</span>
                <?php endif; ?>
            </div>
            <h2 class="h3 font-weight-bold mb-0"><?= esc($pengaduan['judul']) ?></h2>
        </div>
        <div class="text-md-end">
            <div class="text-muted small">Nomor Tiket</div>
            <span class="badge bg-light text-primary border font-monospace fs-6 px-3 py-2"><?= esc($pengaduan['no_tiket']) ?></span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Rincian Laporan -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Rincian Pengaduan</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="text-muted small fw-semibold d-block mb-1">Isi Laporan / Keluhan</label>
                    <div class="p-3 bg-light rounded-3 text-dark" style="white-space: pre-line; line-height: 1.6;">
                        <?= esc($pengaduan['isi_laporan']) ?>
                    </div>
                </div>

                <?php if (!empty($pengaduan['lokasi'])): ?>
                    <div class="mb-4">
                        <label class="text-muted small fw-semibold d-block mb-1">Lokasi Kejadian</label>
                        <div class="d-flex align-items-center text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-2 fs-5"></i>
                            <span><?= esc($pengaduan['lokasi']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                    $fotos = !empty($pengaduan['foto_paths']) ? json_decode($pengaduan['foto_paths'], true) : [];
                ?>
                <?php if (!empty($fotos)): ?>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold d-block mb-2">Foto / Bukti Terlampir (<?= count($fotos) ?>)</label>
                        <div class="row g-3">
                            <?php foreach ($fotos as $foto): ?>
                                <div class="col-6 col-md-4">
                                    <a href="<?= base_url($foto) ?>" target="_blank" class="d-block text-decoration-none">
                                        <div class="card border h-100 overflow-hidden shadow-sm hover-scale">
                                            <img src="<?= base_url($foto) ?>" alt="Bukti Foto" class="img-fluid rounded" style="height: 140px; width: 100%; object-fit: cover;">
                                            <div class="p-2 bg-white text-center small text-muted">
                                                <i class="bi bi-zoom-in me-1"></i> Perbesar
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tanggapan dari Pemerintah Desa -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-chat-square-quote-fill text-primary me-2"></i>Tanggapan & Tindak Lanjut Desa
                </h5>
                <?php if (!empty($pengaduan['tanggapan'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ditanggapi</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($pengaduan['tanggapan'])): ?>
                    <div class="p-3 bg-primary-subtle border-start border-4 border-primary rounded-end mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-primary">
                                <i class="bi bi-shield-check me-1"></i> Pemerintah <?= sebutan_desa() ?>
                            </span>
                            <?php if (!empty($pengaduan['resolved_at'])): ?>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($pengaduan['resolved_at'])) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="text-dark" style="white-space: pre-line; line-height: 1.6;">
                            <?= esc($pengaduan['tanggapan']) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-hourglass-split fs-2 d-block mb-2 text-secondary opacity-75"></i>
                        <p class="mb-0">Laporan sedang dalam antrean peninjauan petugas <?= strtolower(sebutan_desa()) ?>.</p>
                        <small>Tanggapan resmi atau status penanganan akan diperbarui di sini.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Timeline Status -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title fw-bold mb-0 text-dark">Informasi Tiket</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal Dibuat:</span>
                        <span class="fw-semibold text-dark"><?= date('d F Y, H:i', strtotime($pengaduan['created_at'])) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Kategori:</span>
                        <span class="fw-semibold text-dark"><?= esc($pengaduan['kategori']) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-<?= $statusColors[$pengaduan['status']] ?? 'secondary' ?>"><?= $statusLabels[$pengaduan['status']] ?? ucfirst($pengaduan['status']) ?></span>
                    </li>
                    <?php if (!empty($pengaduan['resolved_at'])): ?>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">Tanggal Selesai:</span>
                            <span class="fw-semibold text-success"><?= date('d F Y, H:i', strtotime($pengaduan['resolved_at'])) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title fw-bold mb-0 text-dark">Alur Status</h6>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="d-flex gap-3 mb-3">
                        <div class="text-primary fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <div class="fw-bold small">Aduan Diterima</div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($pengaduan['created_at'])) ?></div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="d-flex gap-3 mb-3">
                        <div class="<?= in_array($pengaduan['status'], ['in_progress', 'resolved', 'closed']) ? 'text-primary' : 'text-muted opacity-50' ?> fs-5">
                            <i class="bi bi-<?= in_array($pengaduan['status'], ['in_progress', 'resolved', 'closed']) ? 'check-circle-fill' : 'circle' ?>"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Verifikasi & Peninjauan</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Diproses oleh perangkat desa</div>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="d-flex gap-3">
                        <div class="<?= in_array($pengaduan['status'], ['resolved', 'closed']) ? 'text-success' : ($pengaduan['status'] === 'rejected' ? 'text-danger' : 'text-muted opacity-50') ?> fs-5">
                            <i class="bi bi-<?= in_array($pengaduan['status'], ['resolved', 'closed']) ? 'check-circle-fill' : ($pengaduan['status'] === 'rejected' ? 'x-circle-fill' : 'circle') ?>"></i>
                        </div>
                        <div>
                            <div class="fw-bold small"><?= $pengaduan['status'] === 'rejected' ? 'Ditolak' : 'Tindak Lanjut Selesai' ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <?= !empty($pengaduan['resolved_at']) ? date('d M Y, H:i', strtotime($pengaduan['resolved_at'])) : 'Menunggu penyelesaian' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-scale {
    transition: transform 0.2s ease;
}
.hover-scale:hover {
    transform: scale(1.03);
}
</style>
<?= $this->endSection() ?>
