<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $prefix = session('role_slug') === 'operator' ? 'operator' : 'admin-desa'; ?>

<div class="mb-4">
    <a href="<?= base_url($prefix . '/pengaduan') ?>" class="text-decoration-none text-muted small fw-medium">
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</div>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Kolom Kiri: Detail Laporan & Form Tindak Lanjut -->
    <div class="col-lg-8">
        <!-- Rincian Laporan -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Isi Pengaduan / Keluhan Warga</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="text-muted small fw-semibold d-block mb-1">Rincian Laporan</label>
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
                    <div class="mb-2">
                        <label class="text-muted small fw-semibold d-block mb-2">Foto / Bukti Terlampir (<?= count($fotos) ?>)</label>
                        <div class="row g-3">
                            <?php foreach ($fotos as $foto): ?>
                                <div class="col-6 col-md-4">
                                    <a href="<?= base_url($foto) ?>" target="_blank" class="d-block text-decoration-none">
                                        <div class="card border h-100 overflow-hidden shadow-sm">
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

        <!-- Form Tindak Lanjut & Tanggapan -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-reply-fill text-primary me-2"></i>Form Tindak Lanjut & Tanggapan Resmi
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url($prefix . '/pengaduan/' . $pengaduan['id'] . '/tanggapi') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status Pengaduan <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <?php foreach ($statusLabels as $k => $label): ?>
                                <option value="<?= $k ?>" <?= ($pengaduan['status'] === $k) ? 'selected' : '' ?>><?= $label ?> (<?= $k ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih 'Diproses' jika sedang ditindaklanjuti lapangan, atau 'Selesai' jika masalah telah diselesaikan.</div>
                    </div>

                    <div class="mb-4">
                        <label for="tanggapan" class="form-label fw-bold">Tanggapan / Catatan Penyelesaian <span class="text-danger">*</span></label>
                        <textarea name="tanggapan" id="tanggapan" rows="5" class="form-control" placeholder="Tuliskan tanggapan resmi pemerintah desa atau laporan hasil penanganan lapangan..." required><?= old('tanggapan', $pengaduan['tanggapan'] ?? '') ?></textarea>
                        <div class="form-text">Tanggapan ini akan dapat dilihat langsung oleh warga pelapor di akun portal mereka.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url($prefix . '/pengaduan') ?>" class="btn btn-light px-3">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Tanggapan & Perbarui Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Info Pelapor & Metadata -->
    <div class="col-lg-4">
        <!-- Info Pelapor -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title fw-bold mb-0 text-dark"><i class="bi bi-person-fill text-primary me-2"></i>Identitas Pelapor</h6>
            </div>
            <div class="card-body p-4">
                <?php if ($pengaduan['is_anonymous']): ?>
                    <div class="p-3 bg-light rounded text-center text-muted">
                        <i class="bi bi-incognito fs-2 d-block mb-1 text-secondary"></i>
                        <strong>Pelapor Memilih Anonim</strong>
                        <div class="small">Identitas disamarkan dari catatan publik.</div>
                    </div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">Nama:</span>
                            <span class="fw-semibold text-dark"><?= esc($pengaduan['nama_pelapor'] ?? '-') ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">NIK:</span>
                            <span class="fw-semibold text-dark font-monospace"><?= esc($pengaduan['nik_pelapor'] ?? '-') ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">Email:</span>
                            <span class="fw-semibold text-dark"><?= esc($pengaduan['email_pelapor'] ?? '-') ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">No. HP / WA:</span>
                            <span class="fw-semibold text-dark"><?= esc($pengaduan['hp_pelapor'] ?? '-') ?></span>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Meta & Timeline -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Aduan</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal Masuk:</span>
                        <span class="fw-semibold text-dark"><?= date('d F Y, H:i', strtotime($pengaduan['created_at'])) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Kategori:</span>
                        <span class="fw-semibold text-dark"><?= esc($pengaduan['kategori']) ?></span>
                    </li>
                    <?php if (!empty($petugas)): ?>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">Ditangani Oleh:</span>
                            <span class="fw-semibold text-primary"><?= esc($petugas['nama_lengkap']) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($pengaduan['resolved_at'])): ?>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">Waktu Selesai:</span>
                            <span class="fw-semibold text-success"><?= date('d F Y, H:i', strtotime($pengaduan['resolved_at'])) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
