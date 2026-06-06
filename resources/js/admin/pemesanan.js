/**
 * admin/pemesanan.js
 * Handles: filter booking, modal aksi (konfirmasi/tolak/selesai)
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Filter booking ───────────────────────────────────
    const filterButtons = document.querySelectorAll('.filter-chip[data-filter]');
    const bookingItems  = document.querySelectorAll('#booking-list .booking-item');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;

            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            bookingItems.forEach(item => {
                const tampil = filter === 'semua' || item.dataset.status === filter;
                item.classList.toggle('is-hidden', !tampil);
            });
        });
    });

    // ── Modal aksi ───────────────────────────────────────
    const modal       = document.getElementById('modal-aksi');
    const modalIcon   = document.getElementById('modal-icon');
    const modalTitle  = document.getElementById('modal-title');
    const modalDesc   = document.getElementById('modal-desc');
    const modalSubmit = document.getElementById('modal-submit');
    const modalBatal  = document.getElementById('modal-batal');

    const CONFIG = {
        konfirmasi: {
            icon:  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>`,
            iconClass: 'modal-icon--success',
            title: 'Konfirmasi pemesanan?',
            btnClass: 'modal-btn--success',
            btnTxt: 'Ya, konfirmasi',
        },
        selesai: {
            icon:  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>`,
            iconClass: 'modal-icon--info',
            title: 'Tandai selesai?',
            btnClass: 'modal-btn--info',
            btnTxt: 'Ya, selesai',
        },
        tolak: {
            icon:  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>`,
            iconClass: 'modal-icon--danger',
            title: 'Tolak pemesanan ini?',
            btnClass: 'modal-btn--danger',
            btnTxt: 'Ya, tolak',
        },
    };

    function bukaModal(aksi, id, nama, mobil, tanggal) {
        const cfg = CONFIG[aksi];

        // Reset classes sebelumnya
        modalIcon.className   = 'modal-aksi-sheet__icon ' + cfg.iconClass;
        modalSubmit.className = 'modal-aksi-sheet__btn-submit ' + cfg.btnClass;

        modalIcon.innerHTML  = cfg.icon;
        modalTitle.textContent = cfg.title;
        modalDesc.innerHTML    = `${nama} &mdash; ${mobil}<br>${tanggal}`;
        modalSubmit.textContent = cfg.btnTxt;

        modalSubmit.onclick = () => {
            document.getElementById(`form-${aksi}-${id}`).submit();
        };

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function tutupModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    // Delegate click ke semua tombol aksi
    document.querySelectorAll('[data-aksi]').forEach(btn => {
        btn.addEventListener('click', () => {
            bukaModal(
                btn.dataset.aksi,
                btn.dataset.id,
                btn.dataset.nama,
                btn.dataset.mobil,
                btn.dataset.tanggal
            );
        });
    });

    // Tutup modal
    modalBatal.addEventListener('click', tutupModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) tutupModal();
    });

});