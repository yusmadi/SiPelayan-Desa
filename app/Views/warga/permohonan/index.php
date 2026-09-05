<?= $this->extend('layouts/warga') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Riwayat Permohonan Surat</h2>
        <p class="text-muted mb-0">Pantau dan kelola seluruh permohonan surat administrasi <?= strtolower(sebutan_desa()) ?> Anda.</p>
    </div>
    <a href="/warga/permohonan/buat" class="btn btn-primary shadow-sm px-4 py-2">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Surat Baru
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Quick Stat Cards -->
<?php
$totalCount     = count($permohonan ?? []);
$processCount   = 0;
$completedCount = 0;
$rejectedCount  = 0;

if (!empty($permohonan)) {
    foreach ($permohonan as $item) {
        if (in_array($item['status'], ['submitted', 'verified_operator', 'approved_admin'])) {
            $processCount++;
        } elseif ($item['status'] === 'completed') {
            $completedCount++;
        } elseif (in_array($item['status'], ['rejected', 'cancelled'])) {
            $rejectedCount++;
        }
    }
}
?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Total Pengajuan</div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalCount ?></h3>
                </div>
                <div class="icon-box bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Sedang Diproses</div>
                    <h3 class="fw-bold mb-0 text-warning"><?= $processCount ?></h3>
                </div>
                <div class="icon-box bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Selesai / Terbit</div>
                    <h3 class="fw-bold mb-0 text-success"><?= $completedCount ?></h3>
                </div>
                <div class="icon-box bg-success-subtle text-success rounded-3">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Ditolak / Batal</div>
                    <h3 class="fw-bold mb-0 text-danger"><?= $rejectedCount ?></h3>
                </div>
                <div class="icon-box bg-danger-subtle text-danger rounded-3">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0 text-dark">
            <i class="bi bi-list-columns-reverse me-2 text-primary"></i>Daftar Permohonan Surat
        </h5>
        <span class="badge bg-light text-secondary border px-2 py-1"><?= $totalCount ?> Data Ditemukan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 22%;">No. Permohonan</th>
                        <th style="width: 25%;">Jenis Surat</th>
                        <th style="width: 18%;">Tanggal Pengajuan</th>
                        <th style="width: 18%;">Status</th>
                        <th class="text-center pe-4" style="width: 17%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($permohonan)): ?>
                        <?php foreach ($permohonan as $p): ?>
                            <?php 
                                $formData = [];
                                if (!empty($p['data_form'])) {
                                    $decoded = json_decode($p['data_form'], true);
                                    if (is_array($decoded)) {
                                        $formData = $decoded;
                                    }
                                }
                                $docSyarat = [];
                                if (!empty($p['dokumen_syarat'])) {
                                    $decodedDocs = json_decode($p['dokumen_syarat'], true);
                                    if (is_array($decodedDocs)) {
                                        $docSyarat = $decodedDocs;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary d-block">
                                        <?= esc($p['no_permohonan']) ?>
                                    </span>
                                    <?php if (!empty($p['no_surat_keluar'])): ?>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">
                                            <i class="bi bi-file-earmark-check text-success"></i> <?= esc($p['no_surat_keluar']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= esc(format_teks_wilayah($p['nama_surat'] ?? ('Surat Administrasi ' . sebutan_desa()))) ?></span>
                                    <?php if (!empty($p['kode_surat'])): ?>
                                        <span class="badge bg-light text-dark border me-1" style="font-size: 0.72rem;"><?= esc($p['kode_surat']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($formData['keperluan'] ?? $formData['tujuan'] ?? '')): ?>
                                        <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="<?= esc($formData['keperluan'] ?? $formData['tujuan']) ?>">
                                            Ket: <?= esc($formData['keperluan'] ?? $formData['tujuan']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium d-block"><?= date('d M Y', strtotime($p['created_at'])) ?></span>
                                    <small class="text-muted"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</small>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'submitted' || $p['status'] === 'diajukan'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                            <i class="bi bi-hourglass-split me-1"></i> Diajukan
                                        </span>
                                    <?php elseif ($p['status'] === 'verified_operator' || $p['status'] === 'diproses'): ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-person-check me-1"></i> Diverifikasi Operator
                                        </span>
                                    <?php elseif (in_array($p['status'], ['approved_admin', 'completed', 'selesai', 'ttd_kades', 'signed_kades', 'signed'])): ?>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                            </span>
                                            <?php $isSigned = !empty($p['ttd_digital_hash']) || in_array($p['status'], ['ttd_kades', 'signed_kades', 'signed']); ?>
                                            <?php if ($isSigned): ?>
                                                <span class="badge bg-primary text-white rounded-pill px-2 py-2" title="Surat telah ditandatangani <?= sebutan_kades() ?>">
                                                    <i class="bi bi-pen-fill me-1"></i>TTD
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white rounded-pill px-2 py-2" title="Menunggu tanda tangan <?= sebutan_kades() ?>">
                                                    <i class="bi bi-pen me-1"></i>Belum TTD
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($p['status'] === 'rejected' || $p['status'] === 'ditolak'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                        </span>
                                    <?php elseif ($p['status'] === 'cancelled' || $p['status'] === 'dibatalkan'): ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <?= esc(ucfirst(str_replace('_', ' ', $p['status']))) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Tombol Preview Surat (Pratinjau) hanya jika status Selesai -->
                                        <?php if (in_array($p['status'], ['approved_admin', 'completed', 'selesai', 'ttd_kades', 'signed_kades', 'signed'])): ?>
                                            <a href="/warga/permohonan/preview/<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Preview / Pratinjau Surat">
                                                <i class="bi bi-file-earmark-text me-1"></i> Preview
                                            </a>
                                        <?php endif; ?>

                                        <!-- Tombol Detail Modal -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $p['id'] ?>" title="Lihat Detail & Lacak">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </button>
                                        
                                        <!-- Tombol Batalkan jika masih tahap submitted -->
                                        <?php if ($p['status'] === 'submitted' || $p['status'] === 'diajukan'): ?>
                                            <form action="/warga/permohonan/<?= $p['id'] ?>/cancel" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan surat ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Batalkan Permohonan">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <div class="icon-box bg-light text-muted rounded-circle mx-auto mb-3" style="width: 64px; height: 64px;">
                                        <i class="bi bi-inbox fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold text-secondary mb-1">Belum Ada Permohonan Surat</h5>
                                    <p class="text-muted small mb-3">Anda belum pernah mengajukan permohonan surat administrasi <?= strtolower(sebutan_desa()) ?>.</p>
                                    <a href="/warga/permohonan/buat" class="btn btn-primary btn-sm px-3 shadow-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Ajukan Surat Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Permohonan (Ditempatkan di luar tabel/card agar tidak flickering) -->
<?php if (!empty($permohonan)): ?>
    <?php foreach ($permohonan as $p): ?>
        <?php 
            $formData = [];
            if (!empty($p['data_form'])) {
                $decoded = json_decode($p['data_form'], true);
                if (is_array($decoded)) {
                    $formData = $decoded;
                }
            }
            $docSyarat = [];
            if (!empty($p['dokumen_syarat'])) {
                $decodedDocs = json_decode($p['dokumen_syarat'], true);
                if (is_array($decodedDocs)) {
                    $docSyarat = $decodedDocs;
                }
            }
        ?>
        <div class="modal fade" id="modalDetail<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel<?= $p['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalDetailLabel<?= $p['id'] ?>">
                                <i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Permohonan Surat
                            </h5>
                            <span class="text-muted small">No. Registrasi: <?= esc($p['no_permohonan']) ?></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Status Banner -->
                        <div class="p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between <?php
                            if (in_array($p['status'], ['completed', 'selesai', 'approved_admin', 'ttd_kades', 'signed_kades', 'signed'])) echo 'bg-success-subtle border border-success-subtle text-success-emphasis';
                            elseif (in_array($p['status'], ['rejected', 'ditolak'])) echo 'bg-danger-subtle border border-danger-subtle text-danger-emphasis';
                            elseif (in_array($p['status'], ['verified_operator'])) echo 'bg-primary-subtle border border-primary-subtle text-primary-emphasis';
                            else echo 'bg-warning-subtle border border-warning-subtle text-warning-emphasis';
                        ?>">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi <?php
                                    if (in_array($p['status'], ['completed', 'selesai', 'approved_admin', 'ttd_kades', 'signed_kades', 'signed'])) echo 'bi-check-circle-fill fs-3 text-success';
                                    elseif (in_array($p['status'], ['rejected', 'ditolak'])) echo 'bi-x-circle-fill fs-3 text-danger';
                                    elseif (in_array($p['status'], ['verified_operator'])) echo 'bi-hourglass-split fs-3 text-primary';
                                    else echo 'bi-clock-history fs-3 text-warning';
                                ?>"></i>
                                <div>
                                    <div class="fw-bold">Status Saat Ini: 
                                        <?php
                                             if ($p['status'] === 'submitted' || $p['status'] === 'diajukan') echo 'Menunggu Verifikasi Operator';
                                             elseif ($p['status'] === 'verified_operator' || $p['status'] === 'diproses') echo 'Diverifikasi Operator (Menunggu Persetujuan ' . sebutan_kades() . '/Sekdes)';
                                             elseif (in_array($p['status'], ['completed', 'selesai', 'approved_admin', 'ttd_kades', 'signed_kades', 'signed'])) {
                                                 $isSigned = !empty($p['ttd_digital_hash']) || in_array($p['status'], ['ttd_kades', 'signed_kades', 'signed']);
                                                 echo 'Surat Selesai (' . ($isSigned ? ('TTD / Sudah Ditandatangani ' . sebutan_kades()) : ('Belum TTD / Menunggu TTD ' . sebutan_kades())) . ')';
                                             }
                                             elseif ($p['status'] === 'rejected' || $p['status'] === 'ditolak') echo 'Permohonan Ditolak';
                                             elseif ($p['status'] === 'cancelled' || $p['status'] === 'dibatalkan') echo 'Permohonan Dibatalkan';
                                             else echo esc(ucfirst($p['status']));
                                         ?>
                                     </div>
                                     <small class="text-muted">Terakhir diperbarui: <?= date('d M Y H:i', strtotime($p['updated_at'] ?? $p['created_at'])) ?> WIB</small>
                                 </div>
                             </div>
                         </div>

                         <!-- Informasi Pokok -->
                         <div class="row g-3 mb-4">
                             <div class="col-md-6">
                                 <label class="text-muted small fw-semibold d-block">Jenis Surat</label>
                                 <div class="fw-bold text-dark fs-6"><?= esc(format_teks_wilayah($p['nama_surat'] ?? '-')) ?></div>
                             </div>
                             <div class="col-md-6">
                                 <label class="text-muted small fw-semibold d-block">Tanggal Pengajuan</label>
                                 <div class="fw-medium text-dark"><?= date('d F Y, H:i', strtotime($p['created_at'])) ?> WIB</div>
                             </div>
                             <?php if (!empty($p['no_surat_keluar'])): ?>
                                 <div class="col-md-12">
                                     <label class="text-muted small fw-semibold d-block">Nomor Surat Keluar Resmi</label>
                                     <div class="fw-bold text-success fs-6 bg-light p-2 rounded border">
                                         <i class="bi bi-patch-check-fill me-1 text-success"></i> <?= esc($p['no_surat_keluar']) ?>
                                     </div>
                                 </div>
                             <?php endif; ?>
                         </div>

                         <!-- Isian Formulir -->
                         <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                             <i class="bi bi-card-checklist me-1"></i> Data Isian Permohonan
                         </h6>
                         <div class="bg-light p-3 rounded-3 mb-4">
                             <?php if (!empty($formData)): ?>
                                 <dl class="row mb-0">
                                     <?php foreach ($formData as $key => $val): ?>
                                         <dt class="col-sm-4 text-muted text-capitalize"><?= esc(format_teks_wilayah(str_replace('_', ' ', $key))) ?></dt>
                                         <dd class="col-sm-8 fw-semibold text-dark mb-2"><?= nl2br(esc(is_array($val) ? json_encode($val) : $val)) ?></dd>
                                     <?php endforeach; ?>
                                 </dl>
                             <?php else: ?>
                                 <p class="text-muted mb-0 small">Tidak ada data isian khusus.</p>
                             <?php endif; ?>
                         </div>

                         <!-- Catatan Petugas / Admin jika ada -->
                         <?php if (!empty($p['catatan_operator']) || !empty($p['catatan_admin']) || !empty($p['catatan_penolakan'])): ?>
                             <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">
                                 <i class="bi bi-chat-left-quote me-1"></i> Catatan Petugas / Admin
                             </h6>
                             <?php if (!empty($p['catatan_operator'])): ?>
                                 <div class="alert alert-info py-2 px-3 mb-2 small">
                                     <strong>Catatan Operator:</strong> <?= esc($p['catatan_operator']) ?>
                                 </div>
                             <?php endif; ?>
                             <?php if (!empty($p['catatan_admin'])): ?>
                                 <div class="alert alert-success py-2 px-3 mb-2 small">
                                     <strong>Catatan Admin <?= sebutan_desa() ?>:</strong> <?= esc($p['catatan_admin']) ?>
                                 </div>
                             <?php endif; ?>
                             <?php if (!empty($p['catatan_penolakan'])): ?>
                                 <div class="alert alert-danger py-2 px-3 mb-2 small">
                                     <strong>Alasan Penolakan:</strong> <?= esc($p['catatan_penolakan']) ?>
                                 </div>
                             <?php endif; ?>
                         <?php endif; ?>

                        <!-- Dokumen Persyaratan Terlampir -->
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
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <?php if (in_array($p['status'], ['completed', 'selesai', 'approved_admin', 'ttd_kades', 'signed_kades', 'signed'])): ?>
                            <a href="/warga/permohonan/preview/<?= $p['id'] ?>" target="_blank" class="btn btn-outline-success">
                                <i class="bi bi-file-earmark-text me-1"></i> Buka Pratinjau Surat
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-secondary px-4 <?= !in_array($p['status'], ['completed', 'selesai', 'approved_admin', 'ttd_kades', 'signed_kades', 'signed']) ? 'ms-auto' : '' ?>" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
