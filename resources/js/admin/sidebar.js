/**
 * Admin Sidebar — toggle collapsed state dengan localStorage persistence.
 * Dipanggil dari desktop-sidebar-admin.blade.php via @vite
 */

(function () {
    const STORAGE_KEY = 'admin_sidebar_collapsed';
    const sidebar     = document.getElementById('admin-sidebar');
    const iconOpen    = document.getElementById('admin-icon-open');
    const iconClose   = document.getElementById('admin-icon-close');

    if (!sidebar) return;

    function applyState(collapsed, animate) {
        if (!animate) sidebar.style.transition = 'none';

        sidebar.classList.toggle('collapsed', collapsed);

        if (iconOpen)  iconOpen.style.display  = collapsed ? 'block' : 'none';
        if (iconClose) iconClose.style.display = collapsed ? 'none'  : 'block';

        if (!animate) {
            requestAnimationFrame(() => requestAnimationFrame(() => {
                sidebar.style.transition = '';
            }));
        }
    }

    window.toggleAdminSidebar = function () {
        const next = !sidebar.classList.contains('collapsed');
        localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
        applyState(next, true);
    };

    // Restore state saat halaman dimuat
    applyState(localStorage.getItem(STORAGE_KEY) === '1', false);
})();