@extends('admin.layout.app')

@section('title', 'Dashboard')

@section('content')

    <div class="main-panel dashboard-page">
        <div class="content">
            <div class="container-fluid">

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <div class="nexora-blue-card is-success">
                            <div class="nexora-blue-card-icon"><i class="la la-money"></i></div>
                            <div class="nexora-blue-card-info">
                                <span class="nexora-blue-card-label">Total Income</span>
                                <span class="nexora-blue-card-value">Rp {{ number_format($payments, 0, ',', '.') }}</span>
                                <small>{{ $paymentCount }} payment aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="nexora-blue-card is-warning">
                            <div class="nexora-blue-card-icon"><i class="la la-qrcode"></i></div>
                            <div class="nexora-blue-card-info">
                                <span class="nexora-blue-card-label">QRIS Income</span>
                                <span class="nexora-blue-card-value">Rp {{ number_format($qrisPayments, 0, ',', '.') }}</span>
                                <small>{{ $qrisPaymentCount }} QRIS payment aktif</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Session::has('failed'))
                    <div class="alert alert-danger">{{ Session::get('failed') }}</div>
                @endif

                <div class="superadmin-header-card mb-4">
                    <h4><i class="fas fa-chart-pie"></i> Dashboard</h4>
                </div>
                @php
                    $dashboardCompany = auth()->user()?->company;
                    $dashboardLicense = $dashboardCompany && ! auth()->user()?->isSuperAdmin()
                        ? $dashboardCompany->licenseSnapshot()
                        : null;
                @endphp

                @if($dashboardLicense)
                    <div class="nexora-license-card nexora-license-card-{{ $dashboardLicense['badge_class'] }} mb-4">
                        <div class="nexora-license-card-head">
                            <div>
                                <span>License Status</span>
                                <h5>{{ $dashboardCompany->name }}</h5>
                                <p>{{ $dashboardLicense['package'] }} • {{ $dashboardLicense['payment_status'] }}</p>
                            </div>
                            <a href="{{ url('admin-panel/settings/license') }}" class="btn btn-secondary btn-sm">View Detail</a>
                        </div>
                        <div class="nexora-license-card-grid">
                            <div>
                                <small>Status</small>
                                <strong class="text-{{ $dashboardLicense['badge_class'] }}">{{ $dashboardLicense['label'] }}</strong>
                            </div>
                            <div>
                                <small>Start</small>
                                <strong>{{ optional($dashboardLicense['started_at'])->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div>
                                <small>Expiry</small>
                                <strong>{{ optional($dashboardLicense['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div>
                                <small>Grace</small>
                                <strong>{{ $dashboardLicense['grace_period_days'] }} days</strong>
                            </div>
                            <div>
                                <small>Features</small>
                                <strong>{{ $dashboardLicense['active_features'] }} / {{ $dashboardLicense['total_features'] }}</strong>
                            </div>
                            <div>
                                <small>User Limit</small>
                                <strong>{{ $dashboardLicense['current_users'] }} / {{ $dashboardLicense['max_users'] ?: 'Unlimited' }}</strong>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ========== SUPER ADMIN: Platform Stats (Colored Cards) ========== --}}
                @if($platformStats)
                    <div class="row mb-3">
                        @php
                            $colors = ['is-success', 'is-warning', 'is-danger', 'is-neutral', 'is-success', 'is-warning'];
                        @endphp
                        @foreach([
                            ['label' => 'Total Companies', 'value' => $platformStats['total_companies'], 'icon' => 'la la-building'],
                            ['label' => 'Active Companies', 'value' => $platformStats['active_companies'], 'icon' => 'la la-check-circle'],
                            ['label' => 'Active Licenses', 'value' => $platformStats['active_licenses'], 'icon' => 'la la-certificate'],
                            ['label' => 'Expiring Licenses', 'value' => $platformStats['expiring_licenses'], 'icon' => 'la la-clock-o'],
                            ['label' => 'Total Users', 'value' => $platformStats['total_users'], 'icon' => 'la la-users'],
                            ['label' => 'Enabled Features', 'value' => $platformStats['enabled_features'], 'icon' => 'la la-toggle-on'],
                        ] as $index => $stat)
                            <div class="col-md-4 col-xl-2 mb-3">
                                <div class="nexora-blue-card {{ $colors[$index] }}">
                                    <div class="nexora-blue-card-icon">
                                        <i class="{{ $stat['icon'] }}"></i>
                                    </div>
                                    <div class="nexora-blue-card-info">
                                        <span class="nexora-blue-card-label">{{ $stat['label'] }}</span>
                                        <span class="nexora-blue-card-value">{{ $stat['value'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif


                {{-- ========== Charts Section ========== --}}
                <div class="row mt-3">
                    {{-- Sales Trend Chart --}}
                    <div class="col-md-8 mb-3">
                        <div class="card dashboard-chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Sales Trend</h4>
                                    <p class="card-category">Revenue overview</p>
                                </div>
                                <select id="salesFilter" class="form-control w-auto">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <canvas id="salesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Data Composition Donut --}}
                    <div class="col-md-4 mb-3">
                        <div class="card dashboard-chart-card">
                            <div class="card-header">
                                <h4 class="card-title">Data Composition</h4>
                                <p class="card-category">Master data breakdown</p>
                            </div>
                            <div class="card-body">
                                <canvas id="compositionChart" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Transaction Summary Bar --}}
                    <div class="col-md-6 mb-3">
                        <div class="card dashboard-chart-card">
                            <div class="card-header">
                                <h4 class="card-title">Transaction Summary</h4>
                                <p class="card-category">Purchases vs Sales vs Payments</p>
                            </div>
                            <div class="card-body">
                                <canvas id="transactionChart" height="160"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Inventory Overview --}}
                    <div class="col-md-6 mb-3">
                        <div class="card dashboard-chart-card">
                            <div class="card-header">
                                <h4 class="card-title">Inventory Overview</h4>
                                <p class="card-category">Products, Variants & Stock Items</p>
                            </div>
                            <div class="card-body">
                                <canvas id="inventoryChart" height="160"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
<style>
    .dashboard-page, .dashboard-page .content, .dashboard-page .container-fluid {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .dashboard-page .row, .dashboard-page .card, .dashboard-page canvas {
        min-width: 0 !important;
        max-width: 100% !important;
    }
    .dashboard-page .nexora-license-card-grid {
        min-width: 0 !important;
        width: 100% !important;
    }
    @media (max-width: 1199px) {
        .dashboard-page .nexora-license-card-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .dashboard-page .nexora-license-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-page .content { padding-left: 10px !important; padding-right: 10px !important; }
    }

    /* ===== Blue Background Cards (Platform Stats) ===== */
    .nexora-blue-card {
        background: #0058A6;
        border-radius: 12px;
        padding: 20px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .nexora-blue-card:hover {
        background: #003F7D;
        box-shadow: 0 6px 20px rgba(0, 88, 166, 0.25);
        transform: translateY(-2px);
    }
    .nexora-blue-card-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nexora-blue-card-icon i {
        font-size: 20px;
        color: #FFFFFF !important;
    }
    .nexora-blue-card-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .nexora-blue-card-label {
        font-size: 11px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.75) !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nexora-blue-card-value {
        font-size: 22px;
        font-weight: 700;
        color: #FFFFFF !important;
        line-height: 1.2;
    }

    /* ===== White Stat Cards (POS Management) ===== */
    .nexora-stat-card {
        display: flex;
        align-items: center;
        background: #FFFFFF;
        border: 1px solid #DCEEFF;
        border-radius: 12px;
        padding: 18px 16px;
        transition: all 0.2s ease;
        gap: 14px;
        height: 100%;
    }
    .nexora-stat-card:hover {
        border-color: #0058A6;
        box-shadow: 0 4px 16px rgba(0, 88, 166, 0.08);
        transform: translateY(-2px);
    }
    .nexora-stat-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 12px;
        background: #EAF5FF;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nexora-stat-icon i {
        font-size: 22px;
        color: #0058A6;
    }
    .nexora-stat-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .nexora-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #1976C9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nexora-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #002E58;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .nexora-stat-link {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        background: #F4FAFF;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .nexora-stat-link i {
        font-size: 16px;
        color: #0058A6 !important;
    }
    .nexora-stat-link:hover {
        background: #0058A6;
    }
    .nexora-stat-link:hover i {
        color: #FFFFFF !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    var chartTextColor = '#000000';
    var chartGridColor = 'rgba(0, 0, 0, 0.05)';
    var chartPrimaryColor = '#6366F1'; 
    var chartFillColor = 'rgba(99, 102, 241, 0.1)';
    
    Chart.defaults.color = chartTextColor;

    // ===== 1. SALES TREND CHART =====
    var salesChartInstance = null;
    function loadSalesChart(filter) {
        filter = filter || 'monthly';
        fetch('/admin-panel/sales-data?filter=' + encodeURIComponent(filter))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var labels = data.map(function (d) { return d.label; });
                var values = data.map(function (d) { return d.value; });

                if (salesChartInstance) salesChartInstance.destroy();

                salesChartInstance = new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue',
                            data: values,
                            borderColor: chartPrimaryColor,
                            backgroundColor: chartFillColor,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: chartPrimaryColor,
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1E1E1E',
                                titleColor: '#FFFFFF',
                                bodyColor: '#FFFFFF',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (ctx) {
                                        return 'Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: chartGridColor },
                                ticks: {
                                    color: chartTextColor,
                                    callback: function (v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: chartTextColor }
                            }
                        }
                    }
                });
            })
            .catch(function (e) { console.error('Sales chart error:', e); });
    }

    var salesFilter = document.getElementById('salesFilter');
    if (salesFilter) {
        loadSalesChart(salesFilter.value);
        salesFilter.addEventListener('change', function () { loadSalesChart(this.value); });
    }

    // ===== 2. DATA COMPOSITION DONUT =====
    var compCtx = document.getElementById('compositionChart');
    if (compCtx) {
        new Chart(compCtx, {
            type: 'doughnut',
            data: {
                labels: ['Products', 'Variants', 'Customers', 'Suppliers', 'Stores', 'Categories'],
                datasets: [{
                    data: [
                        {{ $products }},
                        {{ $variants }},
                        {{ $customers }},
                        {{ $suppliers }},
                        {{ $store }},
                        {{ $cats }}
                    ],
                    backgroundColor: [
                        '#6366F1',
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6'
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: chartTextColor,
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E1E1E',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 8,
                    }
                }
            }
        });
    }

    // ===== 3. TRANSACTION SUMMARY BAR =====
    var txCtx = document.getElementById('transactionChart');
    if (txCtx) {
        new Chart(txCtx, {
            type: 'bar',
            data: {
                labels: ['Purchases', 'Sales', 'Payments'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $purchases }}, {{ $sales }}, {{ $paymentCount }}],
                    backgroundColor: ['#6366F1', '#3B82F6', '#10B981'],
                    borderRadius: 8,
                    barThickness: 40,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E1E1E',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            afterBody: function (ctx) {
                                if (ctx[0].label === 'Payments') {
                                    return 'Total: Rp {{ number_format($payments, 0, ",", ".") }}';
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: { color: chartTextColor, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: chartTextColor, font: { weight: '600' } }
                    }
                }
            }
        });
    }

    // ===== 4. INVENTORY OVERVIEW HORIZONTAL BAR =====
    var invCtx = document.getElementById('inventoryChart');
    if (invCtx) {
        new Chart(invCtx, {
            type: 'bar',
            data: {
                labels: ['Products', 'Variants', 'Purchase Items', 'Categories', 'Taxes'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $products }}, {{ $variants }}, {{ $items }}, {{ $cats }}, {{ $taxes }}],
                    backgroundColor: [
                        '#6366F1',
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ],
                    borderRadius: 6,
                    barThickness: 24,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E1E1E',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: { color: chartTextColor, stepSize: 1 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: chartTextColor, font: { weight: '600', size: 12 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
