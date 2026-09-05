<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('operator/transparansi') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Transparansi
    </a>
    <h2 class="h3 font-weight-bold mb-1">Tambah Laporan Transparansi <?= $istilah ?></h2>
    <p class="text-muted mb-0">Publikasikan rincian anggaran pendapatan, belanja per bidang, dan pembiayaan <?= strtolower($sebutan) ?>.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan Input:</h6>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('operator/transparansi/simpan') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- 1. Informasi Umum Laporan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Informasi Umum Laporan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="tahun_anggaran" class="form-label fw-semibold">Tahun Anggaran <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tahun_anggaran" name="tahun_anggaran" value="<?= old('tahun_anggaran', $tahunSekarang) ?>" required min="2020" max="2030">
                        </div>
                        <div class="col-md-4">
                            <label for="periode_anggaran" class="form-label fw-semibold">Periode / Tahap <span class="text-danger">*</span></label>
                            <select class="form-select" id="periode_anggaran" name="periode_anggaran" required>
                                <?php foreach ($periodeList as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (old('periode_anggaran', 'Tahap I') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nomor_pengumuman" class="form-label fw-semibold">No. Perdes / SK Pengesahan</label>
                            <input type="text" class="form-control font-monospace" id="nomor_pengumuman" name="nomor_pengumuman" value="<?= old('nomor_pengumuman', 'PERDES-' . $tahunSekarang . '/01') ?>" placeholder="Contoh: PERDES-2026/01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul Laporan Transparansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-6" id="judul" name="judul" value="<?= old('judul', 'Laporan Realisasi ' . $istilah . ' ' . $sebutan . ' Tahun Anggaran ' . $tahunSekarang . ' (Tahap I)') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ringkasan" class="form-label fw-semibold">Ringkasan Eksekutif (Singkat)</label>
                        <textarea class="form-control" id="ringkasan" name="ringkasan" rows="2" placeholder="Ringkasan 1-2 kalimat untuk pratinjau di dashboard..."><?= old('ringkasan') ?></textarea>
                    </div>

                    <div class="mb-0">
                        <label for="konten" class="form-label fw-semibold">Narasi & Penjelasan Laporan</label>
                        <textarea class="form-control" id="konten" name="konten" rows="4" placeholder="Tuliskan catatan penjelasan arah kebijakan, fokus belanja, atau kendala realisasi..."><?= old('konten') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Pos Pendapatan Desa -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-graph-up-arrow text-success me-2"></i>1. Pendapatan <?= $sebutan ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="total_pendapatan" class="form-label fw-semibold">Target Pendapatan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="total_pendapatan" name="total_pendapatan" value="<?= old('total_pendapatan', '1250000000') ?>" required min="0">
                            </div>
                            <small class="text-muted">Total target pendapatan pada APBDes/APBG.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="realisasi_pendapatan" class="form-label fw-semibold">Capaian Realisasi (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="realisasi_pendapatan" name="realisasi_pendapatan" value="<?= old('realisasi_pendapatan', '812500000') ?>" min="0">
                            </div>
                            <small class="text-muted">Realisasi penerimaan yang telah masuk kas desa.</small>
                        </div>
                        <div class="col-12">
                            <label for="pendapatan_ket" class="form-label fw-semibold">Keterangan Sumber Pendapatan</label>
                            <input type="text" class="form-control" id="pendapatan_ket" name="pendapatan_ket" value="<?= old('pendapatan_ket', 'Meliputi PADes, Dana Desa (DDS), ADD, dan Bagi Hasil Pajak.') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Rincian Belanja 5 Bidang -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-pie-chart text-primary me-2"></i>2. Belanja <?= $sebutan ?> (5 Bidang Permendagri)
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">5 Bidang Resmi</span>
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted mb-4">Isi nominal anggaran dan realisasi untuk masing-masing bidang belanja desa. Sistem akan otomatis menjumlahkan total belanja.</p>

                    <?php
                        $defaultValues = [
                            'bidang_1' => ['anggaran' => 325000000, 'realisasi' => 195000000, 'ket' => 'Penghasilan tetap aparatur, operasional kantor, BPD/Tuha Peut, dan tata praja.'],
                            'bidang_2' => ['anggaran' => 580000000, 'realisasi' => 377000000, 'ket' => 'Pembangunan jalan rabat beton, drainase pemukiman, dan sarana posyandu.'],
                            'bidang_3' => ['anggaran' => 95000000,  'realisasi' => 57000000,  'ket' => 'Kegiatan keagamaan, kepemudaan, olahraga, dan seni budaya lokal.'],
                            'bidang_4' => ['anggaran' => 140000000, 'realisasi' => 84000000,  'ket' => 'Pelatihan UMKM, ketahanan pangan hewani/nabati, dan bibit pertanian.'],
                            'bidang_5' => ['anggaran' => 110000000, 'realisasi' => 77000000,  'ket' => 'Penyaluran BLT Dana Desa dan tanggap darurat bencana.'],
                        ];
                    ?>

                    <?php foreach ($bidangList as $key => $meta): ?>
                        <?php $def = $defaultValues[$key] ?? ['anggaran' => 0, 'realisasi' => 0, 'ket' => '']; ?>
                        <div class="card bg-light border p-3 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> fs-5 me-2"></i>
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($meta['nama']) ?></h6>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Anggaran Belanja (Rp)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control input-belanja-anggaran" name="belanja_<?= $key ?>_anggaran" value="<?= old("belanja_{$key}_anggaran", $def['anggaran']) ?>" min="0" oninput="hitungTotalBelanja()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Realisasi (Rp)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control input-belanja-realisasi" name="belanja_<?= $key ?>_realisasi" value="<?= old("belanja_{$key}_realisasi", $def['realisasi']) ?>" min="0" oninput="hitungTotalBelanja()">
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <input type="text" class="form-control form-control-sm" name="belanja_<?= $key ?>_ket" value="<?= old("belanja_{$key}_ket", $def['ket']) ?>" placeholder="Uraian singkat kegiatan utama pada bidang ini...">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 4. Pos Pembiayaan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-arrow-left-right text-info me-2"></i>3. Pembiayaan <?= $sebutan ?> (SiLPA)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="total_pembiayaan" class="form-label fw-semibold">Penerimaan Pembiayaan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="total_pembiayaan" name="total_pembiayaan" value="<?= old('total_pembiayaan', '25000000') ?>" min="0">
                            </div>
                            <small class="text-muted">SiLPA tahun sebelumnya yang dicairkan.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="realisasi_pembiayaan" class="form-label fw-semibold">Realisasi Pembiayaan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="realisasi_pembiayaan" name="realisasi_pembiayaan" value="<?= old('realisasi_pembiayaan', '25000000') ?>" min="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="pembiayaan_ket" class="form-label fw-semibold">Keterangan Pembiayaan</label>
                            <input type="text" class="form-control" id="pembiayaan_ket" name="pembiayaan_ket" value="<?= old('pembiayaan_ket', 'Sisa Lebih Perhitungan Anggaran (SiLPA) Tahun Lalu.') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Kalkulator Otomatis -->
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 80px; z-index: 10;">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="bi bi-calculator me-2"></i>Ringkasan Kalkulasi Otomatis
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Belanja (Pagu):</span>
                        <span class="fw-bold text-dark" id="dispTotalBelanja">Rp 1.250.000.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Realisasi Belanja:</span>
                        <span class="fw-bold text-success" id="dispTotalRealisasi">Rp 790.000.000</span>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Tingkat Capaian:</span>
                            <span class="fw-bold text-primary" id="dispPersen">63.2%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="dispProgressBar" role="progressbar" style="width: 63.2%"></div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Berkas Lampiran PDF -->
                    <div class="mb-3">
                        <label for="lampiran" class="form-label fw-semibold small">Unggah Berkas Laporan Resmi (PDF/Gambar)</label>
                        <input type="file" class="form-control form-control-sm" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="text-muted" style="font-size: 0.72rem;">Lampirkan scan dokumen Perdes atau lembar infografis baliho (Maks 10 MB).</small>
                    </div>

                    <!-- Status Publikasi -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small d-block">Status Publikasi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_published" id="status1" value="1" <?= old('is_published', '1') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label text-success small fw-semibold" for="status1">
                                <i class="bi bi-broadcast me-1"></i> Terbitkan Sekarang
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_published" id="status0" value="0" <?= old('is_published') === '0' ? 'checked' : '' ?>>
                            <label class="form-check-label text-muted small" for="status0">
                                <i class="bi bi-file-earmark-lock me-1"></i> Simpan Sebagai Draft
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm py-2">
                            <i class="bi bi-check-circle me-1"></i> Simpan Laporan Transparansi
                        </button>
                        <a href="<?= base_url('operator/transparansi') ?>" class="btn btn-outline-secondary btn-sm">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}

function hitungTotalBelanja() {
    let totalAnggaran = 0;
    let totalRealisasi = 0;

    document.querySelectorAll('.input-belanja-anggaran').forEach(function(el) {
        let val = parseFloat(el.value) || 0;
        totalAnggaran += val;
    });

    document.querySelectorAll('.input-belanja-realisasi').forEach(function(el) {
        let val = parseFloat(el.value) || 0;
        totalRealisasi += val;
    });

    let persen = 0;
    if (totalAnggaran > 0) {
        persen = ((totalRealisasi / totalAnggaran) * 100).toFixed(1);
    }

    document.getElementById('dispTotalBelanja').innerText = formatRupiah(totalAnggaran);
    document.getElementById('dispTotalRealisasi').innerText = formatRupiah(totalRealisasi);
    document.getElementById('dispPersen').innerText = persen + '%';
    document.getElementById('dispProgressBar').style.width = Math.min(100, persen) + '%';
}

document.addEventListener('DOMContentLoaded', hitungTotalBelanja);
</script>
<?= $this->endSection() ?>
