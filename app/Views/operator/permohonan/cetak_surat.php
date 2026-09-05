<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Surat Keterangan Desa') ?></title>
    <!-- Blank Favicon to prevent favicon.ico icon rendering in PDF/Print -->
    <link rel="icon" href="data:,">
    <link rel="shortcut icon" href="data:,">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0284c7;
        }

        body {
            background-color: #e2e8f0;
            font-family: 'Plus+Jakarta+Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* Top Action Bar */
        .top-action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* Surat Paper Container */
        .surat-wrapper {
            padding: 30px 15px 60px 15px;
            display: flex;
            justify-content: center;
        }

        .surat-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 7mm 22mm 25mm 22mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            font-family: 'Tinos', 'Times New Roman', serif;
            font-size: 11.5pt;
            line-height: 1.5;
            color: #000000;
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Bookman Old Style', 'Bookman', 'URW Bookman L', serif;
        }

        .kop-logo {
            width: 95px;
            height: 95px;
            min-width: 95px;
            margin-right: 18px;
        }

        .kop-logo img,
        .kop-logo-img {
            max-width: 95px;
            max-height: 95px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .kop-teks {
            flex: 1;
            font-family: 'Bookman Old Style', 'Bookman', 'URW Bookman L', serif;
        }

        .kop-teks h4 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Bookman Old Style', 'Bookman', 'URW Bookman L', serif;
        }

        .kop-teks h3 {
            font-size: 18pt;
            font-weight: 800;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Bookman Old Style', 'Bookman', 'URW Bookman L', serif;
        }

        .kop-teks p {
            font-size: 9.5pt;
            margin: 0;
            font-style: italic;
            font-family: 'Bookman Old Style', 'Bookman', 'URW Bookman L', serif;
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin: 20px 0 25px 0;
        }

        .judul-surat h4 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .judul-surat p {
            font-size: 11pt;
            margin: 0;
        }

        /* Editable inline fields */
        .editable-field {
            border-bottom: 1px dashed #0284c7;
            padding: 0 4px;
            cursor: pointer;
            border-radius: 2px;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .editable-field:hover {
            background-color: #e0f2fe;
            border-bottom-color: #0369a1;
        }

        .editable-field:focus {
            outline: 2px solid #0284c7;
            background-color: #ffffff;
            border-bottom-color: transparent;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        /* Isi Surat */
        .isi-surat {
            text-align: justify;
            line-height: 1.6;
        }

        .tabel-identitas {
            width: 100%;
            margin: 12px 0 16px 15px;
        }

        .tabel-identitas td {
            padding: 3px 6px;
            vertical-align: top;
            font-size: 11pt;
        }

        .tabel-identitas td.label {
            width: 180px;
        }

        .tabel-identitas td.colon {
            width: 15px;
            text-align: center;
        }

        .box-detail-usaha, .box-detail-khusus {
            background-color: #fdfdfd;
            border-left: 3px solid #000;
            padding: 10px 18px;
            margin: 12px 0;
        }

        /* Tanda Tangan */
        .ttd-section {
            margin-top: 35px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 260px;
            text-align: center;
        }

        .ttd-space {
            height: 75px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* Toast notification */
        #toastSave {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: none;
        }

        /* Print Media Styles */
        @page {
            size: A4 portrait;
            margin: 0mm !important; /* Menghilangkan margin browser agar link URL, judul, nomor halaman, tanggal/jam & favicon tidak tercetak */
        }

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print, #toastSave, .modal, .modal-backdrop {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }
            .surat-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            .surat-sheet {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 210mm !important;
                min-height: auto !important;
                padding: 5mm 20mm 15mm 20mm !important;
                margin: 0 auto !important;
                box-sizing: border-box !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            .editable-field {
                border-bottom: none !important;
                background-color: transparent !important;
                outline: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <?php
        // Helper formatting tanggal Indonesia
        if (!function_exists('tgl_indo')) {
            function tgl_indo($tanggal) {
                if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
                return (int)$pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
            }
        }

        if (!function_exists('hari_tgl_indo')) {
            function hari_tgl_indo($tanggal) {
                if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
                if (!strtotime($tanggal) && is_string($tanggal) && strlen($tanggal) > 10) {
                    return $tanggal;
                }
                $namaHari = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                ];
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $ts = strtotime($tanggal);
                if (!$ts) return $tanggal;
                $dayName = $namaHari[date('l', $ts)] ?? '';
                $pecahkan = explode('-', date('Y-m-d', $ts));
                $tglStr = (int)$pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
                return $dayName ? "{$dayName}, {$tglStr}" : $tglStr;
            }
        }

        $villageId = (int) ($permohonan['village_id'] ?? session('village_id') ?? 1);
        $lblDesa = sebutan_desa($villageId);
        $lblDesaUpper = sebutan_desa($villageId, 'upper');
        $lblKades = sebutan_kades($villageId);
        $lblKadesUpper = sebutan_kades($villageId, 'upper');

        $kodeSurat = strtoupper($permohonan['kode_surat'] ?? 'SKD');
        $namaDesa = !empty($permohonan['nama_desa']) ? $permohonan['nama_desa'] : 'Sukamaju';
        $namaKecamatan = !empty($permohonan['nama_kecamatan']) ? $permohonan['nama_kecamatan'] : '';
        $namaKabupaten = !empty($permohonan['nama_kabupaten']) ? $permohonan['nama_kabupaten'] : '';
        $alamatKantor = !empty($permohonan['alamat_kantor']) ? $permohonan['alamat_kantor'] : '';
        $teleponDesa = !empty($permohonan['telepon_desa']) ? $permohonan['telepon_desa'] : '';
        $emailDesa = !empty($permohonan['email_desa']) ? $permohonan['email_desa'] : '';
        $namaKades = !empty($permohonan['nama_kepala_desa']) ? $permohonan['nama_kepala_desa'] : $lblKades;
        $nipKades = (!empty($permohonan['nip_kepala_desa']) && trim($permohonan['nip_kepala_desa']) !== '-' && trim($permohonan['nip_kepala_desa']) !== '') ? trim($permohonan['nip_kepala_desa']) : null;

        // Logo dari database
        $logoSrc = '';
        if (!empty($permohonan['logo_path'])) {
            $rawLogo = trim($permohonan['logo_path']);
            if (str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) {
                $logoSrc = $rawLogo;
            } elseif (file_exists(FCPATH . ltrim($rawLogo, '/'))) {
                $logoSrc = base_url(ltrim($rawLogo, '/'));
            } elseif (file_exists(FCPATH . 'uploads/' . ltrim($rawLogo, '/'))) {
                $logoSrc = base_url('uploads/' . ltrim($rawLogo, '/'));
            } else {
                $logoSrc = base_url(ltrim($rawLogo, '/'));
            }
        }

        $isKeramaian = ($kodeSurat === 'KERAMAIAN' || $kodeSurat === 'SIK' || $kodeSurat === 'SKIK' || stripos($permohonan['nama_surat'] ?? '', 'Keramaian') !== false || stripos($kodeSurat, 'KERAMAIAN') !== false);
        $isDomisili = ($kodeSurat === 'SKD' || stripos($permohonan['nama_surat'] ?? '', 'Domisili') !== false || stripos($kodeSurat, 'DOMISILI') !== false);

        // Parse detail isian kegiatan keramaian
        $dalamRangka = !empty($formData['dalam_rangka']) ? $formData['dalam_rangka'] : (!empty($formData['nama_acara']) ? $formData['nama_acara'] : (!empty($formData['keperluan']) ? $formData['keperluan'] : (!empty($formData['rangka']) ? $formData['rangka'] : 'Kegiatan Acara Keramaian Warga')));

        $tglAcaraRaw = $formData['tanggal_pelaksanaan'] ?? $formData['tanggal_acara'] ?? $formData['hari_tanggal'] ?? $formData['tanggal'] ?? null;
        $hariTanggal = !empty($formData['hari_tanggal']) ? $formData['hari_tanggal'] : (!empty($tglAcaraRaw) ? hari_tgl_indo($tglAcaraRaw) : hari_tgl_indo(date('Y-m-d', strtotime('+3 days'))));

        $pukul = !empty($formData['waktu_pelaksanaan']) ? $formData['waktu_pelaksanaan'] : (!empty($formData['pukul']) ? $formData['pukul'] : (!empty($formData['jam']) ? $formData['jam'] : '08.00 WIB s.d. selesai'));

        $tempatLokasi = !empty($formData['lokasi_kegiatan']) ? $formData['lokasi_kegiatan'] : (!empty($formData['tempat']) ? $formData['tempat'] : (!empty($formData['lokasi']) ? $formData['lokasi'] : (!empty($permohonan['alamat_penduduk']) ? $permohonan['alamat_penduduk'] : ($lblDesa . ' ' . $namaDesa))));

        $acaraHiburan = !empty($formData['jenis_hiburan']) ? $formData['jenis_hiburan'] : (!empty($formData['acara']) ? $formData['acara'] : (!empty($formData['hiburan']) ? $formData['hiburan'] : (!empty($formData['nama_acara']) ? $formData['nama_acara'] : 'Orgen Tunggal / Wayang Kulit / Hiburan Warga')));

        // Parse detail isian surat domisili
        $alamatAsal = !empty($formData['alamat_asal']) ? $formData['alamat_asal'] : (!empty($formData['alamat_ktp']) ? $formData['alamat_ktp'] : (!empty($formData['alamat_sekarang']) ? $formData['alamat_sekarang'] : (!empty($permohonan['alamat_penduduk']) ? $permohonan['alamat_penduduk'] : 'Sesuai Domisili')));
        $keperluanDomisili = !empty($formData['keperluan']) ? $formData['keperluan'] : (!empty($formData['tujuan']) ? $formData['tujuan'] : 'pengurusan administrasi atau keperluan penting lainnya yang memerlukan bukti domisili yang sah');

        $tanggalSurat = !empty($formData['tanggal_surat']) ? $formData['tanggal_surat'] : tgl_indo(date('Y-m-d'));

        $rtDesa = !empty($permohonan['rt']) ? $permohonan['rt'] : '001';
        $rwDesa = !empty($permohonan['rw']) ? $permohonan['rw'] : '002';
        $dusunDesa = !empty($permohonan['dusun']) ? $permohonan['dusun'] : 'Dusun Krajan';
        
        $roleSlug = session('role_slug') ?? 'operator';
        $backUrl = ($roleSlug === 'admin_desa') ? '/admin-desa/permohonan' : '/operator/permohonan';
        $updateSuratUrl = ($roleSlug === 'admin_desa') ? '/admin-desa/permohonan/update-surat/' . $permohonan['id'] : '/operator/permohonan/update-surat/' . $permohonan['id'];
    ?>

    <!-- Top Action Bar (Hanya tampil di layar) -->
    <div class="top-action-bar no-print d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= $backUrl ?>" class="btn btn-outline-secondary btn-sm" onclick="if(window.history.length > 1) { window.history.back(); return false; }">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                    <i class="bi bi-check-circle me-1"></i> Status: Disetujui / Selesai
                </span>
                <span class="text-muted ms-2 small d-none d-md-inline">No. Reg: <strong><?= esc($permohonan['no_permohonan']) ?></strong></span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Edit Detail Surat Modal Button -->
            <?php if ($isKeramaian): ?>
            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditSurat">
                <i class="bi bi-pencil-square me-1"></i> Edit Detail Acara & Tanggal
            </button>
            <?php elseif ($isDomisili): ?>
            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditDomisili">
                <i class="bi bi-pencil-square me-1"></i> Edit Detail Domisili
            </button>
            <?php endif; ?>

            <!-- Edit Nomor Surat Cepat -->
            <div class="input-group input-group-sm" style="width: auto; min-width: 260px; max-width: 340px;">
                <span class="input-group-text bg-light text-muted fw-semibold" title="Nomor Surat Keluar"><i class="bi bi-hash me-1"></i>No. Surat:</span>
                <input type="text" id="inputNomorSurat" class="form-control fw-bold text-dark font-monospace" value="<?= esc($permohonan['no_surat_keluar']) ?>" placeholder="Nomor Surat">
                <button class="btn btn-primary" type="button" id="btnSimpanCepat" title="Simpan Perubahan">
                    <i class="bi bi-floppy me-1"></i> Simpan
                </button>
            </div>

            <button onclick="window.print()" class="btn btn-success btn-sm fw-semibold shadow-sm px-3">
                <i class="bi bi-printer-fill me-1"></i> Cetak / PDF
            </button>
        </div>
    </div>

    <!-- Lembar Surat Format Resmi A4 -->
    <div class="surat-wrapper">
        <div class="surat-sheet">
            
            <!-- KOP SURAT RESMI PEMERINTAH DESA / GAMPONG (LOGO DATABASE) -->
            <div class="kop-surat">
                <div class="kop-logo d-flex align-items-center justify-content-center">
                    <?php if (!empty($logoSrc)): ?>
                        <img src="<?= esc($logoSrc) ?>" alt="Logo <?= $lblDesa ?>" class="kop-logo-img">
                    <?php else: ?>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Coat_of_arms_of_Indonesia.svg/800px-Coat_of_arms_of_Indonesia.svg.png" alt="Logo Garuda" class="kop-logo-img">
                    <?php endif; ?>
                </div>
                <div class="kop-teks">
                    <h4>PEMERINTAH KABUPATEN <?= esc(strtoupper(str_replace('KABUPATEN ', '', $namaKabupaten))) ?></h4>
                    <h4>KECAMATAN <?= esc(strtoupper($namaKecamatan)) ?></h4>
                    <h3><?= $lblDesaUpper ?> <?= esc(strtoupper($namaDesa)) ?></h3>
                    <p><?= esc($alamatKantor) ?><?= !empty($teleponDesa) ? ' | Telp: ' . esc($teleponDesa) : '' ?><?= !empty($emailDesa) ? ' | Email: ' . esc($emailDesa) : '' ?></p>
                </div>
            </div>

            <!-- JUDUL & NOMOR SURAT (NOMOR DAPAT DIEDIT) -->
            <div class="judul-surat">
                <h4><?= esc(strtoupper(format_teks_wilayah($permohonan['nama_surat'] ?? ('SURAT KETERANGAN ' . $lblDesaUpper), $villageId))) ?></h4>
                <p>Nomor: <span id="displayNomorSurat" class="editable-field fw-bold" contenteditable="true" title="Klik untuk mengedit nomor surat" data-field="no_surat_keluar"><?= esc($permohonan['no_surat_keluar']) ?></span><button type="button" id="btnFocusEdit" class="btn btn-sm btn-outline-secondary py-0 px-1 ms-1 border-0 no-print" title="Klik untuk edit"><i class="bi bi-pencil"></i></button></p>
            </div>

            <!-- ISI SURAT -->
            <div class="isi-surat">
                
                <?php if ($isKeramaian): ?>
                    <!-- ========================================================
                         FORMAT RESMI: SURAT IZIN KERAMAIAN
                         ======================================================== -->
                    <!-- Bagian Pembuka: Kepala Desa / Keuchik [Nama Desa] (dari database) -->
                    <p><?= $lblKades ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, Kabupaten <?= esc($namaKabupaten) ?>, dengan ini menerangkan bahwa:</p>

                    <!-- TABEL IDENTITAS PEMOHON -->
                    <table class="tabel-identitas">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc($permohonan['nama_pemohon']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK / No. KTP</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['nik_penduduk'] ?? $permohonan['nik_user'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['pekerjaan'] ?? 'Wiraswasta') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat</td>
                            <td class="colon">:</td>
                            <td>RT <?= esc($rtDesa) ?> / RW <?= esc($rwDesa) ?>, Dusun <?= esc($dusunDesa) ?>, <?= $lblDesa ?> <?= esc($namaDesa) ?></td>
                        </tr>
                    </table>

                    <p style="margin-top: 14px;">Adalah benar warga <?= $lblDesa ?> <?= esc($namaDesa) ?> yang telah mengajukan permohonan izin untuk menyelenggarakan kegiatan keramaian dalam rangka <span id="displayDalamRangka" class="editable-field fw-bold" contenteditable="true" data-field="dalam_rangka" title="Klik untuk mengedit"><?= esc($dalamRangka) ?></span>.</p>

                    <p style="margin-top: 14px; margin-bottom: 4px;">Kegiatan tersebut akan diselenggarakan pada:</p>

                    <!-- DETAIL ACARA DAPAT DIEDIT OLEH OPERATOR DESA -->
                    <table class="tabel-identitas" style="margin-top: 4px; margin-bottom: 14px;">
                        <tr>
                            <td class="label">Hari / Tanggal</td>
                            <td class="colon">:</td>
                            <td><span id="displayHariTanggal" class="editable-field fw-bold" contenteditable="true" data-field="hari_tanggal" title="Klik untuk mengedit"><?= esc($hariTanggal) ?></span></td>
                        </tr>
                        <tr>
                            <td class="label">Pukul</td>
                            <td class="colon">:</td>
                            <td><span id="displayPukul" class="editable-field" contenteditable="true" data-field="pukul" title="Klik untuk mengedit"><?= esc($pukul) ?></span></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat / Lokasi</td>
                            <td class="colon">:</td>
                            <td><span id="displayTempatLokasi" class="editable-field" contenteditable="true" data-field="tempat" title="Klik untuk mengedit"><?= esc($tempatLokasi) ?></span></td>
                        </tr>
                        <tr>
                            <td class="label">Acara / Hiburan</td>
                            <td class="colon">:</td>
                            <td><span id="displayAcaraHiburan" class="editable-field" contenteditable="true" data-field="acara" title="Klik untuk mengedit"><?= esc($acaraHiburan) ?></span></td>
                        </tr>
                    </table>

                    <p style="margin-top: 14px; margin-bottom: 8px;">Sehubungan dengan penyelenggaraan kegiatan tersebut, pihak Pemerintah <?= $lblDesa ?> pada prinsipnya memberikan izin, dengan ketentuan dan kewajiban pemohon sebagai berikut:</p>

                    <ol style="margin-top: 0; margin-bottom: 14px; padding-left: 24px; text-align: justify; line-height: 1.6;">
                        <li style="margin-bottom: 5px;">Wajib menjaga keamanan, ketertiban, dan kebersihan di sekitar lokasi kegiatan.</li>
                        <li style="margin-bottom: 5px;">Bertanggung jawab penuh atas segala akibat yang timbul dari penyelenggaraan kegiatan ini baik sebelum, pada saat, maupun sesudah acara berlangsung.</li>
                        <li style="margin-bottom: 5px;">Tidak diperkenankan menyediakan atau mengonsumsi minuman keras, narkotika, serta melakukan tindakan apa pun yang melanggar hukum dan norma yang berlaku di masyarakat.</li>
                        <li style="margin-bottom: 5px;">Wajib menghentikan kegiatan tepat waktu sesuai dengan ketentuan jam yang telah disetujui dalam surat izin ini.</li>
                    </ol>

                    <p style="margin-top: 14px;">Demikian Surat Keterangan Izin Keramaian ini diterbitkan untuk dapat dipergunakan sebagaimana mestinya dan sebagai bahan pengurusan tembusan lebih lanjut kepada instansi terkait (jika diperlukan).</p>

                <?php elseif ($isDomisili): ?>
                    <!-- ========================================================
                         FORMAT RESMI: SURAT KETERANGAN DOMISILI
                         ======================================================== -->
                    <p>Yang bertanda tangan di bawah ini <?= $lblKades ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, <?= (stripos(trim($namaKabupaten), 'kabupaten') === 0 || stripos(trim($namaKabupaten), 'kota') === 0) ? esc(trim($namaKabupaten)) : 'Kabupaten ' . esc(trim($namaKabupaten)) ?>, dengan ini menerangkan bahwa:</p>

                    <!-- TABEL IDENTITAS PEMOHON -->
                    <table class="tabel-identitas">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc($permohonan['nama_pemohon']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK / No. KTP</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['nik_penduduk'] ?? $permohonan['nik_user'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat, Tanggal Lahir</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['tempat_lahir'] ?? ($lblDesa . ' ' . $namaDesa)) ?>, <?= tgl_indo($permohonan['tanggal_lahir'] ?? '1990-01-01') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="colon">:</td>
                            <td><?= (($permohonan['jenis_kelamin'] ?? 'L') === 'L' || ($permohonan['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <td class="label">Agama</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['agama'] ?? 'Islam') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['pekerjaan'] ?? 'Wiraswasta') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status Perkawinan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['status_perkawinan'] ?? 'Kawin') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat Asal (KTP)</td>
                            <td class="colon">:</td>
                            <td><span id="displayAlamatAsal" class="editable-field" contenteditable="true" data-field="alamat_asal" title="Klik untuk mengedit"><?= esc($alamatAsal) ?></span></td>
                        </tr>
                    </table>

                    <p style="margin-top: 14px;">Berdasarkan pengamatan dan data administrasi kependudukan kami serta laporan dari Ketua RT/RW setempat, nama tersebut benar-benar warga penduduk yang bertempat tinggal dan berdomisili di <?= $lblDesa ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, <?= (stripos(trim($namaKabupaten), 'kabupaten') === 0 || stripos(trim($namaKabupaten), 'kota') === 0) ? esc(trim($namaKabupaten)) : 'Kabupaten ' . esc(trim($namaKabupaten)) ?>. Dan berdasarkan keterangan yang ada, yang bersangkutan berkelakuan baik, serta aktif dalam kehidupan sosial bermasyarakat di lingkungan <?= $lblDesa ?> <?= esc($namaDesa) ?>.</p>

                    <p style="margin-top: 12px; margin-bottom: 4px;">Surat Keterangan Domisili ini diberikan kepada yang bersangkutan agar dapat dipergunakan untuk keperluan:</p>

                    <div style="text-align: center; margin: 8px 0 12px 0; font-weight: bold; font-size: 11.5pt;">
                        <span id="displayKeperluanDomisili" class="editable-field fw-bold" contenteditable="true" data-field="keperluan" title="Klik untuk mengedit"><?= esc($keperluanDomisili) ?></span>
                    </div>

                    <p style="margin-top: 12px;">Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya.</p>

                <?php else: ?>
                    
                    <!-- ========================================================
                         FORMAT SURAT LAINNYA (SKTM, SKU, SKCK, DLL)
                         ======================================================== -->
                    <p>Yang bertanda tangan di bawah ini <?= $lblKades ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, Kabupaten <?= esc($namaKabupaten) ?>, dengan ini menerangkan bahwa:</p>

                    <!-- TABEL DATA IDENTITAS PEMOHON -->
                    <table class="tabel-identitas">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc($permohonan['nama_pemohon']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['nik_penduduk'] ?? $permohonan['nik_user'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">No. Kartu Keluarga</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['no_kk'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat / Tgl. Lahir</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['tempat_lahir'] ?? ($lblDesa . ' ' . $namaDesa)) ?>, <?= tgl_indo($permohonan['tanggal_lahir'] ?? '1990-01-01') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="colon">:</td>
                            <td><?= (($permohonan['jenis_kelamin'] ?? 'L') === 'L' || ($permohonan['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <td class="label">Agama</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['agama'] ?? 'Islam') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['pekerjaan'] ?? 'Wiraswasta') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status Perkawinan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['status_perkawinan'] ?? 'Kawin') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Kewarganegaraan</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['kewarganegaraan'] ?? 'WNI') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat / Domisili</td>
                            <td class="colon">:</td>
                            <td><?= esc($permohonan['alamat_penduduk'] ?? ($lblDesa . ' ' . $namaDesa . ', RT 01 RW 02')) ?></td>
                        </tr>
                    </table>

                    <!-- KETERANGAN KHUSUS SESUAI JENIS SURAT -->
                    <?php if ($kodeSurat === 'SKTM'): ?>
                        <p>Menerangkan dengan sebenarnya bahwa orang tersebut di atas adalah benar-benar warga penduduk <?= $lblDesa ?> <?= esc($namaDesa) ?> yang tergolong dalam keluarga <strong>Kurang Mampu / Pra-Sejahtera</strong> secara ekonomi.</p>
                        
                        <div class="box-detail-khusus">
                            <table style="width: 100%; font-size: 11pt;">
                                <tr>
                                    <td style="width: 170px;">Estimasi Penghasilan</td>
                                    <td style="width: 15px; text-align: center;">:</td>
                                    <td>Rp <?= number_format((float)($formData['penghasilan_perbulan'] ?? 1000000), 0, ',', '.') ?> / bulan</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Tanggungan</td>
                                    <td style="text-align: center;">:</td>
                                    <td><?= esc($formData['jumlah_tanggungan'] ?? '3') ?> Orang</td>
                                </tr>
                                <tr>
                                    <td>Keperluan Surat</td>
                                    <td style="text-align: center;">:</td>
                                    <td><strong><?= esc($formData['keperluan'] ?? 'Pengajuan Keringanan Biaya / Beasiswa') ?></strong></td>
                                </tr>
                            </table>
                        </div>

                        <p>Surat keterangan ini diberikan atas permohonan yang bersangkutan untuk keperluan <strong><?= esc($formData['keperluan'] ?? 'Pengajuan Keringanan / Bantuan Sosial') ?></strong>.</p>

                    <?php elseif ($kodeSurat === 'SKU'): ?>
                        <p>Menerangkan dengan sebenarnya bahwa orang tersebut di atas adalah benar warga <?= $lblDesa ?> <?= esc($namaDesa) ?> yang memiliki dan menjalankan usaha mandiri dengan data sebagai berikut:</p>
                        
                        <div class="box-detail-usaha">
                            <table style="width: 100%; font-size: 11pt;">
                                <tr>
                                    <td style="width: 160px;">Nama Usaha / Toko</td>
                                    <td style="width: 15px; text-align: center;">:</td>
                                    <td><strong><?= esc($formData['nama_usaha'] ?? 'Warung Usaha Mandiri') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Bidang Usaha</td>
                                    <td style="text-align: center;">:</td>
                                    <td><?= esc($formData['bidang_usaha'] ?? 'Perdagangan') ?></td>
                                </tr>
                                <tr>
                                    <td>Lama Berdiri</td>
                                    <td style="text-align: center;">:</td>
                                    <td><?= esc($formData['lama_usaha'] ?? '2 Tahun') ?></td>
                                </tr>
                                <tr>
                                    <td>Lokasi Tempat Usaha</td>
                                    <td style="text-align: center;">:</td>
                                    <td><?= esc($formData['alamat_usaha'] ?? $permohonan['alamat_penduduk'] ?? ($lblDesa . ' ' . $namaDesa)) ?></td>
                                </tr>
                            </table>
                        </div>

                        <p>Surat keterangan ini dibuat sebagai bukti bahwa yang bersangkutan benar menjalankan usaha aktif di wilayah <?= $lblDesa ?> <?= esc($namaDesa) ?>.</p>

                    <?php elseif ($kodeSurat === 'SKCK'): ?>
                        <p>Menerangkan dengan sebenarnya bahwa orang tersebut di atas adalah benar warga <?= $lblDesa ?> <?= esc($namaDesa) ?> yang berkelakuan baik, tidak sedang tersangkut perkara pidana atau perdata, dan tidak pernah melanggar norma hukum yang berlaku.</p>

                        <p>Surat pengantar ini diberikan sebagai kelengkapan berkas penerbitan <strong>Surat Keterangan Catatan Kepolisian (SKCK)</strong> di Kepolisian untuk keperluan: <strong><?= esc($formData['keperluan'] ?? 'Melamar Pekerjaan / Administrasi') ?></strong>.</p>

                    <?php else: ?>
                        <!-- Default Form Details -->
                        <p>Menerangkan dengan sebenarnya bahwa permohonan <strong><?= esc(format_teks_wilayah($permohonan['nama_surat'], $villageId)) ?></strong> atas nama yang bersangkutan telah diverifikasi dan disetujui sesuai dengan data dan ketentuan yang berlaku di <?= $lblDesa ?> <?= esc($namaDesa) ?>.</p>
                        
                        <?php if (!empty($formData)): ?>
                            <div class="box-detail-khusus">
                                <table style="width: 100%; font-size: 11pt;">
                                    <?php foreach ($formData as $k => $v): ?>
                                        <tr>
                                            <td style="width: 180px; text-transform: capitalize;"><?= esc(format_teks_wilayah(str_replace('_', ' ', $k), $villageId)) ?></td>
                                            <td style="width: 15px; text-align: center;">:</td>
                                            <td><strong><?= esc(is_array($v) ? json_encode($v) : $v) ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- PENUTUP -->
                    <p style="margin-top: 15px;">Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya dan kepada pihak yang berkepentingan untuk maklum.</p>
                <?php endif; ?>

            </div>

            <!-- TANDA TANGAN KEPALA DESA / KEUCHIK (TANGGAL DAPAT DIEDIT OLEH OPERATOR DESA) -->
            <div class="ttd-section">
                <div class="ttd-box">
                    <div><?= esc($namaDesa) ?>, <span id="displayTanggalSurat" class="editable-field fw-medium" contenteditable="true" data-field="tanggal_surat" title="Klik untuk mengedit tanggal surat"><?= esc($tanggalSurat) ?></span></div>
                    <div class="fw-bold mb-1"><?= $lblKades ?> <?= esc($namaDesa) ?></div>
                    
                    <!-- Ruang Kosong untuk Tanda Tangan & Cap Stempel Manual -->
                    <div class="ttd-space"></div>

                    <div class="ttd-nama"><?= esc($namaKades) ?></div>
                    <?php if (!empty($nipKades)): ?>
                        <div style="font-size: 9.5pt;">NIP. <?= esc($nipKades) ?></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL EDIT DETAIL SURAT KERAMAIAN (UNTUK OPERATOR DESA) -->
    <div class="modal fade no-print" id="modalEditSurat" tabindex="-1" aria-labelledby="modalEditSuratLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditSuratLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Detail Acara & Tanggal Surat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditDetailSurat">
                        <div class="mb-3">
                            <label for="modal_no_surat" class="form-label fw-bold small text-muted">Nomor Surat Keluar</label>
                            <input type="text" class="form-control font-monospace fw-bold" id="modal_no_surat" value="<?= esc($permohonan['no_surat_keluar']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="modal_tanggal_surat" class="form-label fw-bold small text-muted">Tanggal Surat (Bagian Tanda Tangan)</label>
                            <input type="text" class="form-control" id="modal_tanggal_surat" value="<?= esc($tanggalSurat) ?>" placeholder="Contoh: 22 Agustus 2026">
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-event me-1"></i>Data Acara Keramaian</h6>

                        <div class="mb-3">
                            <label for="modal_dalam_rangka" class="form-label fw-bold small text-muted">Dalam Rangka Kegiatan</label>
                            <input type="text" class="form-control" id="modal_dalam_rangka" value="<?= esc($dalamRangka) ?>" placeholder="Contoh: Resepsi Pernikahan / Khitanan">
                        </div>

                        <div class="mb-3">
                            <label for="modal_hari_tanggal" class="form-label fw-bold small text-muted">Hari / Tanggal Acara</label>
                            <input type="text" class="form-control" id="modal_hari_tanggal" value="<?= esc($hariTanggal) ?>" placeholder="Contoh: Minggu, 25 Agustus 2026">
                        </div>

                        <div class="mb-3">
                            <label for="modal_pukul" class="form-label fw-bold small text-muted">Pukul / Waktu</label>
                            <input type="text" class="form-control" id="modal_pukul" value="<?= esc($pukul) ?>" placeholder="Contoh: 08.00 WIB s.d. selesai">
                        </div>

                        <div class="mb-3">
                            <label for="modal_tempat" class="form-label fw-bold small text-muted">Tempat / Lokasi</label>
                            <textarea class="form-control" id="modal_tempat" rows="2" placeholder="Alamat lengkap atau lokasi"><?= esc($tempatLokasi) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="modal_acara" class="form-label fw-bold small text-muted">Acara / Hiburan</label>
                            <input type="text" class="form-control" id="modal_acara" value="<?= esc($acaraHiburan) ?>" placeholder="Contoh: Orgen Tunggal / Wayang Kulit">
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSimpanModal">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT DETAIL SURAT DOMISILI (UNTUK OPERATOR DESA) -->
    <div class="modal fade no-print" id="modalEditDomisili" tabindex="-1" aria-labelledby="modalEditDomisiliLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditDomisiliLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Detail Surat Domisili
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditDetailDomisili">
                        <div class="mb-3">
                            <label for="modal_domisili_no_surat" class="form-label fw-bold small text-muted">Nomor Surat Keluar</label>
                            <input type="text" class="form-control font-monospace fw-bold" id="modal_domisili_no_surat" value="<?= esc($permohonan['no_surat_keluar']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="modal_domisili_tanggal_surat" class="form-label fw-bold small text-muted">Tanggal Surat (Bagian Tanda Tangan)</label>
                            <input type="text" class="form-control" id="modal_domisili_tanggal_surat" value="<?= esc($tanggalSurat) ?>" placeholder="Contoh: 22 Agustus 2026">
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt me-1"></i>Data Keterangan Domisili</h6>

                        <div class="mb-3">
                            <label for="modal_domisili_alamat_asal" class="form-label fw-bold small text-muted">Alamat Asal (KTP)</label>
                            <textarea class="form-control" id="modal_domisili_alamat_asal" rows="2" placeholder="Alamat sesuai KTP atau tulis Sesuai Domisili"><?= esc($alamatAsal) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="modal_domisili_keperluan" class="form-label fw-bold small text-muted">Keperluan / Syarat Pengurusan</label>
                            <textarea class="form-control" id="modal_domisili_keperluan" rows="2" placeholder="pengurusan administrasi atau keperluan penting lainnya yang memerlukan bukti domisili yang sah"><?= esc($keperluanDomisili) ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSimpanModalDomisili">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastSave" class="card shadow-lg border-0 bg-success text-white px-3 py-2 no-print">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span id="toastSaveMsg">Perubahan berhasil disimpan!</span>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputNomorSurat = document.getElementById('inputNomorSurat');
        const displayNomorSurat = document.getElementById('displayNomorSurat');
        const btnSimpanCepat = document.getElementById('btnSimpanCepat');
        const btnFocusEdit = document.getElementById('btnFocusEdit');
        const toastSave = document.getElementById('toastSave');
        const toastSaveMsg = document.getElementById('toastSaveMsg');

        // Field elements on sheet
        const displayDalamRangka = document.getElementById('displayDalamRangka');
        const displayHariTanggal = document.getElementById('displayHariTanggal');
        const displayPukul = document.getElementById('displayPukul');
        const displayTempatLokasi = document.getElementById('displayTempatLokasi');
        const displayAcaraHiburan = document.getElementById('displayAcaraHiburan');
        const displayAlamatAsal = document.getElementById('displayAlamatAsal');
        const displayKeperluanDomisili = document.getElementById('displayKeperluanDomisili');
        const displayTanggalSurat = document.getElementById('displayTanggalSurat');

        // Modal Keramaian elements
        const modalNoSurat = document.getElementById('modal_no_surat');
        const modalTanggalSurat = document.getElementById('modal_tanggal_surat');
        const modalDalamRangka = document.getElementById('modal_dalam_rangka');
        const modalHariTanggal = document.getElementById('modal_hari_tanggal');
        const modalPukul = document.getElementById('modal_pukul');
        const modalTempat = document.getElementById('modal_tempat');
        const modalAcara = document.getElementById('modal_acara');
        const btnSimpanModal = document.getElementById('btnSimpanModal');

        // Modal Domisili elements
        const modalDomisiliNoSurat = document.getElementById('modal_domisili_no_surat');
        const modalDomisiliTanggalSurat = document.getElementById('modal_domisili_tanggal_surat');
        const modalDomisiliAlamatAsal = document.getElementById('modal_domisili_alamat_asal');
        const modalDomisiliKeperluan = document.getElementById('modal_domisili_keperluan');
        const btnSimpanModalDomisili = document.getElementById('btnSimpanModalDomisili');

        function showToast(msg, isSuccess = true) {
            toastSaveMsg.innerText = msg;
            toastSave.className = 'card shadow-lg border-0 px-3 py-2 ' + (isSuccess ? 'bg-success text-white' : 'bg-danger text-white');
            toastSave.style.display = 'block';
            setTimeout(() => {
                toastSave.style.display = 'none';
            }, 3000);
        }

        function collectData() {
            return {
                no_surat_keluar: inputNomorSurat ? inputNomorSurat.value.trim() : (displayNomorSurat ? displayNomorSurat.innerText.trim() : ''),
                tanggal_surat: displayTanggalSurat ? displayTanggalSurat.innerText.trim() : '',
                dalam_rangka: displayDalamRangka ? displayDalamRangka.innerText.trim() : '',
                hari_tanggal: displayHariTanggal ? displayHariTanggal.innerText.trim() : '',
                pukul: displayPukul ? displayPukul.innerText.trim() : '',
                tempat: displayTempatLokasi ? displayTempatLokasi.innerText.trim() : '',
                acara: displayAcaraHiburan ? displayAcaraHiburan.innerText.trim() : '',
                alamat_asal: displayAlamatAsal ? displayAlamatAsal.innerText.trim() : '',
                keperluan: displayKeperluanDomisili ? displayKeperluanDomisili.innerText.trim() : ''
            };
        }

        function saveAllData(data, btnTrigger = null) {
            if (btnTrigger) {
                btnTrigger.disabled = true;
                btnTrigger.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
            }

            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }
            }

            fetch('<?= $updateSuratUrl ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(resData => {
                if (btnTrigger) {
                    btnTrigger.disabled = false;
                    btnTrigger.innerHTML = '<i class="bi bi-floppy me-1"></i> Simpan';
                }
                if (resData.success) {
                    showToast(resData.message || 'Data surat berhasil diperbarui!', true);
                    
                    // Sync values to all displays
                    if (displayNomorSurat && data.no_surat_keluar !== undefined) displayNomorSurat.innerText = data.no_surat_keluar;
                    if (inputNomorSurat && data.no_surat_keluar !== undefined) inputNomorSurat.value = data.no_surat_keluar;
                    if (modalNoSurat && data.no_surat_keluar !== undefined) modalNoSurat.value = data.no_surat_keluar;
                    if (modalDomisiliNoSurat && data.no_surat_keluar !== undefined) modalDomisiliNoSurat.value = data.no_surat_keluar;

                    if (displayTanggalSurat && data.tanggal_surat !== undefined) displayTanggalSurat.innerText = data.tanggal_surat;
                    if (modalTanggalSurat && data.tanggal_surat !== undefined) modalTanggalSurat.value = data.tanggal_surat;
                    if (modalDomisiliTanggalSurat && data.tanggal_surat !== undefined) modalDomisiliTanggalSurat.value = data.tanggal_surat;

                    if (displayDalamRangka && data.dalam_rangka !== undefined) displayDalamRangka.innerText = data.dalam_rangka;
                    if (modalDalamRangka && data.dalam_rangka !== undefined) modalDalamRangka.value = data.dalam_rangka;

                    if (displayHariTanggal && data.hari_tanggal !== undefined) displayHariTanggal.innerText = data.hari_tanggal;
                    if (modalHariTanggal && data.hari_tanggal !== undefined) modalHariTanggal.value = data.hari_tanggal;

                    if (displayPukul && data.pukul !== undefined) displayPukul.innerText = data.pukul;
                    if (modalPukul && data.pukul !== undefined) modalPukul.value = data.pukul;

                    if (displayTempatLokasi && data.tempat !== undefined) displayTempatLokasi.innerText = data.tempat;
                    if (modalTempat && data.tempat !== undefined) modalTempat.value = data.tempat;

                    if (displayAcaraHiburan && data.acara !== undefined) displayAcaraHiburan.innerText = data.acara;
                    if (modalAcara && data.acara !== undefined) modalAcara.value = data.acara;

                    if (displayAlamatAsal && data.alamat_asal !== undefined) displayAlamatAsal.innerText = data.alamat_asal;
                    if (modalDomisiliAlamatAsal && data.alamat_asal !== undefined) modalDomisiliAlamatAsal.value = data.alamat_asal;

                    if (displayKeperluanDomisili && data.keperluan !== undefined) displayKeperluanDomisili.innerText = data.keperluan;
                    if (modalDomisiliKeperluan && data.keperluan !== undefined) modalDomisiliKeperluan.value = data.keperluan;

                    // Close modals if open
                    const modalElem = document.getElementById('modalEditSurat');
                    if (modalElem) {
                        const modalInstance = bootstrap.Modal.getInstance(modalElem);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                    const modalDomisiliElem = document.getElementById('modalEditDomisili');
                    if (modalDomisiliElem) {
                        const modalDomisiliInstance = bootstrap.Modal.getInstance(modalDomisiliElem);
                        if (modalDomisiliInstance) {
                            modalDomisiliInstance.hide();
                        }
                    }
                } else {
                    showToast(resData.message || 'Gagal menyimpan perubahan.', false);
                }
            })
            .catch(err => {
                console.error(err);
                if (btnTrigger) {
                    btnTrigger.disabled = false;
                    btnTrigger.innerHTML = '<i class="bi bi-floppy me-1"></i> Simpan';
                }
                showToast('Terjadi kesalahan saat menyimpan data.', false);
            });
        }

        // Inline editable fields events
        document.querySelectorAll('.editable-field').forEach(elem => {
            elem.addEventListener('blur', function() {
                // If it's nomor surat, sync with input
                if (this.id === 'displayNomorSurat' && inputNomorSurat) {
                    inputNomorSurat.value = this.innerText.trim();
                }
                saveAllData(collectData());
            });

            elem.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
            });
        });

        // Sync nomor surat input
        if (inputNomorSurat) {
            inputNomorSurat.addEventListener('input', function() {
                if (displayNomorSurat) {
                    displayNomorSurat.innerText = this.value;
                }
            });
            inputNomorSurat.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveAllData(collectData(), btnSimpanCepat);
                }
            });
        }

        if (btnFocusEdit) {
            btnFocusEdit.addEventListener('click', function() {
                if (inputNomorSurat) {
                    inputNomorSurat.focus();
                    inputNomorSurat.select();
                }
            });
        }

        if (btnSimpanCepat) {
            btnSimpanCepat.addEventListener('click', function() {
                saveAllData(collectData(), btnSimpanCepat);
            });
        }

        // Simpan dari Modal Keramaian
        if (btnSimpanModal) {
            btnSimpanModal.addEventListener('click', function() {
                const modalData = {
                    no_surat_keluar: modalNoSurat ? modalNoSurat.value.trim() : '',
                    tanggal_surat: modalTanggalSurat ? modalTanggalSurat.value.trim() : '',
                    dalam_rangka: modalDalamRangka ? modalDalamRangka.value.trim() : '',
                    hari_tanggal: modalHariTanggal ? modalHariTanggal.value.trim() : '',
                    pukul: modalPukul ? modalPukul.value.trim() : '',
                    tempat: modalTempat ? modalTempat.value.trim() : '',
                    acara: modalAcara ? modalAcara.value.trim() : ''
                };
                saveAllData(modalData, btnSimpanModal);
            });
        }

        // Simpan dari Modal Domisili
        if (btnSimpanModalDomisili) {
            btnSimpanModalDomisili.addEventListener('click', function() {
                const modalData = {
                    no_surat_keluar: modalDomisiliNoSurat ? modalDomisiliNoSurat.value.trim() : '',
                    tanggal_surat: modalDomisiliTanggalSurat ? modalDomisiliTanggalSurat.value.trim() : '',
                    alamat_asal: modalDomisiliAlamatAsal ? modalDomisiliAlamatAsal.value.trim() : '',
                    keperluan: modalDomisiliKeperluan ? modalDomisiliKeperluan.value.trim() : ''
                };
                saveAllData(modalData, btnSimpanModalDomisili);
            });
        }
    });
    </script>
</body>
</html>
