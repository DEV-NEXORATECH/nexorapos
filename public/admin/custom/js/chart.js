(function () {
    let salesChartInstance = null;

    function loadSalesChart(filter = 'monthly') {
        const canvas = document.getElementById('salesChart');
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        fetch(`/admin-panel/sales-data?filter=${encodeURIComponent(filter)}`)
            .then((response) => response.json())
            .then((data) => {
                const labels = data.map((item) => item.label);
                const values = data.map((item) => item.value);

                if (salesChartInstance) {
                    salesChartInstance.destroy();
                }

                salesChartInstance = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Sales',
                            data: values,
                            borderColor: '#0058A6',
                            backgroundColor: 'rgba(0, 88, 166, 0.12)',
                            fill: true,
                            tension: 0.35,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback(value) {
                                        return Number(value).toLocaleString();
                                    },
                                },
                            },
                        },
                    },
                });
            })
            .catch((error) => console.error('Error fetching sales data:', error));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const filterDropdown = document.getElementById('salesFilter');
        const salesCanvas = document.getElementById('salesChart');

        if (salesCanvas) {
            loadSalesChart(filterDropdown ? filterDropdown.value : 'monthly');
        }

        if (filterDropdown) {
            filterDropdown.addEventListener('change', function () {
                loadSalesChart(this.value);
            });
        }
    });
})();
