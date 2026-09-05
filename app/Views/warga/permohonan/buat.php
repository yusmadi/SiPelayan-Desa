<?= $this->extend('layouts/warga') ?>

<?= $this->section('styles') ?>
<!-- Tom Select CSS (Bootstrap 5 theme) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select-lg .ts-control,
    .ts-control {
        border-radius: 0.5rem !important;
        padding: 0.6rem 1rem !important;
        border-color: #dee2e6 !important;
        font-size: 1rem;
        min-height: 46px;
        box-shadow: none;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    .ts-dropdown {
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: #dee2e6 !important;
        font-size: 0.95rem;
    }
    .ts-dropdown .active {
        background-color: #0d6efd !important;
        color: #fff !important;
    }
    .ts-dropdown .option {
        padding: 0.6rem 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold mb-1">Ajukan Surat Baru</h2>
        <p class="text-muted mb-0">Pilih jenis surat dan lengkapi data permohonan dengan benar</p>
    </div>
    <a href="/warga/permohonan" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="/warga/permohonan/buat" method="post" enctype="multipart/form-data" id="form-permohonan">
            <?= csrf_field() ?>
            
            <div class="row mb-4">
                <div class="col-md-7 col-lg-6">
                    <label for="jenis_surat_id" class="form-label fw-bold">Pilih Jenis Surat <span class="text-danger">*</span></label>
                    <select name="jenis_surat_id" id="jenis_surat_id" class="form-select form-select-lg" required>
                        <option value="">-- Cari atau Pilih Jenis Surat --</option>
                        <?php foreach ($jenisSurat as $js): ?>
                            <option value="<?= $js['id'] ?>" <?= (old('jenis_surat_id') == $js['id']) ? 'selected' : '' ?>><?= esc(format_teks_wilayah($js['nama'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted">Ketik nama surat untuk mencari dengan cepat.</div>
                </div>
            </div>

            <!-- Dynamic Form Area -->
            <div id="dynamic-form-area" class="border rounded p-4 bg-light mb-4" style="display: none;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-primary fw-bold" id="form-surat-title">Form Isian Permohonan</h5>
                        <p class="text-muted small mb-0" id="form-surat-desc"></p>
                    </div>
                </div>

                <!-- Persyaratan Dokumen Alert -->
                <div id="syarat-dokumen-box" class="alert alert-info py-2 px-3 mb-3 d-none">
                    <strong><i class="bi bi-info-circle me-1"></i> Dokumen Persyaratan yang Diperlukan:</strong>
                    <ul id="syarat-dokumen-list" class="mb-0 mt-1 small"></ul>
                </div>

                <!-- Container untuk input dinamis berdasarkan schema_form -->
                <div id="dynamic-fields-container"></div>
                
                <div class="mb-3 mt-3">
                    <label for="dokumen_syarat" class="form-label fw-semibold">Upload Berkas / Dokumen Lampiran (Opsional)</label>
                    <input type="file" name="dokumen_syarat" id="dokumen_syarat" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">Format didukung: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2">
                <a href="/warga/permohonan" class="btn btn-outline-secondary btn-lg px-4 d-inline-flex align-items-center justify-content-center">Batal</a>
                <button type="submit" id="btn-submit" class="btn btn-primary btn-lg px-5 shadow-sm d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-send me-2"></i>Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    const jenisSuratData = <?= json_encode(array_column($jenisSurat, null, 'id')) ?>;
    const selectJenisSurat = document.getElementById('jenis_surat_id');
    const dynamicFormArea = document.getElementById('dynamic-form-area');
    const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');
    const formSuratTitle = document.getElementById('form-surat-title');
    const formSuratDesc = document.getElementById('form-surat-desc');
    const syaratDokumenBox = document.getElementById('syarat-dokumen-box');
    const syaratDokumenList = document.getElementById('syarat-dokumen-list');

    // Inisialisasi TomSelect untuk Fitur Pencarian pada Dropdown Jenis Surat
    const tsJenisSurat = new TomSelect('#jenis_surat_id', {
        create: false,
        placeholder: "-- Cari atau Pilih Jenis Surat --",
        allowEmptyOption: true,
        maxOptions: 500,
        sortField: {
            field: "text",
            direction: "asc"
        },
        onChange: function(value) {
            renderDynamicForm(value);
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderDynamicForm(selectedId) {
        if (selectedId === undefined || selectedId === null) {
            selectedId = selectJenisSurat.value;
        }
        const surat = jenisSuratData[selectedId];

        if (!surat) {
            dynamicFormArea.style.display = 'none';
            dynamicFieldsContainer.innerHTML = '';
            return;
        }

        dynamicFormArea.style.display = 'block';
        formSuratTitle.textContent = 'Form Isian: ' + surat.nama;
        formSuratDesc.textContent = surat.deskripsi || '';

        // Render Syarat Dokumen
        let syarat = [];
        try {
            if (typeof surat.syarat_dokumen === 'string' && surat.syarat_dokumen.trim() !== '') {
                syarat = JSON.parse(surat.syarat_dokumen);
            } else if (Array.isArray(surat.syarat_dokumen)) {
                syarat = surat.syarat_dokumen;
            }
        } catch(e) {
            syarat = [];
        }

        if (Array.isArray(syarat) && syarat.length > 0) {
            syaratDokumenList.innerHTML = syarat.map(item => `<li>${escapeHtml(item)}</li>`).join('');
            syaratDokumenBox.classList.remove('d-none');
        } else {
            syaratDokumenBox.classList.add('d-none');
            syaratDokumenList.innerHTML = '';
        }

        // Render Schema Form
        let schema = [];
        try {
            if (typeof surat.schema_form === 'string' && surat.schema_form.trim() !== '') {
                schema = JSON.parse(surat.schema_form);
            } else if (Array.isArray(surat.schema_form)) {
                schema = surat.schema_form;
            }
        } catch(e) {
            schema = [];
        }

        dynamicFieldsContainer.innerHTML = '';

        if (Array.isArray(schema) && schema.length > 0) {
            schema.forEach(field => {
                const fieldWrapper = document.createElement('div');
                fieldWrapper.className = 'mb-3';

                const fieldId = 'data_form_' + field.name;
                const fieldName = 'data_form_' + field.name;
                const isRequired = field.required ? 'required' : '';
                const reqStar = field.required ? '<span class="text-danger">*</span>' : '';
                const placeholder = field.placeholder ? escapeHtml(field.placeholder) : '';
                const labelText = escapeHtml(field.label || field.name);

                let inputHtml = '';
                if (field.type === 'textarea') {
                    inputHtml = `<textarea name="${fieldName}" id="${fieldId}" rows="3" class="form-control" ${isRequired} placeholder="${placeholder}"></textarea>`;
                } else if (field.type === 'select' && Array.isArray(field.options)) {
                    const optionsHtml = field.options.map(opt => `<option value="${escapeHtml(opt)}">${escapeHtml(opt)}</option>`).join('');
                    inputHtml = `<select name="${fieldName}" id="${fieldId}" class="form-select" ${isRequired}><option value="">-- Pilih --</option>${optionsHtml}</select>`;
                } else {
                    const inputType = field.type || 'text';
                    inputHtml = `<input type="${inputType}" name="${fieldName}" id="${fieldId}" class="form-control" ${isRequired} placeholder="${placeholder}">`;
                }

                fieldWrapper.innerHTML = `
                    <label for="${fieldId}" class="form-label fw-semibold">${labelText} ${reqStar}</label>
                    ${inputHtml}
                `;
                dynamicFieldsContainer.appendChild(fieldWrapper);
            });
        } else {
            // Default generic keperluan field if schema_form is empty
            const defaultWrapper = document.createElement('div');
            defaultWrapper.className = 'mb-3';
            defaultWrapper.innerHTML = `
                <label for="data_form_keperluan" class="form-label fw-semibold">Keperluan Pengajuan <span class="text-danger">*</span></label>
                <textarea name="data_form_keperluan" id="data_form_keperluan" rows="3" class="form-control" required placeholder="Contoh: Pembuatan rekening bank, beasiswa, pendaftaran sekolah, dll."></textarea>
            `;
            dynamicFieldsContainer.appendChild(defaultWrapper);
        }
    }

    // Trigger on page load if already selected (e.g. on validation reload / old input)
    if (selectJenisSurat.value) {
        renderDynamicForm(selectJenisSurat.value);
    }
</script>
<?= $this->endSection() ?>
