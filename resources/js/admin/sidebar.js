/**
 * Admin Sidebar — desktop collapse + mobile drawer.
 */

(function () {
    const STORAGE_KEY = 'admin_sidebar_collapsed';
    const sidebar     = document.getElementById('admin-sidebar');
    const iconOpen    = document.getElementById('admin-icon-open');
    const iconClose   = document.getElementById('admin-icon-close');
    const backdrop    = document.getElementById('drawer-backdrop');
    const hamburger   = document.getElementById('btn-hamburger');

    if (!sidebar) return;

    // ── Desktop: collapsed state ──────────────────────────────
    function applyCollapsed(collapsed, animate) {
        if (!animate) sidebar.style.transition = 'none';

        sidebar.classList.toggle('collapsed', collapsed);

        if (iconOpen)  iconOpen.classList.toggle('hidden', !collapsed);
        if (iconClose) iconClose.classList.toggle('hidden', collapsed);

        if (!animate) {
            requestAnimationFrame(() => requestAnimationFrame(() => {
                sidebar.style.transition = '';
            }));
        }
    }

    window.toggleAdminSidebar = function () {
        const next = !sidebar.classList.contains('collapsed');
        localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
        applyCollapsed(next, true);
    };

    // Restore state saat halaman dimuat (desktop only)
    if (window.innerWidth >= 1024) {
        applyCollapsed(localStorage.getItem(STORAGE_KEY) === '1', false);
    }

    // ── Mobile: drawer ────────────────────────────────────────
    function openDrawer() {
        sidebar.classList.add('drawer-open');
        backdrop?.classList.add('open');
        document.body.style.overflow = 'hidden'; // cegah scroll background
    }

    function closeDrawer() {
        sidebar.classList.remove('drawer-open');
        backdrop?.classList.remove('open');
        document.body.style.overflow = '';
    }

    hamburger?.addEventListener('click', openDrawer);
    backdrop?.addEventListener('click', closeDrawer);

    // Tutup drawer saat nav link diklik (langsung navigasi)
    sidebar.querySelectorAll('.admin-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) closeDrawer();
        });
    });

    // Tutup drawer saat resize ke desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) closeDrawer();
    });

})();