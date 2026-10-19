@extends('admin.layout.app')

@section('title', 'Daily Reconciliation')

@section('content')
<div class="main-panel reconciliation-page">
    <div class="content">
        <div class="container-fluid">
            <div class="reconciliation-page-header">
                <div class="reconciliation-page-heading">
                    <span class="reconciliation-eyebrow"><i class="la la-balance-scale"></i> Finance & Reports</span>
                    <h4 class="page-title mb-1">Daily Reconciliation</h4>
                    <p class="text-muted mb-0">Cocokkan total struk dengan uang masuk per metode pembayaran.</p>
                </div>
                <form method="GET" action="{{ url('admin-panel/sales/reconciliation') }}" class="reconciliation-filter-form">
                    <label for="reconciliation-date">Tanggal laporan</label>
                    <div class="reconciliation-filter-controls">
                        <input id="reconciliation-date" type="date" name="date" value="{{ $date }}" class="form-control">
                        <button class="btn btn-primary" type="submit"><i class="la la-filter"></i> Filter</button>
                        <button class="btn btn-secondary print" type="button"><i class="la la-print"></i> Print</button>
                    </div>
                </form>
            </div>

            <div id="invoice">
                <div class="reconciliation-stat-grid">
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon"><i class="la la-file-text"></i></div>
                        <div><small>Total Struk</small><strong>{{ $summary['sales_count'] }}</strong><span>Transaksi hari ini</span></div>
                    </div>
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon"><i class="la la-line-chart"></i></div>
                        <div><small>Net Sales</small><strong>Rp {{ number_format($summary['net_sales'], 0, ',', '.') }}</strong><span>Total penjualan bersih</span></div>
                    </div>
                    <div class="reconciliation-stat-card">
                        <div class="reconciliation-stat-icon"><i class="la la-money"></i></div>
                        <div><small>Payment Masuk</small><strong>Rp {{ number_format($summary['payment_total'], 0, ',', '.') }}</strong><span>Total pembayaran diterima</span></div>
                    </div>
                    <div class="reconciliation-stat-card {{ abs($summary['difference']) > 0 ? 'has-difference' : 'is-balanced' }}">
                        <div class="reconciliation-stat-icon"><i class="la la-{{ abs($summary['difference']) > 0 ? 'warning' : 'check-circle' }}"></i></div>
                        <div><small>Selisih</small><strong>Rp {{ number_format($summary['difference'], 0, ',', '.') }}</strong><span>{{ abs($summary['difference']) > 0 ? 'Perlu diperiksa' : 'Laporan seimbang' }}</span></div>
                    </div>
                </div>

                <div class="reconciliation-panels">
                    <div class="card reconciliation-panel-card">
                        <div class="card-header">
                            <div><span class="reconciliation-panel-eyebrow">Payment overview</span><div class="card-title">Breakdown Pembayaran</div></div>
                        </div>
                        <div class="card-body">
                            <div class="reconciliation-table-wrap">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Metode</th>
                                            <th>Transaksi</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paymentsByMethod as $payment)
                                            <tr>
                                                <td>{{ $payment['method'] }}</td>
                                                <td>{{ $payment['count'] }}</td>
                                                <td>{{ number_format($payment['amount'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada pembayaran pada tanggal ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card reconciliation-panel-card">
                        <div class="card-header">
                            <div><span class="reconciliation-panel-eyebrow">Sales activity</span><div class="card-title">Ringkasan Jam Ramai</div></div>
                        </div>
                        <div class="card-body">
                            <div class="reconciliation-table-wrap">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Jam</th>
                                            <th>Total Penjualan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($hourlySales as $hour)
                                            <tr>
                                                <td>{{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                                                <td>{{ number_format($hour->total, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">Belum ada penjualan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card reconciliation-sales-card">
                    <div class="card-header">
                        <div>
                            <span class="reconciliation-panel-eyebrow">Transaction detail</span>
                            <div class="card-title">Daftar Struk {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="reconciliation-table-wrap">
                        <table class="table table-bordered" id="reconciliationTable">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Waktu</th>
                                    <th>Customer</th>
                                    <th>Kasir</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Selisih</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    @php
                                        $paid = $sale->payments->sum('amount');
                                        $diff = ($sale->total ?? 0) - $paid;
                                    @endphp
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->created_at->format('H:i') }}</td>
                                        <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
                                        <td>{{ optional($sale->user)->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($sale->total ?? 0, 2) }}</td>
                                        <td>
                                            @forelse($sale->payments as $payment)
                                                {{ optional($payment->method)->name ?? 'Payment' }}: {{ number_format($payment->amount ?? 0, 2) }}<br>
                                            @empty
                                                <span class="text-muted">No payment</span>
                                            @endforelse
                                        </td>
                                        <td class="{{ abs($diff) > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($diff, 2) }}</td>
                                        <td>
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" target="_blank" class="btn-sm btn-primary">Struk</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
@endpush
