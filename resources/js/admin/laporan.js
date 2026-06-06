/**
 * Admin Laporan — Chart pendapatan & distribusi status.
 * Requires Chart.js (di-load via @push('styles') di blade).
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Chart Pendapatan per Bulan ──────────────────────────
    const ctxPendapatan = document.getElementById('chartPendapatan');
    if (ctxPendapatan) {
        new Chart(ctxPendapatan.getContext('2d'), {
            type: 'bar',
            data: {
                labels:   window.laporanChartLabels ?? [],
                datasets: [{
                    label:           'Pendapatan (Rp)',
                    data:            window.laporanChartData ?? [],
                    backgroundColor: 'rgba(37,99,235,0.15)',
                    borderColor:     '#2563eb',
                    borderWidth:     2,
                    borderRadius:    6,
                    borderSkipped:   false,
                }],
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v >= 1_000_000
                                ? 'Rp ' + (v / 1_000_000).toFixed(1) + 'jt'
                                : 'Rp ' + v.toLocaleString('id-ID'),
                            font: { size: 10 },
                        },
                        grid: { color: 'rgba(0,0,0,.05)' },
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                },
            },
        });
    }

    // ── Chart Status Pemesanan ──────────────────────────────
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus && window.laporanStatusData) {
        const labelMap = {
            selesai: 'Selesai', pending: 'Pending',
            dikonfirmasi: 'Berjalan', dibatalkan: 'Dibatalkan',
        };
        new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.laporanStatusData).map(k => labelMap[k] ?? k),
                datasets: [{
                    data:            Object.values(window.laporanStatusData),
                    backgroundColor: ['#16a34a', '#d97706', '#2563eb', '#dc2626'],
                    borderWidth:     2,
                    borderColor:     '#fff',
                }],
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 11 }, padding: 12, boxWidth: 12 },
                    },
                },
            },
        });
    }
});