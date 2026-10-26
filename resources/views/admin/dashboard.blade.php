@extends('admin.layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="main-panel dashboard-page">
    <div class="content">
        <div class="container-fluid">

            {{-- 1. HERO BANNER (EXACT JOB-FINANCE STYLE) --}}
            <div class="dashboard-hero-banner">
                <div class="dashboard-hero-left">
                    <div class="dashboard-hero-badges">
                        <span class="hero-tag-pill">
                            <i class="fa fa-circle" style="font-size: 6px; color: #10B981;"></i>
                            WORKSPACE POS
                        </span>
                        <span class="hero-tag-pill">
                            <i class="fa fa-calendar-alt"></i>
                            {{ now()->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <h2>Operasional Toko & Penjualan Lebih Terkontrol.</h2>
                    <p>Pantau omset harian, buka kasir POS, cek transaksi terbaru, dan kelola inventaris toko secara real-time dalam satu workspace terpadu.</p>
                    <a href="{{ url('admin-panel/pos/sell') }}" class="dashboard-hero-btn">
                        <i class="fa fa-cash-register mr-1"></i> Buka Kasir POS &rarr;
                    </a>
                </div>
                <div class="dashboard-hero-right d-none d-lg-flex">
                    <div class="hero-stat-card-preview">
                        <div class="hero-stat-badge-check">✓</div>
                        <small>Omset Real-Time</small>
                        <strong>Rp {{ number_format($payments, 0, ',', '.') }}</strong>
                        <div class="hero-bars-sim">
                            <div class="hero-bar-col" style="height: 35%;"></div>
                            <div class="hero-bar-col" style="height: 55%;"></div>
                            <div class="hero-bar-col" style="height: 45%;"></div>
                            <div class="hero-bar-col" style="height: 70%;"></div>
                            <div class="hero-bar-col" style="height: 60%;"></div>
                            <div class="hero-bar-col" style="height: 95%; background: #10B981;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LICENSE / TENANT STATUS (IF APPLICABLE) --}}
            @php
                $dashboardCompany = auth()->user()?->company;
                $dashboardLicense = $dashboardCompany && ! auth()->user()?->isSuperAdmin()
                    ? $dashboardCompany->licenseSnapshot()
                    : null;
            @endphp

            @if($dashboardLicense)
                <div class="nexora-license-card mb-4">
                    <div class="nexora-license-card-head">
                        <div>
                            <span>STATUS LISENSI PERUSAHAAN</span>
                            <h5>{{ $dashboardCompany->name }}</h5>
                            <small class="text-muted">{{ $dashboardLicense['package'] }} • {{ $dashboardLicense['payment_status'] }}</small>
                        </div>
                        <a href="{{ url('admin-panel/settings/license') }}" class="btn btn-outline-primary btn-sm">Detail Lisensi</a>
                    </div>
                    <div class="nexora-license-card-grid">
                        <div>
                            <small>Status</small>
                            <strong class="text-{{ $dashboardLicense['badge_class'] }}">{{ $dashboardLicense['label'] }}</strong>
                        </div>
                        <div>
                            <small>Mulai</small>
                            <strong>{{ optional($dashboardLicense['started_at'])->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <small>Kadaluarsa</small>
                            <strong>{{ optional($dashboardLicense['expiry_date'])->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <small>Grace Period</small>
                            <strong>{{ $dashboardLicense['grace_period_days'] }} Hari</strong>
                        </div>
                        <div>
                            <small>Fitur Aktif</small>
                            <strong>{{ $dashboardLicense['active_features'] }} / {{ $dashboardLicense['total_features'] }}</strong>
                        </div>
                        <div>
                            <small>Batas User</small>
                            <strong>{{ $dashboardLicense['current_users'] }} / {{ $dashboardLicense['max_users'] ?: 'Unlimited' }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUPER ADMIN PLATFORM STATS --}}
            @if(isset($platformStats) && $platformStats)
                <div class="row mb-3">
                    <div class="col-12 mb-2">
                        <span class="eyebrow-tag"><span class="dot"></span> PLATFORM MASTER OVERVIEW</span>
                    </div>
                    @foreach([
                        ['label' => 'Total Perusahaan', 'value' => $platformStats['total_companies'], 'icon' => 'fa fa-building', 'color' => 'blue'],
                        ['label' => 'Perusahaan Aktif', 'value' => $platformStats['active_companies'], 'icon' => 'fa fa-check-circle', 'color' => 'emerald'],
                        ['label' => 'Lisensi Aktif', 'value' => $platformStats['active_licenses'], 'icon' => 'fa fa-certificate', 'color' => 'purple'],
                        ['label' => 'Lisensi Warning', 'value' => $platformStats['expiring_licenses'], 'icon' => 'fa fa-clock', 'color' => 'amber'],
                        ['label' => 'Total Pengguna', 'value' => $platformStats['total_users'], 'icon' => 'fa fa-users', 'color' => 'blue'],
                        ['label' => 'Fitur Terpasang', 'value' => $platformStats['enabled_features'], 'icon' => 'fa fa-toggle-on', 'color' => 'emerald'],
                    ] as $stat)
                        <div class="col-md-4 col-xl-2 mb-3">
                            <div class="metric-card-item">
                                <div class="metric-card-top">
                                    <span class="metric-card-label">{{ $stat['label'] }}</span>
                                    <span class="metric-card-icon {{ $stat['color'] }}"><i class="{{ $stat['icon'] }}"></i></span>
                                </div>
                                <div class="metric-card-value">{{ $stat['value'] }}</div>
                                <span class="metric-card-footer">Tenant Platform</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 2. SECTION: ANALISIS ARUS PENJUALAN & TRANSAKSI (EXACT JOB-FINANCE DASHBOARD-HERO-CHARTS) --}}
            <section class="dashboard-hero-charts" aria-label="Grafik Analitik Dashboard">
                <div class="chart-hero-top">
                    <div class="chart-hero-title-area">
                        <span class="chart-live-badge">
                            <span class="pulse-indicator"></span>
                            ANALISIS PENJUALAN
                        </span>
                        <h2 class="chart-hero-title">Arus Penjualan & Penerimaan Kas</h2>
                        <p class="chart-hero-subtitle">Monitoring penerimaan kasir dan tren transaksi toko secara real-time</p>
                    </div>
                    <div class="chart-hero-controls">
                        <span class="chart-status-pill">
                            <i class="fa fa-chart-line mr-1"></i>
                            <span>Arus Kasir Stabil</span>
                        </span>
                        <div class="chart-period-badge">
                            <i class="fa fa-calendar-alt mr-1"></i>
                            <select id="salesFilter" class="chart-period-select">
                                <option value="daily">Hari Ini (Per Jam)</option>
                                <option value="weekly">Minggu Ini (Per Hari)</option>
                                <option value="monthly" selected>Bulan Ini (Per Bulan)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="chart-hero-grid">
                    <!-- Main Line Visual Chart -->
                    <div class="chart-card-main">
                        <div class="chart-card-inner">
                            <div class="chart-canvas-wrap">
                                <canvas id="dashboardMainChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Donut & KPI Stats -->
                    <div class="chart-card-side">
                        <div class="donut-chart-box">
                            <div class="donut-header">
                                <h4>Metode Pembayaran (Transaksi)</h4>
                                <p>Komposisi penerimaan kasir toko</p>
                            </div>
                            <div class="donut-canvas-wrap">
                                <canvas id="dashboardDonutChart"></canvas>
                            </div>
                        </div>

                        <div class="chart-kpi-strip">
                            <div class="chart-kpi-item">
                                <span class="kpi-label">TOTAL OMSET</span>
                                <strong class="kpi-val">Rp {{ number_format($payments, 0, ',', '.') }}</strong>
                                <small class="kpi-sub">Seluruh transaksi</small>
                            </div>
                            <div class="chart-kpi-item">
                                <span class="kpi-label">TRANSAKSI QRIS</span>
                                <strong class="kpi-val">Rp {{ number_format($qrisPayments, 0, ',', '.') }}</strong>
                                <small class="kpi-sub">{{ $qrisPaymentCount }} transaksi</small>
                            </div>
                            <div class="chart-kpi-item">
                                <span class="kpi-label">TOTAL PENJUALAN</span>
                                <strong class="kpi-val">{{ $sales }} TRX</strong>
                                <small class="kpi-sub">Selesai terlayani</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3. WIDE SUMMARY CARD (RINGKASAN PENJUALAN HARI INI) --}}
            <div class="wide-summary-card">
                <div class="wide-summary-head">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> POSISI OPERASIONAL</span>
                        <h4 class="card-title">Ringkasan Penjualan Hari Ini</h4>
                        <p class="card-category">Saldo transaksi kasir dan penerimaan pembayaran terverifikasi.</p>
                    </div>
                    <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                        Lihat Riwayat &rarr;
                    </a>
                </div>
                <div class="wide-summary-cols">
                    <div class="wide-summary-col">
                        <small>Total Omset Diterima</small>
                        <strong>Rp {{ number_format($payments, 0, ',', '.') }}</strong>
                        <span>Termasuk pembayaran QRIS & Tunai</span>
                    </div>
                    <div class="wide-summary-col">
                        <small>Total Transaksi Kasir</small>
                        <strong class="text-primary">{{ $sales }} Transaksi</strong>
                        <span>Penjualan berhasil diselesaikan</span>
                    </div>
                    <div class="wide-summary-col">
                        <small>Penerimaan QRIS & Digital</small>
                        <strong class="text-success">Rp {{ number_format($qrisPayments, 0, ',', '.') }}</strong>
                        <span>{{ $qrisPaymentCount }} transaksi non-tunai</span>
                    </div>
                </div>
                <div class="wide-summary-progress-wrap">
                    @php
                        $cashAmount = max(0, $payments - $qrisPayments);
                        $totalAmt = max(1, $payments);
                        $cashPercent = round(($cashAmount / $totalAmt) * 100);
                        $qrisPercent = 100 - $cashPercent;
                    @endphp
                    <div class="wide-summary-progress-labels">
                        <span><i class="fa fa-circle text-primary" style="font-size: 8px;"></i> Tunai / Cash ({{ $cashPercent }}%)</span>
                        <span><i class="fa fa-circle text-success" style="font-size: 8px;"></i> QRIS & Non-Tunai ({{ $qrisPercent }}%)</span>
                    </div>
                    <div class="wide-summary-progress-bar">
                        <div class="progress-fill-cash" style="width: {{ $cashPercent }}%;"></div>
                        <div class="progress-fill-qris" style="width: {{ $qrisPercent }}%;"></div>
                    </div>
                </div>
            </div>

            {{-- 4. SECTION: METRIC CARDS (8 ITEMS - 4x2 GRID EXACT JOB-FINANCE STYLE) --}}
            <div class="metric-grid-8">
                {{-- Card 1 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Penjualan Terdaftar</span>
                        <span class="metric-card-icon amber"><i class="fa fa-receipt"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $sales }}</div>
                    <span class="metric-card-footer">Invoice penjualan aktif</span>
                </div>

                {{-- Card 2 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Penerimaan Kasir</span>
                        <span class="metric-card-icon purple"><i class="fa fa-money-bill-wave"></i></span>
                    </div>
                    <div class="metric-card-value">Rp {{ number_format($payments, 0, ',', '.') }}</div>
                    <span class="metric-card-footer">Total penerimaan kasir</span>
                </div>

                {{-- Card 3 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">QRIS Payment</span>
                        <span class="metric-card-icon blue"><i class="fa fa-qrcode"></i></span>
                    </div>
                    <div class="metric-card-value">Rp {{ number_format($qrisPayments, 0, ',', '.') }}</div>
                    <span class="metric-card-footer">{{ $qrisPaymentCount }} transaksi QRIS</span>
                </div>

                {{-- Card 4 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Toko / Cabang</span>
                        <span class="metric-card-icon emerald"><i class="fa fa-store"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $store }} Toko</div>
                    <span class="metric-card-footer">Cabang aktif beroperasi</span>
                </div>

                {{-- Card 5 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Katalog Produk</span>
                        <span class="metric-card-icon amber"><i class="fa fa-cube"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $products }}</div>
                    <span class="metric-card-footer">Master SKU produk</span>
                </div>

                {{-- Card 6 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Varian Produk</span>
                        <span class="metric-card-icon purple"><i class="fa fa-cubes"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $variants }}</div>
                    <span class="metric-card-footer">Varian warna, size, opsi</span>
                </div>

                {{-- Card 7 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Data Supplier</span>
                        <span class="metric-card-icon blue"><i class="fa fa-truck"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $suppliers }}</div>
                    <span class="metric-card-footer">Pemasok persediaan</span>
                </div>

                {{-- Card 8 --}}
                <div class="metric-card-item">
                    <div class="metric-card-top">
                        <span class="metric-card-label">Data Pelanggan</span>
                        <span class="metric-card-icon emerald"><i class="fa fa-users"></i></span>
                    </div>
                    <div class="metric-card-value">{{ $customers }}</div>
                    <span class="metric-card-footer">Customer profil terdaftar</span>
                </div>
            </div>

            {{-- 5. SECTION: TRANSAKSI TERAKHIR (RECENT SALES TABLE) --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> TRANSAKSI TERBARU</span>
                        <h4 class="card-title">Penjualan Terakhir Kasir</h4>
                        <p class="card-category">Daftar transaksi kasir terbaru yang diproses di sistem.</p>
                    </div>
                    <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                        Lihat Semua Penjualan &rarr;
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern table-recent-sales table-hover">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Toko / Cabang</th>
                                <th>Kasir</th>
                                <th>Pelanggan</th>
                                <th>Waktu</th>
                                <th class="text-right">Total Tagihan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recentSales) && $recentSales->isNotEmpty())
                                @foreach($recentSales as $sale)
                                    <tr>
                                        <td>
                                            <strong class="text-primary font-weight-bold">{{ $sale->invoice_number ?: 'TRX-'.$sale->id }}</strong>
                                        </td>
                                        <td>{{ $sale->store?->name ?? 'Toko Pusat' }}</td>
                                        <td>{{ $sale->user?->name ?? 'Kasir' }}</td>
                                        <td>{{ $sale->customer?->name ?? 'Pelanggan Umum' }}</td>
                                        <td><small class="text-muted">{{ $sale->created_at?->format('d/m/Y H:i') }}</small></td>
                                        <td class="text-right font-weight-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">Selesai</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('admin-panel/pos/invoice/'.$sale->id) }}" class="btn-action btn-action-primary" title="Lihat Struk">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fa fa-receipt text-muted mb-2" style="font-size: 28px; display: block;"></i>
                                        Belum ada transaksi penjualan yang tercatat. Buka Kasir POS untuk mulai transaksi!
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    window.nexoraCompositionData = {
        data: [{{ max(0, $payments - $qrisPayments) ?: 1 }}, {{ $qrisPayments ?: 1 }}],
        labels: ['Tunai / Cash', 'QRIS & Digital'],
        colors: ['#0058A6', '#10B981']
    };
    if (window.NexoraDashboardCharts && typeof window.NexoraDashboardCharts.initDonutChart === 'function') {
        window.NexoraDashboardCharts.initDonutChart();
    }
</script>
@endpush
