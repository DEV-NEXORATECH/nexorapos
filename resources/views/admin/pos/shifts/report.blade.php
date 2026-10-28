@extends('admin.layout.app')

@section('title', 'Laporan Shift Kasir')

@section('content')
<style>
    .shift-stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid #eef2f6;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .shift-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }
    .shift-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .stat-blue { background: #e8f3fc; color: #1572e8; }
    .stat-green { background: #e9f8f1; color: #2ecc71; }
    .stat-purple { background: #f2edf8; color: #6f42c1; }
    .stat-red { background: #feebeb; color: #e74c3c; }

    @media print {
        .main-header, .sidebar, .navbar, .topbar, .footer, .nexora-page-header-card, .btn, .no-print {
            display: none !important;
        }
        .main-panel {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }
        .shift-stat-card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Header Card --}}
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Laporan Shift Kasir</h4>
                        <p class="nexora-page-header-desc">Pantau kas awal, kas tutup, selisih fisik kasir, dan ringkasan penjualan per shift.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <form method="GET" action="{{ url('admin-panel/pos/shifts/report') }}" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <input type="date" name="date" value="{{ $date }}" class="form-control nexora-input" style="width: auto; height: 40px;">
                        <button class="btn nexora-btn-primary" type="submit">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ url('admin-panel/pos/sell') }}" class="btn nexora-btn-secondary">
                            <i class="fas fa-cash-register mr-1"></i> POS
                        </a>
                    </form>
                </div>
            </div>

            @if($migrationMissing)
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Migration shift belum dijalankan. Jalankan <code>php artisan migrate</code> supaya pencatatan shift aktif sepenuhnya.
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="shift-stat-card">
                        <div class="shift-stat-icon stat-blue">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Total Shift</small>
                            <h4 class="mb-0 font-weight-bold text-dark">{{ $summary['total_shift'] }}</h4>
                            <span class="small text-muted">Sesi kasir tercatat</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="shift-stat-card">
                        <div class="shift-stat-icon stat-purple">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Shift Aktif</small>
                            <h4 class="mb-0 font-weight-bold text-primary">{{ $summary['open_shift'] }}</h4>
                            <span class="small text-muted">Sedang berjalan</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="shift-stat-card">
                        <div class="shift-stat-icon stat-green">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Total Penjualan</small>
                            <h4 class="mb-0 font-weight-bold text-dark">Rp {{ number_format($summary['sales_total'], 0, ',', '.') }}</h4>
                            <span class="small text-muted">Akumulasi seluruh shift</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="shift-stat-card">
                        <div class="shift-stat-icon {{ abs($summary['cash_difference']) > 0 ? 'stat-red' : 'stat-green' }}">
                            <i class="fas fa-{{ abs($summary['cash_difference']) > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Selisih Kas Fisik</small>
                            <h4 class="mb-0 font-weight-bold {{ abs($summary['cash_difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($summary['cash_difference'], 0, ',', '.') }}
                            </h4>
                            <span class="small {{ abs($summary['cash_difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                {{ abs($summary['cash_difference']) > 0 ? 'Perlu investigasi' : 'Kas fisik cocok' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="nexora-table-card">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-alt mr-2 text-primary"></i>
                        <h5 class="mb-0 font-weight-bold text-dark">Daftar Sesi Shift Kasir ({{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }})</h5>
                    </div>
                    <span class="nexora-badge is-primary">{{ count($shifts) }} sesi</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kasir</th>
                                <th>Store</th>
                                <th>Nama Shift</th>
                                <th>Status</th>
                                <th>Waktu Buka</th>
                                <th>Waktu Tutup</th>
                                <th class="text-right">Kas Awal</th>
                                <th class="text-right">Kas Tutup</th>
                                <th class="text-right">Ekspektasi</th>
                                <th class="text-right">Selisih</th>
                                <th class="text-center">Struk</th>
                                <th class="text-right">Total Sales</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                                <tr>
                                    <td class="font-weight-bold text-dark">{{ $shift->user?->name ?? '-' }}</td>
                                    <td>{{ $shift->store?->name ?? '-' }}</td>
                                    <td>{{ $shift->shift_name ?: '-' }}</td>
                                    <td>
                                        <span class="nexora-badge {{ $shift->status === 'open' ? 'is-warning' : 'is-success' }}">
                                            {{ strtoupper($shift->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $shift->opened_at?->format('d M H:i') ?? '-' }}</td>
                                    <td>{{ $shift->closed_at?->format('d M H:i') ?? '-' }}</td>
                                    <td class="text-right">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $shift->closing_cash === null ? '-' : 'Rp ' . number_format($shift->closing_cash, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold {{ abs($shift->cash_difference) > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($shift->cash_difference, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="nexora-badge is-info">{{ $shift->sales_count }}</span>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        Rp {{ number_format($shift->sales_total, 0, ',', '.') }}
                                    </td>
                                    <td class="small text-muted">{{ $shift->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">
                                        Belum ada riwayat shift pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($shifts, 'links'))
                    <div class="p-3 border-top d-flex justify-content-end">{{ $shifts->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
