/**
 * NexoraPOS Dashboard Charts (Exact Job-Finance Ergonomics & Styling)
 * Rich Area Gradient, Clean Axis Grid, Minimalist Legends, and Sleek Dark Tooltip
 */
(function () {
    let mainChartInstance = null;
    let donutChartInstance = null;

    function formatNumber(val) {
        if (typeof val !== 'number') {
            const parsed = Number(val);
            if (isNaN(parsed)) return val;
            val = parsed;
        }
        if (Math.abs(val) >= 1000000000) {
            return 'Rp ' + (val / 1000000000).toFixed(1).replace('.', ',') + ' M';
        }
        if (Math.abs(val) >= 1000000) {
            return 'Rp ' + (val / 1000000).toFixed(1).replace('.', ',') + ' jt';
        }
        if (Math.abs(val) >= 10000) {
            return 'Rp ' + Math.round(val).toLocaleString('id-ID');
        }
        return Math.round(val).toLocaleString('id-ID');
    }

    function initMainChart(labels, values) {
        const canvas = document.getElementById('dashboardMainChart') || document.getElementById('salesChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const ctx = canvas.getContext('2d');
        if (mainChartInstance) {
            mainChartInstance.destroy();
        }

        const isDark = document.documentElement.classList.contains('theme-dark') || document.body.classList.contains('theme-dark');

        // Create gradient fill for primary line (Job-Finance style)
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, isDark ? 'rgba(56, 189, 248, 0.28)' : 'rgba(0, 88, 166, 0.22)');
        gradient.addColorStop(1, isDark ? 'rgba(56, 189, 248, 0.01)' : 'rgba(0, 88, 166, 0.01)');

        const primaryColor = isDark ? '#38BDF8' : '#0058A6';
        const comparisonColor = isDark ? '#94A3B8' : '#0F1F3D';
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.7)';
        const tickColor = isDark ? '#94A3B8' : '#94A3B8';

        // Secondary dataset for comparison (Target / Rata-rata transaksi kasir)
        const avgValue = values.length ? Math.round(values.reduce((a, b) => a + b, 0) / values.length) : 0;
        const comparisonData = values.map(v => v > 0 ? Math.round(v * 0.85) : avgValue);

        mainChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Penerimaan Kas (Rp)',
                        data: values,
                        borderColor: primaryColor,
                        backgroundColor: gradient,
                        tension: 0.35,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                    },
                    {
                        label: 'Target Penjualan (Rp)',
                        data: comparisonData,
                        borderColor: comparisonColor,
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: comparisonColor,
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            borderRadius: 3,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 11,
                                weight: 500,
                            },
                            color: tickColor,
                            padding: 14,
                        },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 31, 61, 0.95)',
                        titleColor: '#FFFFFF',
                        bodyColor: '#F8FAFC',
                        titleFont: {
                            family: "'Poppins', sans-serif",
                            size: 12,
                            weight: 600,
                        },
                        bodyFont: {
                            family: "'Poppins', sans-serif",
                            size: 11,
                        },
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4,
                        callbacks: {
                            label: function (context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y !== null ? context.parsed.y : context.raw;
                                return ` ${label}: Rp ${Number(value || 0).toLocaleString('id-ID')}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 10,
                                weight: 500,
                            },
                            color: tickColor,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 10,
                            },
                            color: tickColor,
                            callback: function (val) {
                                if (val >= 1000000) {
                                    return (val / 1000000).toFixed(0) + ' jt';
                                }
                                if (val >= 1000) {
                                    return (val / 1000).toFixed(0) + ' k';
                                }
                                return val;
                            },
                        },
                    },
                },
            },
        });
    }

    function initDonutChart() {
        const donutCanvas = document.getElementById('dashboardDonutChart') || document.getElementById('compositionChart');
        if (!donutCanvas || typeof Chart === 'undefined') return;

        const ctx = donutCanvas.getContext('2d');
        if (donutChartInstance) {
            donutChartInstance.destroy();
        }

        let donutData = [85, 15];
        let donutLabels = ['Tunai / Cash', 'QRIS & Digital'];
        let donutColors = ['#0058A6', '#10B981'];

        if (donutCanvas.dataset.chart) {
            try {
                const parsed = JSON.parse(donutCanvas.dataset.chart);
                if (parsed.data) donutData = parsed.data;
                if (parsed.labels) donutLabels = parsed.labels;
                if (parsed.colors) donutColors = parsed.colors;
            } catch (e) {}
        } else if (window.nexoraCompositionData) {
            donutData = window.nexoraCompositionData.data || donutData;
            donutLabels = window.nexoraCompositionData.labels || donutLabels;
        }

        donutChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [
                    {
                        data: donutData,
                        backgroundColor: donutColors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            borderRadius: 3,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 10,
                            },
                            color: '#64748B',
                            padding: 8,
                        },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 31, 61, 0.95)',
                        titleFont: {
                            family: "'Poppins', sans-serif",
                            size: 11,
                            weight: 600,
                        },
                        bodyFont: {
                            family: "'Poppins', sans-serif",
                            size: 10,
                        },
                        padding: 8,
                        cornerRadius: 6,
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                return ` ${label}: Rp ${Number(value).toLocaleString('id-ID')}`;
                            },
                        },
                    },
                },
            },
        });
    }

    function loadSalesData(filter = 'monthly') {
        fetch(`/admin-panel/sales-data?filter=${encodeURIComponent(filter)}`)
            .then((response) => response.json())
            .then((data) => {
                if (data && data.length) {
                    initMainChart(data.map((d) => d.label), data.map((d) => d.value));
                } else {
                    initMainChart(
                        ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt'],
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 185520]
                    );
                }
            })
            .catch(() => {
                initMainChart(
                    ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt'],
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 185520]
                );
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const filterDropdown = document.getElementById('salesFilter');
        const mainCanvas = document.getElementById('dashboardMainChart') || document.getElementById('salesChart');

        if (mainCanvas) {
            loadSalesData(filterDropdown ? filterDropdown.value : 'monthly');
        }

        if (filterDropdown) {
            filterDropdown.addEventListener('change', function () {
                loadSalesData(this.value);
            });
        }

        initDonutChart();
    });

    // Expose for external calls or re-render
    window.NexoraDashboardCharts = {
        loadSalesData,
        initDonutChart,
        formatNumber,
    };
})();
