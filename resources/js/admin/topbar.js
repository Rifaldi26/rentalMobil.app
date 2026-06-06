window.toggleAvatarDropdown = function () {
    const dropdown = document.getElementById('topbar-dropdown');
    const btn      = document.getElementById('topbar-avatar-btn');
    if (!dropdown || !btn) return;
    const isOpen = dropdown.classList.contains('open');

    dropdown.classList.toggle('open', !isOpen);
    btn.setAttribute('aria-expanded', String(!isOpen));
};

window.confirmLogout = function () {
    document.getElementById('modal-logout')?.classList.add('open');
};

document.addEventListener('DOMContentLoaded', () => {

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', (e) => {
        const wrap = document.getElementById('topbar-avatar-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('topbar-dropdown')?.classList.remove('open');
            document.getElementById('topbar-avatar-btn')?.setAttribute('aria-expanded', 'false');
        }
    });

    // Tutup dropdown saat tekan Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.getElementById('topbar-dropdown')?.classList.remove('open');
            document.getElementById('topbar-avatar-btn')?.setAttribute('aria-expanded', 'false');
        }
    });

});