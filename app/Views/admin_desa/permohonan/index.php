<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Approval Permohonan Surat</h2>
        <p class="text-muted mb-0">Daftar permohonan surat warga yang menunggu persetujuan <?= sebutan_kades() ?> / <?= sebutan_sekdes() ?>.</p>
    </div>
</div>

<div class="card bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Permohonan</th>
                    <th>Nama Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($permohonan)): ?>
                    <?php foreach($permohonan as $p): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($p['no_permohonan'] ?? $p['nomor_permohonan'] ?? $p['id']) ?></td>
                            <td><?= esc($p['nama_pemohon']) ?></td>
                            <td><?= esc(format_teks_wilayah($p['nama_surat'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                            <td>
                                <?php if($p['status'] === 'submitted' || $p['status'] === 'diajukan'): ?>
                                    <span class="badge bg-warning text-dark">Diajukan</span>
                                <?php elseif($p['status'] === 'verified_operator' || $p['status'] === 'diproses'): ?>
                                    <span class="badge bg-primary">Siap Approval</span>
                                <?php elseif(in_array($p['status'], ['approved_admin', 'completed', 'selesai', 'ttd_kades', 'signed_kades', 'signed'])): ?>
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="badge bg-success">Selesai</span>
                                        <?php $isSigned = !empty($p['ttd_digital_hash']) || in_array($p['status'], ['ttd_kades', 'signed_kades', 'signed']); ?>
                                        <button type="button" 
                                                class="btn btn-sm p-0 border-0 toggle-sign-btn" 
                                                data-id="<?= $p['id'] ?>" 
                                                data-signed="<?= $isSigned ? '1' : '0' ?>"
                                                title="Klik untuk mengubah status tanda tangan">
                                            <?php if($isSigned): ?>
                                                <span class="badge bg-primary text-white" style="cursor: pointer;"><i class="bi bi-pen-fill me-1"></i>TTD</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white" style="cursor: pointer;"><i class="bi bi-pen me-1"></i>Belum TTD</span>
                                            <?php endif; ?>
                                        </button>
                                    </div>
                                <?php elseif(in_array($p['status'], ['rejected', 'ditolak'])): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc(ucfirst(str_replace('_', ' ', $p['status']))) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(in_array($p['status'], ['verified_operator', 'submitted', 'diproses'])): ?>
                                    <form action="/admin-desa/permohonan/approve/<?= $p['id'] ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                                    </form>
                                    <form action="/admin-desa/permohonan/reject/<?= $p['id'] ?>" method="post" class="d-inline ms-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak permohonan ini?')"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                                    </form>
                                <?php elseif(in_array($p['status'], ['approved_admin', 'completed', 'selesai', 'ttd_kades', 'signed_kades', 'signed'])): ?>
                                    <a href="/admin-desa/permohonan/generate/<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-success fw-semibold">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Sudah Diproses</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada permohonan surat masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-sign-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const button = this;
            
            button.style.opacity = '0.5';
            button.style.pointerEvents = 'none';

            fetch('/admin-desa/permohonan/toggle-signed/' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto';
                if (data.success) {
                    if (data.is_signed) {
                        button.setAttribute('data-signed', '1');
                        button.innerHTML = '<span class="badge bg-primary text-white" style="cursor: pointer;"><i class="bi bi-pen-fill me-1"></i>TTD</span>';
                    } else {
                        button.setAttribute('data-signed', '0');
                        button.innerHTML = '<span class="badge bg-secondary text-white" style="cursor: pointer;"><i class="bi bi-pen me-1"></i>Belum TTD</span>';
                    }
                } else {
                    alert(data.message || 'Gagal mengubah status tanda tangan.');
                }
            })
            .catch(err => {
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto';
                console.error(err);
                alert('Terjadi kesalahan saat memproses status tanda tangan.');
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
