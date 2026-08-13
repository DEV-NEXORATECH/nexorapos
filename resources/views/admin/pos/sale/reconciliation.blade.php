@extends('admin.layout.app')

@section('title', 'Daily Reconciliation')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div>
                    <h4 class="page-title mb-1">Daily Reconciliation</h4>
                    <p class="text-muted mb-0">Cocokkan total struk dengan uang masuk per metode pembayaran.</p>
                </div>
                <form method="GET" action="{{ url('admin-panel/sales/reconciliation') }}" class="d-flex align-items-center" style="gap: 8px;">
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <button class="btn btn-secondary print" type="button">Print</button>
                </form>
            </div>

            <div id="invoice">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-category mb-1">Total Struk</p>
                                <h4 class="card-title">{{ $summary['sales_count'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-category mb-1">Net Sales</p>
                                <h4 class="card-title">{{ number_format($summary['net_sales'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-category mb-1">Payment Masuk</p>
                                <h4 class="card-title">{{ number_format($summary['payment_total'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-category mb-1">Selisih</p>
                                <h4 class="card-title {{ abs($summary['difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($summary['difference'], 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Breakdown Pembayaran</div>
                            </div>
                            <div class="card-body">
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

                    <div class="col-md-7 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Ringkasan Jam Ramai</div>
                            </div>
                            <div class="card-body">
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Daftar Struk {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="reconciliationTable">
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
@endsection

@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
@endpush
