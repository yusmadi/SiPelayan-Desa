<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pratinjau Surat - SiPelayan Desa') ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* =========================================================
           PROTEKSI COPY-PASTE & SELEKSI TEKS
           ========================================================= */
        *, *::before, *::after {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }

        /* =========================================================
           PROTEKSI CETAK / DOWNLOAD (PRINT MEDIA BLOCKED)
           ========================================================= */
        @media print {
            html, body {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
            }
        }

        :root {
            --primary-color: #0d6efd;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Plus+Jakarta+Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* Top Action Bar */
        .top-action-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Surat Paper Container */
        .surat-wrapper {
            padding: 30px 15px 60px 15px;
            display: flex;
            justify-content: center;
            position: relative;
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
            overflow: hidden;
        }

        /* Watermark Overlay Proteksi Preview */
        .watermark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            pointer-events: none;
            z-index: 10;
            opacity: 0.12;
            transform: rotate(-30deg);
            transform-origin: center;
        }

        .watermark-text {
            font-family: 'Plus+Jakarta+Sans', sans-serif;
            font-size: 38pt;
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 4px;
            text-align: center;
            white-space: nowrap;
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
            margin-bottom: 25px;
        }

        .judul-surat h4 {
            font-size: 13.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .judul-surat .nomor-surat {
            font-size: 11pt;
            margin-top: 3px;
        }

        /* Isi Surat */
        .isi-surat {
            text-align: justify;
        }

        .isi-surat p {
            margin-bottom: 12px;
            text-indent: 30px;
        }

        .isi-surat p.no-indent {
            text-indent: 0;
        }

        /* Tabel Identitas */
        .tabel-identitas {
            width: 100%;
            margin: 12px 0 16px 20px;
            border-collapse: collapse;
        }

        .tabel-identitas td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11.5pt;
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
            width: 250px;
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

        /* Floating Toast Alert */
        #securityToast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #1e293b;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            font-size: 0.88rem;
            z-index: 9999;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

    <!-- Top Action Bar -->
    <div class="top-action-bar d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="/warga/permohonan" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Permohonan
            </a>
            <div>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                    <i class="bi bi-shield-lock-fill me-1"></i> Mode Pratinjau (Lihat Saja)
                </span>
                <span class="text-muted ms-2 small">No. Registrasi: <strong><?= esc($permohonan['no_permohonan']) ?></strong></span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1 text-primary"></i> Fitur unduh dan salin teks dinonaktifkan untuk mode pratinjau.
            </div>
        </div>
    </div>

    <?php
        if (!function_exists('tgl_indo')) {
            function tgl_indo($tanggal) {
                if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
                return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
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

        // Data Pemohon
        $namaWarga = $permohonan['nama_pemohon'] ?? $permohonan['nama_lengkap'] ?? '-';
        $nikWarga = $permohonan['nik_penduduk'] ?? $permohonan['nik_user'] ?? $permohonan['nik'] ?? '-';
        $noKkWarga = $permohonan['no_kk'] ?? '-';
        $tempatLahir = $permohonan['tempat_lahir'] ?? ($lblDesa . ' ' . $namaDesa);
        $tanggalLahir = !empty($permohonan['tanggal_lahir']) ? tgl_indo($permohonan['tanggal_lahir']) : '-';
        $ttl = $tempatLahir . ', ' . $tanggalLahir;
        $jk = (($permohonan['jenis_kelamin'] ?? 'L') === 'L' || ($permohonan['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'Laki-laki' : 'Perempuan';
        $agama = $permohonan['agama'] ?? 'Islam';
        $pekerjaan = $permohonan['pekerjaan'] ?? 'Wiraswasta';
        $statusKawin = $permohonan['status_perkawinan'] ?? 'Kawin';
        $kewarganegaraan = $permohonan['kewarganegaraan'] ?? 'WNI';
        $rtDesa = !empty($permohonan['rt']) ? $permohonan['rt'] : '001';
        $rwDesa = !empty($permohonan['rw']) ? $permohonan['rw'] : '002';
        $dusunDesa = !empty($permohonan['dusun']) ? $permohonan['dusun'] : 'Dusun Krajan';
        
        $alamatWarga = $permohonan['alamat_penduduk'] ?? '';
        if (empty($alamatWarga)) {
            $alamatWarga = "RT {$rtDesa} / RW {$rwDesa}, {$dusunDesa}, {$lblDesa} {$namaDesa}, Kec. {$namaKecamatan}";
        }

        // Parse data keramaian
        $dalamRangka = !empty($formData['dalam_rangka']) ? $formData['dalam_rangka'] : (!empty($formData['nama_acara']) ? $formData['nama_acara'] : (!empty($formData['keperluan']) ? $formData['keperluan'] : (!empty($formData['rangka']) ? $formData['rangka'] : 'Kegiatan Acara Keramaian Warga')));
        $tglAcaraRaw = $formData['tanggal_pelaksanaan'] ?? $formData['tanggal_acara'] ?? $formData['tanggal'] ?? null;
        $hariTanggal = !empty($tglAcaraRaw) ? hari_tgl_indo($tglAcaraRaw) : (!empty($formData['hari_tanggal']) ? $formData['hari_tanggal'] : hari_tgl_indo(date('Y-m-d', strtotime('+3 days'))));
        $pukul = !empty($formData['waktu_pelaksanaan']) ? $formData['waktu_pelaksanaan'] : (!empty($formData['pukul']) ? $formData['pukul'] : (!empty($formData['jam']) ? $formData['jam'] : '08.00 WIB s.d. selesai'));
        $tempatLokasi = !empty($formData['lokasi_kegiatan']) ? $formData['lokasi_kegiatan'] : (!empty($formData['tempat']) ? $formData['tempat'] : (!empty($formData['lokasi']) ? $formData['lokasi'] : (!empty($permohonan['alamat_penduduk']) ? $permohonan['alamat_penduduk'] : ($lblDesa . ' ' . $namaDesa))));
        $acaraHiburan = !empty($formData['jenis_hiburan']) ? $formData['jenis_hiburan'] : (!empty($formData['acara']) ? $formData['acara'] : (!empty($formData['hiburan']) ? $formData['hiburan'] : (!empty($formData['nama_acara']) ? $formData['nama_acara'] : 'Orgen Tunggal / Wayang Kulit / Hiburan Warga')));

        // Parse detail isian surat domisili
        $alamatAsal = !empty($formData['alamat_asal']) ? $formData['alamat_asal'] : (!empty($formData['alamat_ktp']) ? $formData['alamat_ktp'] : (!empty($formData['alamat_sekarang']) ? $formData['alamat_sekarang'] : (!empty($permohonan['alamat_penduduk']) ? $permohonan['alamat_penduduk'] : 'Sesuai Domisili')));
        $keperluanDomisili = !empty($formData['keperluan']) ? $formData['keperluan'] : (!empty($formData['tujuan']) ? $formData['tujuan'] : 'pengurusan administrasi atau keperluan penting lainnya yang memerlukan bukti domisili yang sah');
    ?>

    <!-- Lembar Surat (Pratinjau A4) -->
    <div class="surat-wrapper">
        <div class="surat-sheet">
            
            <!-- Watermark Proteksi Pratinjau -->
            <div class="watermark-overlay">
                <div class="watermark-text">PRATINJAU DOKUMEN</div>
                <div class="watermark-text">BUKAN DOKUMEN RESMI</div>
                <div class="watermark-text">SIPELAYAN <?= $lblDesaUpper ?></div>
            </div>

            <!-- KOP SURAT RESMI DESA / GAMPONG -->
            <div class="kop-surat">
                <div class="kop-logo d-flex align-items-center justify-content-center">
                    <?php if (!empty($logoSrc)): ?>
                        <img src="<?= esc($logoSrc) ?>" alt="Logo <?= $lblDesa ?>" class="kop-logo-img">
                    <?php else: ?>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Coat_of_arms_of_Indonesia.svg/800px-Coat_of_arms_of_Indonesia.svg.png" alt="Logo Garuda / Pemda" class="kop-logo-img">
                    <?php endif; ?>
                </div>
                <div class="kop-teks">
                    <h4>PEMERINTAH KABUPATEN <?= esc(strtoupper($namaKabupaten)) ?></h4>
                    <h4>KECAMATAN <?= esc(strtoupper($namaKecamatan)) ?></h4>
                    <h3><?= $lblDesaUpper ?> <?= esc(strtoupper($namaDesa)) ?></h3>
                    <p><?= esc($alamatKantor) ?><?= !empty($teleponDesa) ? ' | Telp: ' . esc($teleponDesa) : '' ?><?= !empty($emailDesa) ? ' | Email: ' . esc($emailDesa) : '' ?></p>
                </div>
            </div>

            <!-- JUDUL DAN NOMOR SURAT -->
            <div class="judul-surat">
                <h4><?= esc(strtoupper(format_teks_wilayah($permohonan['nama_surat'] ?? ('SURAT KETERANGAN ' . $lblDesaUpper), $villageId))) ?></h4>
                <div class="nomor-surat">Nomor: <?= esc($permohonan['no_surat_keluar']) ?></div>
            </div>

            <!-- ISI SURAT -->
            <div class="isi-surat">
                <?php if ($isKeramaian): ?>
                    <!-- FORMAT RESMI: SURAT IZIN KERAMAIAN -->
                    <p><?= $lblKades ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, Kabupaten <?= esc($namaKabupaten) ?>, dengan ini menerangkan bahwa:</p>

                    <table class="tabel-identitas">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc($permohonan['nama_pemohon'] ?? $namaWarga) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK / No. KTP</td>
                            <td class="colon">:</td>
                            <td><?= esc($nikWarga) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($pekerjaan) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat</td>
                            <td class="colon">:</td>
                            <td>RT <?= esc($rtDesa) ?> / RW <?= esc($rwDesa) ?>, Dusun <?= esc($dusunDesa) ?>, <?= $lblDesa ?> <?= esc($namaDesa) ?></td>
                        </tr>
                    </table>

                    <p style="margin-top: 14px;">Adalah benar warga <?= $lblDesa ?> <?= esc($namaDesa) ?> yang telah mengajukan permohonan izin untuk menyelenggarakan kegiatan keramaian dalam rangka <strong><?= esc($dalamRangka) ?></strong>.</p>

                    <p style="margin-top: 14px; margin-bottom: 4px;">Kegiatan tersebut akan diselenggarakan pada:</p>

                    <table class="tabel-identitas" style="margin-top: 4px; margin-bottom: 14px;">
                        <tr>
                            <td class="label">Hari / Tanggal</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc($hariTanggal) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">Pukul</td>
                            <td class="colon">:</td>
                            <td><?= esc($pukul) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat / Lokasi</td>
                            <td class="colon">:</td>
                            <td><?= esc($tempatLokasi) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Acara / Hiburan</td>
                            <td class="colon">:</td>
                            <td><?= esc($acaraHiburan) ?></td>
                        </tr>
                    </table>

                    <p style="margin-top: 10px; margin-bottom: 6px;">Dengan ketentuan dan kewajiban bagi penyelenggara sebagai berikut:</p>
                    <ol style="margin: 0; padding-left: 20px; line-height: 1.45;">
                        <li style="margin-bottom: 5px;">Wajib menjaga keamanan, ketertiban, dan ketentraman lingkungan selama kegiatan berlangsung.</li>
                        <li style="margin-bottom: 5px;">Dilarang keras menyelenggarakan perjudian, peredaran minuman keras / narkoba, dan tindakan melanggar hukum lainnya.</li>
                        <li style="margin-bottom: 5px;">Wajib berkoordinasi dengan Ketua RT/RW setempat, Babinsa, dan Bhabinkamtibmas.</li>
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
                            <td><strong><?= esc($permohonan['nama_pemohon'] ?? $namaWarga) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK / No. KTP</td>
                            <td class="colon">:</td>
                            <td><?= esc($nikWarga) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat, Tanggal Lahir</td>
                            <td class="colon">:</td>
                            <td><?= esc($ttl) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="colon">:</td>
                            <td><?= esc($jk) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Agama</td>
                            <td class="colon">:</td>
                            <td><?= esc($agama) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($pekerjaan) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status Perkawinan</td>
                            <td class="colon">:</td>
                            <td><?= esc($statusKawin) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat Asal (KTP)</td>
                            <td class="colon">:</td>
                            <td><?= esc($alamatAsal) ?></td>
                        </tr>
                    </table>

                    <p style="margin-top: 14px;">Berdasarkan pengamatan dan data administrasi kependudukan kami serta laporan dari Ketua RT/RW setempat, nama tersebut benar-benar warga penduduk yang bertempat tinggal dan berdomisili di <?= $lblDesa ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, <?= (stripos(trim($namaKabupaten), 'kabupaten') === 0 || stripos(trim($namaKabupaten), 'kota') === 0) ? esc(trim($namaKabupaten)) : 'Kabupaten ' . esc(trim($namaKabupaten)) ?>. Dan berdasarkan keterangan yang ada, yang bersangkutan berkelakuan baik, serta aktif dalam kehidupan sosial bermasyarakat di lingkungan <?= $lblDesa ?> <?= esc($namaDesa) ?>.</p>

                    <p style="margin-top: 12px; margin-bottom: 4px;">Surat Keterangan Domisili ini diberikan kepada yang bersangkutan agar dapat dipergunakan untuk keperluan:</p>

                    <div style="text-align: center; margin: 8px 0 12px 0; font-weight: bold; font-size: 11.5pt;">
                        <span class="fw-bold"><?= esc($keperluanDomisili) ?></span>
                    </div>

                    <p style="margin-top: 12px;">Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya.</p>

                <?php else: ?>
                    <p>Yang bertanda tangan di bawah ini <?= $lblKades ?> <strong><?= esc($namaDesa) ?></strong>, Kecamatan <?= esc($namaKecamatan) ?>, Kabupaten <?= esc($namaKabupaten) ?>, dengan ini menerangkan bahwa:</p>

                    <!-- TABEL DATA IDENTITAS PEMOHON -->
                    <table class="tabel-identitas">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="colon">:</td>
                            <td><strong><?= esc(strtoupper($namaWarga)) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="label">NIK</td>
                            <td class="colon">:</td>
                            <td><?= esc($nikWarga) ?></td>
                        </tr>
                        <?php if (!empty($noKkWarga) && $noKkWarga !== '-'): ?>
                        <tr>
                            <td class="label">No. Kartu Keluarga</td>
                            <td class="colon">:</td>
                            <td><?= esc($noKkWarga) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="label">Tempat, Tanggal Lahir</td>
                            <td class="colon">:</td>
                            <td><?= esc($ttl) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="colon">:</td>
                            <td><?= esc($jk) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Agama</td>
                            <td class="colon">:</td>
                            <td><?= esc($agama) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Pekerjaan</td>
                            <td class="colon">:</td>
                            <td><?= esc($pekerjaan) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status Perkawinan</td>
                            <td class="colon">:</td>
                            <td><?= esc($statusKawin) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Kewarganegaraan</td>
                            <td class="colon">:</td>
                            <td><?= esc($kewarganegaraan) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Alamat / Domisili</td>
                            <td class="colon">:</td>
                            <td><?= esc($alamatWarga) ?></td>
                        </tr>
                    </table>

                <!-- KONTEN SPESIFIK SESUAI KODE / JENIS SURAT -->
                <?php if ($kodeSurat === 'SKTM' || str_contains($kodeSurat, 'TIDAK_MAMPU')): ?>
                    <p>Menerangkan dengan sebenarnya bahwa orang tua/wali dari nama tersebut di atas adalah benar tergolong keluarga <strong>Kurang Mampu / Tidak Mampu (Prasejahtera)</strong> di wilayah <?= $lblDesa ?> <?= esc($namaDesa) ?>.</p>

                    <?php if (!empty($formData['nama_anak'])): ?>
                        <div class="box-detail-khusus">
                            <table style="width: 100%; font-size: 11pt;">
                                <tr>
                                    <td style="width: 180px;">Nama Anak/Siswa</td>
                                    <td style="width: 15px; text-align: center;">:</td>
                                    <td><strong><?= esc($formData['nama_anak']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Sekolah / Universitas</td>
                                    <td style="text-align: center;">:</td>
                                    <td><?= esc($formData['sekolah_tujuan'] ?? $formData['instansi'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>

                    <p>Surat keterangan ini diterbitkan guna keperluan: <strong><?= esc($formData['keperluan'] ?? 'Pengajuan Keringanan Biaya Pendidikan / Beasiswa') ?></strong>.</p>

                <?php elseif ($kodeSurat === 'SKU' || str_contains($kodeSurat, 'USAHA')): ?>
                    <p>Menerangkan dengan sebenarnya bahwa orang tersebut di atas adalah benar penduduk <?= $lblDesa ?> <?= esc($namaDesa) ?> dan saat ini memiliki/menjalankan usaha dengan rincian sebagai berikut:</p>

                    <div class="box-detail-usaha">
                        <table style="width: 100%; font-size: 11pt;">
                            <tr>
                                <td style="width: 180px;">Nama Usaha</td>
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

            <!-- TANDA TANGAN KEPALA DESA / KEUCHIK -->
            <div class="ttd-section">
                <div class="ttd-box">
                    <div><?= esc($namaDesa) ?>, <?= esc(!empty($formData['tanggal_surat']) ? $formData['tanggal_surat'] : tgl_indo(date('Y-m-d'))) ?></div>
                    <div class="fw-bold mb-1"><?= $lblKades ?> <?= esc($namaDesa) ?></div>
                    
                    <!-- Ruang Kosong untuk Tanda Tangan Manual -->
                    <div class="ttd-space"></div>

                    <div class="ttd-nama"><?= esc($namaKades) ?></div>
                    <?php if (!empty($nipKades)): ?>
                        <div style="font-size: 9.5pt;">NIP. <?= esc($nipKades) ?></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating Security Toast -->
    <div id="securityToast">
        <i class="bi bi-shield-exclamation text-warning me-2"></i>
        <span>Pratinjau Terproteksi: Salin teks dan cetak dinonaktifkan.</span>
    </div>

    <script>
        // Handler untuk notifikasi proteksi
        function showSecurityNotice() {
            const toast = document.getElementById('securityToast');
            if (toast) {
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 2500);
            }
        }

        // Cegah Klik Kanan (Context Menu)
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showSecurityNotice();
            return false;
        });

        // Cegah Aksi Copy / Cut / Paste
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            showSecurityNotice();
            return false;
        });
        document.addEventListener('cut', function(e) {
            e.preventDefault();
            showSecurityNotice();
            return false;
        });
        document.addEventListener('paste', function(e) {
            e.preventDefault();
            return false;
        });

        // Cegah Shortcut Keyboard: Ctrl+C, Ctrl+P (Print), Ctrl+S (Save), Ctrl+U (Source), F12 (Inspect)
        document.addEventListener('keydown', function(e) {
            const isCtrl = e.ctrlKey || e.metaKey;
            
            if (
                (isCtrl && (e.key === 'c' || e.key === 'C')) ||
                (isCtrl && (e.key === 'p' || e.key === 'P')) ||
                (isCtrl && (e.key === 's' || e.key === 'S')) ||
                (isCtrl && (e.key === 'u' || e.key === 'U')) ||
                (isCtrl && (e.key === 'x' || e.key === 'X')) ||
                (isCtrl && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) ||
                e.key === 'F12'
            ) {
                e.preventDefault();
                e.stopPropagation();
                showSecurityNotice();
                return false;
            }
        });
    </script>
</body>
</html>
