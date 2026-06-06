let activeTab = 'semua';

function filterTab(tab, el) {
    activeTab = tab;
    document.querySelectorAll('.tab-item').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    applyUserFilters();
}

function applyUserFilters() {
    const keyword = document.getElementById('search-input').value.toLowerCase().trim();

    let visible = 0;
    document.querySelectorAll('.user-card').forEach(card => {
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