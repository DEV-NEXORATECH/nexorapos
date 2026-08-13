@extends('admin.layout.app')

@section('title', 'Sales')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Sales</h4>
                <a href="{{ url('admin-panel/pos/sell') }}" class="btn btn-secondary" style="border: none;">Add New Sale</a>
            </div>
                            <table class="table table-bordered" id="salesTable">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Store</th>
                                        <th>Customer</th>
                                        <th>Cashier</th>
                                        <th>Items</th>
                                        <th>Subtotal</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Grand Total</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                        <th>Sale Status</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sales as $sale)
                                        <tr>
                                            <td><strong>{{ $sale->invoice_number ?? '#'.$sale->id }}</strong></td>
                                            <td>{{ $sale->created_at?->format('d M Y H:i') }}</td>
                                            <td>{{ optional($sale->store)->name ?? '-' }}</td>
                                            <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
                                            <td>{{ optional($sale->user)->name ?? '-' }}</td>
                                            <td>{{ $sale->sale_items_count ?? $sale->sale_items->count() }}</td>
                                            <td class="text-right">{{ number_format($sale->subtotal, 2) }}</td>
                                            <td class="text-right">{{ number_format($sale->discount, 2) }}</td>
                                            <td class="text-right">{{ number_format($sale->tax, 2) }}</td>
                                            <td class="text-right"><strong>{{ number_format($sale->total, 2) }}</strong></td>
                                            <td class="text-right">{{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($sale->change_amount ?? 0, 2) }}</td>
                                            <td>
                                                @forelse($sale->payments as $payment)
                                                    <span class="badge badge-primary">{{ optional($payment->method)->name ?? '-' }}</span>
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @php
                                                    $psBadge = match($sale->payment_status ?? 'UNPAID') {
                                                        'PAID' => 'badge-success',
                                                        'PARTIAL' => 'badge-warning',
                                                        'REFUNDED' => 'badge-info',
                                                        default => 'badge-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $psBadge }}">{{ $sale->payment_status ?? 'UNPAID' }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $ssBadge = match($sale->status) {
                                                        'completed' => 'badge-success',
                                                        'cancelled', 'void' => 'badge-danger',
                                                        default => 'badge-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $ssBadge }}">{{ ucfirst($sale->status) }}</span>
                                            </td>
                                            <td>{{ $sale->created_at?->format('d/m/Y') }}</td>
                                            <td style="white-space:nowrap;">
                                                <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="btn btn-sm btn-primary" title="View Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ url('admin-panel/pos/pdf', $sale->id) }}" class="btn btn-sm btn-secondary" title="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if(!in_array($sale->status, ['cancelled', 'void']))
                                                <form action="{{ url('admin-panel/sales/delete', $sale->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin membatalkan sale ini? Stok akan dikembalikan.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancel Sale">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="17" class="text-center py-4">
                                                <i class="fas fa-receipt fa-2x mb-2" style="color:var(--color-blue-500)"></i>
                                                <p>Belum ada transaksi.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script>
    // Optional: Add confirmation for delete
    document.querySelectorAll('.delete-sale').forEach(function(el){
        el.addEventListener('click', function(e){
            if(!confirm('Are you sure you want to delete this sale?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
