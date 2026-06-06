/**
 * admin/mobil.js
 * Handles: foto preview, harga preview, status option toggle, confirm delete
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Foto Upload Preview ──────────────────────────────
    const fotoInput   = document.getElementById('foto-input');
    const uploadArea  = document.getElementById('upload-area');
    const fotoPreview = document.getElementById('foto-preview');
    const uploadText  = document.getElementById('upload-text');
    const uploadHint  = document.getElementById('upload-hint');

    if (uploadArea && fotoInput) {
        uploadArea.addEventListener('click', () => fotoInput.click());

        fotoInput.addEventListener('change', () => {
            const file = fotoInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                fotoPreview.src = e.target.result;
                fotoPreview.classList.remove('is-hidden');
                uploadText.textContent = file.name;
                uploadHint.textContent = (file.size / 1024).toFixed(0) + ' KB';
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Harga Preview ────────────────────────────────────
    const inputHarga   = document.getElementById('input-harga');
    const previewHarga = document.getElementById('preview-harga');

    function updateHargaPreview(val) {
        if (!previewHarga) return;
        previewHarga.textContent = val && !isNaN(val)
            ? '→ Rp ' + new Intl.NumberFormat('id-ID').format(val) + ' per hari'
            : '';
    }

    if (inputHarga) {
        inputHarga.addEventListener('input', () => updateHargaPreview(inputHarga.value));
        updateHargaPreview(inputHarga.value); // inisialisasi saat edit mode
    }

    // ── Status Option Toggle ─────────────────────────────
    document.querySelectorAll('.status-option').forEach(label => {
        label.addEventListener('click', () => {
            const status = label.dataset.status;
            const radio  = label.querySelector('input[type="radio"]');

            document.querySelectorAll('.status-option').forEach(l => {
                l.classList.remove('status-option--active-success', 'status-option--active-danger');
                l.querySelector('input[type="radio"]').checked = false;
            });

            radio.checked = true;
            label.classList.add(
                status === 'tersedia'
                    ? 'status-option--active-success'
                    : 'status-option--active-danger'
            );
        });
    });

    // ── Confirm Delete ───────────────────────────────────
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const msg = form.dataset.confirm || 'Yakin ingin menghapus?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

});