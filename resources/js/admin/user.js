// ─── State ────────────────────────────────────────────────
let activeTab = 'semua';

// ─── Filter by tab ────────────────────────────────────────
function setTab(tab) {
    activeTab = tab;
    applyFilters();
}

// ─── Apply all active filters ─────────────────────────────
function applyFilters() {
    const keyword = document.getElementById('search-input')?.value.toLowerCase().trim() ?? '';
    const cards   = document.querySelectorAll('.user-card');
    let   visible = 0;

    cards.forEach(card => {
        const matchTab = activeTab === 'semua'
            || (activeTab === 'aktif' && card.dataset.aktif === 'ya')
            || (activeTab === 'baru'  && card.dataset.baru  === 'ya');

        const matchSearch = !keyword
            || card.dataset.nama.includes(keyword)
            || card.dataset.email.includes(keyword);

        const show = matchTab && matchSearch;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const emptyEl = document.getElementById('empty-search');
    if (emptyEl) emptyEl.style.display = visible === 0 ? 'block' : 'none';
}

// ─── DOM Ready ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Tab buttons — pakai data-tab, tanpa onclick di blade
    document.querySelectorAll('.cat-chip[data-tab]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.cat-chip[data-tab]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            setTab(btn.dataset.tab);
        });
    });

    // Search input
    document.getElementById('search-input')?.addEventListener('input', applyFilters);
});