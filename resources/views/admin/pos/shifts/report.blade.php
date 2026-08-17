@extends('admin.layout.app')

@section('title', 'Shift Report')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div>
                    <h4 class="page-title mb-1">Shift Report</h4>
                    <p class="text-muted mb-0">Laporan kas awal, kas tutup, selisih, dan total penjualan per kasir.</p>
                </div>
                <form method="GET" action="{{ url('admin-panel/pos/shifts/report') }}" class="d-flex align-items-center" style="gap: 8px;">
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a href="{{ url('admin-panel/pos/sell') }}" class="btn btn-secondary">POS</a>
                </form>
            </div>

            @if($migrationMissing)
                <div class="alert alert-warning">
                    Migration shift belum dijalankan. Jalankan <code>php artisan migrate</code> supaya laporan shift aktif.
                </div>
            @endif

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-category mb-1">Total Shift</p>
                            <h4 class="card-title">{{ $summary['total_shift'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-category mb-1">Open</p>
                            <h4 class="card-title text-primary">{{ $summary['open_shift'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-category mb-1">Sales Total</p>
                            <h4 class="card-title">Rp {{ number_format($summary['sales_total'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-category mb-1">Cash Difference</p>
                            <h4 class="card-title {{ abs($summary['cash_difference']) > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($summary['cash_difference'], 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Daftar Shift</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Kasir</th>
                                    <th>Store</th>
                                    <th>Shift</th>
                                    <th>Status</th>
                                    <th>Open</th>
                                    <th>Close</th>
                                    <th>Kas Awal</th>
                                    <th>Kas Tutup</th>
                                    <th>Expected</th>
                                    <th>Selisih</th>
                                    <th>Sales</th>
                                    <th>Total</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift->user?->name ?? '-' }}</td>
                                        <td>{{ $shift->store?->name ?? '-' }}</td>
                                        <td>{{ $shift->shift_name ?: '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $shift->status === 'open' ? 'primary' : 'success' }}">
                                                {{ ucfirst($shift->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $shift->opened_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td>{{ $shift->closed_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td>Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                                        <td>{{ $shift->closing_cash === null ? '-' : 'Rp '.number_format($shift->closing_cash, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</td>
                                        <td class="{{ abs($shift->cash_difference) > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($shift->cash_difference, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $shift->sales_count }}</td>
                                        <td>Rp {{ number_format($shift->sales_total, 0, ',', '.') }}</td>
                                        <td>{{ $shift->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted">Belum ada data shift.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($shifts, 'links'))
                        <div class="mt-3">{{ $shifts->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
