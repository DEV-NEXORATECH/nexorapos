    let chart;

    // Function to fetch and render chart
    function loadChart(filter = 'monthly') {
        fetch(`/admin-panel/sales-data?filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.label);
                const values = data.map(item => item.value);

                // Destroy old chart
                if (chart) {
                    chart.destroy();
                }

                // Create chart
                chart = new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Products Sold',
                            data: values,
                            borderColor: 'blue',
                            backgroundColor: 'rgba(0,123,255,0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching sales data:', error));
    }

    // Run after DOM is ready
    document.addEventListener("DOMContentLoaded", function() {
        const filterDropdown = document.getElementById('salesFilter'); // ✅ Must match Blade <select id="salesFilter">

        // Default: monthly
        loadChart('monthly');

        // Change filter
        filterDropdown.addEventListener('change', function() {
            loadChart(this.value);
        });
    });



document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    // 🔹 Random demo data
    const data = {
        labels: ['Active Users', 'Inactive Users', 'New Signups'],
        datasets: [{
            data: [
                Math.floor(Math.random() * 200) + 50, // Active
                Math.floor(Math.random() * 100) + 20, // Inactive
                Math.floor(Math.random() * 50) + 10   // New
            ],
            backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56'],
            hoverOffset: 10
        }]
    };

    // 🔹 Create Pie Chart
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.label + ': ' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});