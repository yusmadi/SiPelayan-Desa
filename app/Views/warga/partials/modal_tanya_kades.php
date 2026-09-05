<?php
$desa = $desa ?? desa_info();
$namaKades = !empty($desa['nama_kepala_desa']) ? $desa['nama_kepala_desa'] : (sebutan_kades() . ' ' . ($desa['nama_desa'] ?? session('nama_desa')));
$namaDesa = $desa['nama_desa'] ?? session('nama_desa') ?? 'Desa';
$whatsappKades = $desa['whatsapp_kades'] ?? '';
$cleanWa = preg_replace('/[^0-9]/', '', $whatsappKades);
if (str_starts_with($cleanWa, '0')) {
    $cleanWa = '62' . substr($cleanWa, 1);
}
?>

<!-- Modal Tanya Kades / Keuchik -->
<div class="modal fade" id="modalTanyaKades" tabindex="-1" aria-labelledby="modalTanyaKadesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white text-teal p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; color: #0f766e;">
                        <i class="bi bi-chat-dots-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTanyaKadesLabel">Tanya <?= sebutan_kades() ?></h5>
                        <small class="opacity-75">Konsultasi langsung <?= strtolower(sebutan_desa()) ?> <?= esc($namaDesa) ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Info Kades Card -->
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3 border">
                    <div class="position-relative">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($namaKades) ?>&background=0d9488&color=fff&size=96" 
                             alt="<?= esc($namaKades) ?>" 
                             class="rounded-circle shadow-sm" 
                             width="52" height="52">
                        <?php if (!empty($whatsappKades)): ?>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" title="WhatsApp Aktif" style="width: 14px; height: 14px;"></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <span class="badge mb-1" style="color: #0f766e; background-color: #ccfbf1; border: 1px solid #99f6e4;">
                            <?= sebutan_kades() ?> <?= esc($namaDesa) ?>
                        </span>
                        <h6 class="fw-bold mb-0 text-dark"><?= esc($namaKades) ?></h6>
                        <small class="text-muted d-block">
                            <?php if (!empty($whatsappKades)): ?>
                                <i class="bi bi-whatsapp text-success me-1"></i><?= esc($whatsappKades) ?>
                            <?php else: ?>
                                <i class="bi bi-exclamation-circle text-warning me-1"></i>Kontak WhatsApp belum didaftarkan
                            <?php endif; ?>
                        </small>
                    </div>
                </div>

                <?php if (!empty($whatsappKades)): ?>
                    <form id="formTanyaKades" onsubmit="handleKirimTanyaKades(event)">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Pemohon / Warga</label>
                            <input type="text" class="form-control bg-light" value="<?= esc(session('nama_lengkap')) ?> (NIK: <?= esc(session('nik')) ?>)" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="topikKonsultasi" class="form-label small fw-semibold text-secondary">Topik Pertanyaan</label>
                            <select class="form-select" id="topikKonsultasi" required>
                                <option value="Konsultasi Pelayanan & Surat">Konsultasi Pelayanan & Surat</option>
                                <option value="Bantuan Sosial & Kesejahteraan">Bantuan Sosial & Kesejahteraan</option>
                                <option value="Ketertiban & Keamanan Lingkungan">Ketertiban & Keamanan Lingkungan</option>
                                <option value="Usulan Pembangunan <?= sebutan_desa() ?>">Usulan Pembangunan <?= sebutan_desa() ?></option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="isiPertanyaan" class="form-label small fw-semibold text-secondary">Pesan / Pertanyaan Anda</label>
                            <textarea class="form-control" id="isiPertanyaan" rows="3" placeholder="Tuliskan pertanyaan atau hal yang ingin Anda konsultasikan secara sopan..." required></textarea>
                            <div class="form-text text-muted small">Pesan ini akan otomatis dialihkan ke WhatsApp resmi <?= sebutan_kades() ?>.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="bi bi-whatsapp fs-5"></i> Kirim ke WhatsApp <?= sebutan_kades() ?>
                            </button>
                            <button type="button" class="btn btn-light py-2 text-muted" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning border-warning d-flex align-items-start gap-2 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 text-warning"></i>
                        <div>
                            <div class="fw-bold">Kontak WhatsApp Belum Tersedia</div>
                            <div class="small">Nomor WhatsApp resmi <?= sebutan_kades() ?> belum didaftarkan oleh administrator <?= strtolower(sebutan_desa()) ?>. Anda dapat menyampaikan aspirasi atau permohonan melalui saluran berikut:</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?= base_url('warga/pengaduan/buat') ?>" class="btn btn-primary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-megaphone-fill"></i> Buat Pengaduan / Aspirasi
                        </a>
                        <button type="button" class="btn btn-light py-2 text-muted" data-bs-dismiss="modal">Tutup</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function handleKirimTanyaKades(e) {
    e.preventDefault();
    const topik = document.getElementById('topikKonsultasi').value;
    const pertanyaan = document.getElementById('isiPertanyaan').value.trim();
    const namaWarga = <?= json_encode(session('nama_lengkap') ?? '') ?>;
    const nikWarga = <?= json_encode(session('nik') ?? '') ?>;
    const kadesName = <?= json_encode($namaKades) ?>;
    const sebutanKades = <?= json_encode(sebutan_kades()) ?>;
    const sebutanDesa = <?= json_encode(sebutan_desa()) ?>;
    const namaDesa = <?= json_encode($namaDesa) ?>;
    const waNumber = <?= json_encode($cleanWa) ?>;

    if (!pertanyaan) {
        alert('Silakan tuliskan pertanyaan terlebih dahulu.');
        return;
    }

    const text = `*TANYA ${sebutanKades.toUpperCase()}*\n\n` +
                 `Kepada Yth. Bapak/Ibu ${kadesName}\n` +
                 `(${sebutanKades} ${namaDesa})\n\n` +
                 `Assalamu'alaikum Wr. Wb. / Salam Sejahtera,\n\n` +
                 `Perkenalkan saya warga ${sebutanDesa} ${namaDesa}:\n` +
                 `• *Nama:* ${namaWarga}\n` +
                 `• *NIK:* ${nikWarga}\n` +
                 `• *Topik:* ${topik}\n\n` +
                 `*Isi Pertanyaan / Konsultasi:*\n` +
                 `${pertanyaan}\n\n` +
                 `_Dikirim melalui Portal Layanan Warga SiPelayan-${sebutanDesa}_`;

    const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`;
    window.open(waUrl, '_blank');

    // Tutup modal
    const modalEl = document.getElementById('modalTanyaKades');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
        modal.hide();
    }
}
</script>
