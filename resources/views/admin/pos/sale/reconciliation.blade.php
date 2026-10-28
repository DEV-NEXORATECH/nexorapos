@extends('admin.layout.app')

@section('title', 'Rekonsiliasi Harian')

@section('content')
<style>
    .reconciliation-stat-card {
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
    .reconciliation-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }
    .reconciliation-stat-icon {
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
    .stat-orange { background: #fef5ea; color: #e67e22; }
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
        .reconciliation-stat-card {
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
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Rekonsiliasi Harian</h4>
                        <p class="nexora-page-header-desc">Cocokkan total struk transaksi penjualan kasir dengan uang masuk per metode pembayaran.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <form method="GET" action="{{ url('admin-panel/sales/reconciliation') }}" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <input type="date" name="date" value="{{ $date }}" class="form-control nexora-input" style="width: auto; height: 40px;">
                        <button class="btn nexora-btn-primary" type="submit">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <button class="btn nexora-btn-secondary" type="button" onclick="window.print()">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <a href="{{ url('admin-panel/pos/sell') }}" class="btn nexora-btn-secondary">
                            <i class="fas fa-cash-register mr-1"></i> POS
                        </a>
                    </form>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon stat-blue">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Total Struk</small>
                            <h4 class="mb-0 font-weight-bold text-dark">{{ $summary['sales_count'] }}</h4>
                            <span class="small text-muted">Transaksi hari ini</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon stat-green">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Penjualan Bersih</small>
                            <h4 class="mb-0 font-weight-bold text-dark">Rp {{ number_format($summary['net_sales'], 0, ',', '.') }}</h4>
                            <span class="small text-muted">Net sales hari ini</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon stat-purple">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Pembayaran Masuk</small>
                            <h4 class="mb-0 font-weight-bold text-dark">Rp {{ number_format($summary['payment_total'], 0, ',', '.') }}</h4>
                            <span class="small text-muted">Total kas diterima</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon {{ abs($summary['difference']) > 0 ? 'stat-red' : 'stat-green' }}">
                            <i class="fas fa-{{ abs($summary['difference']) > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Selisih Rekonsiliasi</small>
                            <h4 class="mb-0 font-weight-bold {{ abs($summary['difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($summary['difference'], 0, ',', '.') }}
                            </h4>
                            <span class="small {{ abs($summary['difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                {{ abs($summary['difference']) > 0 ? 'Perlu diperiksa' : 'Laporan seimbang' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Breakdown Row --}}
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="nexora-table-card h-100">
                        <div class="p-3 border-bottom d-flex align-items-center">
                            <i class="fas fa-wallet mr-2 text-primary"></i>
                            <h5 class="mb-0 font-weight-bold text-dark">Breakdown per Metode Pembayaran</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Metode Pembayaran</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-right">Total Uang Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentsByMethod as $payment)
                                        <tr>
                                            <td class="font-weight-bold text-dark">{{ $payment['method'] }}</td>
                                            <td class="text-center">
                                                <span class="nexora-badge is-info">{{ $payment['count'] }} kali</span>
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                Rp {{ number_format($payment['amount'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Belum ada pembayaran pada tanggal ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="nexora-table-card h-100">
                        <div class="p-3 border-bottom d-flex align-items-center">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            <h5 class="mb-0 font-weight-bold text-dark">Ringkasan Jam Penjualan</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Jam Transaksi</th>
                                        <th class="text-right">Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hourlySales as $hour)
                                        <tr>
                                            <td class="font-weight-bold text-dark">{{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($hour->hour + 1, 2, '0', STR_PAD_LEFT) }}:00</td>
                                            <td class="text-right font-weight-bold text-primary">
                                                Rp {{ number_format($hour->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">Belum ada aktivitas penjualan pada tanggal ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Struk Table Card --}}
            <div class="nexora-table-card">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-alt mr-2 text-primary"></i>
                        <h5 class="mb-0 font-weight-bold text-dark">Daftar Transaksi Struk ({{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }})</h5>
                    </div>
                    <span class="nexora-badge is-primary">{{ count($sales) }} transaksi</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Waktu</th>
                                <th>Pelanggan</th>
                                <th>Kasir</th>
                                <th class="text-right">Total Transaksi</th>
                                <th>Rincian Pembayaran</th>
                                <th class="text-right">Selisih</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                @php
                                    $paid = $sale->payments->sum('amount');
                                    $diff = ($sale->total ?? 0) - $paid;
                                @endphp
                                <tr>
                                    <td class="font-weight-bold text-primary">
                                        <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}">
                                            {{ $sale->invoice_number ?: ('#' . $sale->id) }}
                                        </a>
                                    </td>
                                    <td>{{ $sale->created_at->format('H:i') }}</td>
                                    <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
                                    <td>{{ optional($sale->user)->name ?? '-' }}</td>
                                    <td class="text-right font-weight-bold text-dark">
                                        Rp {{ number_format($sale->total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @forelse($sale->payments as $payment)
                                            <div class="small">
                                                <span class="font-weight-bold">{{ optional($payment->method)->name ?? 'Bayar' }}:</span>
                                                Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                            </div>
                                        @empty
                                            <span class="text-muted small">Belum ada bayar</span>
                                        @endforelse
                                    </td>
                                    <td class="text-right font-weight-bold {{ abs($diff) > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($diff, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="nexora-action-group">
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="nexora-action-btn is-info" title="Lihat Struk">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Tidak ada transaksi struk penjualan pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
