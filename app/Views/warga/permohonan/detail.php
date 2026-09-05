<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Detail Permohonan Surat</h2>
        <p class="text-muted mb-0">Nomor Registrasi: <span class="text-primary fw-bold"><?= esc($permohonan['no_permohonan']) ?></span></p>
    </div>
    <a href="/warga/permohonan" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
    </a>
</div>

<?php 
    $formData = [];
    if (!empty($permohonan['data_form'])) {
        $decoded = json_decode($permohonan['data_form'], true);
        if (is_array($decoded)) {
            $formData = $decoded;
        }
    }
    $docSyarat = [];
    if (!empty($permohonan['dokumen_syarat'])) {
        $decodedDocs = json_decode($permohonan['dokumen_syarat'], true);
        if (is_array($decodedDocs)) {
            $docSyarat = $decodedDocs;
        }
    }
?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Informasi Surat Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Data Pengajuan Surat
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block">Jenis Surat</label>
                        <div class="fw-bold text-dark fs-5"><?= esc($permohonan['nama_surat'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold d-block">Tanggal Pengajuan</label>
                        <div class="fw-medium text-dark"><?= date('d F Y, H:i', strtotime($permohonan['created_at'])) ?> WIB</div>
                    </div>
                    <?php if (!empty($permohonan['no_surat_keluar'])): ?>
                        <div class="col-12">
                            <label class="text-muted small fw-semibold d-block">Nomor Surat Resmi Diterbitkan</label>
                            <div class="fw-bold text-success fs-6 bg-light p-3 rounded border">
                                <i class="bi bi-patch-check-fill me-1 text-success"></i> <?= esc($permohonan['no_surat_keluar']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-card-checklist me-1"></i> Data Isian Formulir
                </h6>
                <div class="bg-light p-3 rounded-3 mb-4">
                    <?php if (!empty($formData)): ?>
                        <dl class="row mb-0">
                            <?php foreach ($formData as $key => $val): ?>
                                <dt class="col-sm-4 text-muted text-capitalize"><?= esc(str_replace('_', ' ', $key)) ?></dt>
                                <dd class="col-sm-8 fw-semibold text-dark mb-2"><?= nl2br(esc(is_array($val) ? json_encode($val) : $val)) ?></dd>
                            <?php endforeach; ?>
                        </dl>
                    <?php else: ?>
                        <p class="text-muted mb-0 small">Tidak ada data isian formulir.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($docSyarat)): ?>
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                        <i class="bi bi-paperclip me-1"></i> Dokumen Lampiran
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($docSyarat as $doc): ?>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <i class="bi bi-file-earmark-check me-1 text-primary"></i> <?= esc($doc) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-activity text-primary me-2"></i>Status Permohonan
                </h5>
            </div>
            <div class="card-body p-4 text-center">
                <?php if ($permohonan['status'] === 'submitted' || $permohonan['status'] === 'diajukan'): ?>
                    <span class="badge bg-warning text-dark fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-hourglass-split me-1"></i> Diajukan
                    </span>
                    <p class="text-muted small">Permohonan Anda sedang dalam antrean verifikasi operator <?= strtolower(sebutan_desa()) ?>.</p>
                <?php elseif ($permohonan['status'] === 'verified_operator'): ?>
                    <span class="badge bg-primary fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-person-check me-1"></i> Diverifikasi Operator
                    </span>
                    <p class="text-muted small">Berkas telah diverifikasi dan menunggu penandatanganan <?= sebutan_kades() ?> / <?= sebutan_sekdes() ?>.</p>
                <?php elseif ($permohonan['status'] === 'approved_admin'): ?>
                    <span class="badge bg-info text-dark fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-file-earmark-check me-1"></i> Disetujui Admin
                    </span>
                    <p class="text-muted small">Permohonan telah disetujui dan nomor surat sedang diterbitkan.</p>
                <?php elseif ($permohonan['status'] === 'completed' || $permohonan['status'] === 'selesai'): ?>
                    <span class="badge bg-success fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                    </span>
                    <p class="text-muted small">Surat telah resmi diterbitkan. Anda dapat mengambil fisik surat di kantor <?= strtolower(sebutan_desa()) ?> atau mengunduhnya jika tersedia.</p>
                <?php elseif ($permohonan['status'] === 'rejected' || $permohonan['status'] === 'ditolak'): ?>
                    <span class="badge bg-danger fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </span>
                    <p class="text-muted small">Permohonan tidak dapat diproses. Silakan periksa catatan penolakan.</p>
                <?php elseif ($permohonan['status'] === 'cancelled' || $permohonan['status'] === 'dibatalkan'): ?>
                    <span class="badge bg-secondary fs-6 rounded-pill px-4 py-2 mb-3">
                        <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                    </span>
                    <p class="text-muted small">Permohonan ini telah dibatalkan oleh pemohon.</p>
                <?php endif; ?>

                <?php if (!empty($permohonan['catatan_operator'])): ?>
                    <div class="alert alert-info py-2 px-3 text-start small mt-3">
                        <strong>Catatan Operator:</strong><br><?= esc($permohonan['catatan_operator']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($permohonan['catatan_admin'])): ?>
                    <div class="alert alert-success py-2 px-3 text-start small mt-3">
                        <strong>Catatan Admin:</strong><br><?= esc($permohonan['catatan_admin']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($permohonan['catatan_penolakan'])): ?>
                    <div class="alert alert-danger py-2 px-3 text-start small mt-3">
                        <strong>Alasan Penolakan:</strong><br><?= esc($permohonan['catatan_penolakan']) ?>
                    </div>
                <?php endif; ?>

                <?php if ($permohonan['status'] === 'submitted'): ?>
                    <form action="/warga/permohonan/<?= $permohonan['id'] ?>/cancel" method="post" class="mt-4" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan ini?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Permohonan
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
