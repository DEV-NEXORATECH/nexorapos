@extends('admin.layout.app')

@section('title', 'Purchase Detail — ' . ($purchase->purchase_number ?? 'PUR-'.$purchase->id))

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Purchase Detail</h4>
                <div>
                    <a href="{{ url('admin-panel/purchases') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    @if($purchase->status !== 'RECEIVED' && $purchase->status !== 'CANCELLED')
                        <a href="{{ url('admin-panel/purchase/edit', $purchase->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                    @endif
                    <button class="btn btn-info print"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <div id="invoice">
            {{-- Purchase Header Card --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Purchase Information</div></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Purchase Number</small>
                            <div><strong>{{ $purchase->purchase_number ?? 'PUR-'.$purchase->id }}</strong></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Transaction Date</small>
                            <div>{{ $purchase->created_at?->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <div>
                                @php
                                    $stBadge = match($purchase->status) {
                                        'RECEIVED' => 'badge-success',
                                        'PENDING' => 'badge-warning',
                                        'CANCELLED' => 'badge-danger',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $stBadge }}">{{ $purchase->status }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Reference</small>
                            <div>{{ $purchase->reference ?? '-' }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Supplier</small>
                            <div><strong>{{ optional($purchase->supplier)->name ?? '-' }}</strong></div>
                            @if($purchase->supplier)
                                <small>{{ $purchase->supplier->email ?? '' }} {{ $purchase->supplier->phone ? '| '.$purchase->supplier->phone : '' }}</small><br>
                                <small>{{ $purchase->supplier->address ?? '' }}</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Store</small>
                            <div>{{ optional($purchase->store)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Notes</small>
                            <div>{{ $purchase->notes ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Items ({{ $purchase->purchase_items ? $purchase->purchase_items->count() : 0 }})</div></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($purchase->purchase_items)
                                @foreach($purchase->purchase_items as $i => $it)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ optional($it->product)->name ?? '-' }}</td>
                                        <td>{{ optional($it->product_variant)->name ?? '-' }}</td>
                                        <td>{{ optional($it->product)->sku ?? '-' }}</td>
                                        <td class="text-right">{{ $it->quantity }}</td>
                                        <td class="text-right">{{ number_format($it->unit_cost ?? 0, 2) }}</td>
                                        <td class="text-right">{{ number_format($it->subtotal ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada item dalam purchase ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary --}}
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Summary</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr style="border-top:2px solid var(--color-blue-200);">
                                    <td><strong>Total Amount</strong></td>
                                    <td class="text-right"><strong>{{ number_format($purchase->total_amount ?? 0, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit --}}
            <div class="card mb-3">
                <div class="card-header"><div class="card-title">Audit</div></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Created By</small>
                            <div>{{ optional($purchase->user)->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Created At</small>
                            <div>{{ $purchase->created_at?->format('d M Y H:i:s') }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Updated At</small>
                            <div>{{ $purchase->updated_at?->format('d M Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            </div> {{-- end #invoice --}}

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.querySelector('.print').addEventListener('click', function(e) {
        e.preventDefault();
        var printContents = document.getElementById('invoice').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    });
</script>
@endpush
