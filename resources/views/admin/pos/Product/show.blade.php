@extends('admin.layout.app')

@section('title', 'Product Detail — ' . $product->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Product Detail</h4>
                <div>
                    <a href="{{ url('admin-panel/products/listing') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="{{ url('admin-panel/products/edit', $product->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                </div>
            </div>

            <div class="row">
                {{-- Product Information --}}
                <div class="col-md-5">
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Product Information</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td style="width:40%"><strong>Name</strong></td><td>{{ $product->name }}</td></tr>
                                <tr><td><strong>SKU</strong></td><td>{{ $product->sku ?? '-' }}</td></tr>
                                <tr><td><strong>Barcode</strong></td><td>{{ $product->barcode ?? '-' }}</td></tr>
                                <tr><td><strong>Category</strong></td><td>{{ optional($product->category)->name ?? '-' }}</td></tr>
                                <tr><td><strong>Tax</strong></td><td>{{ $product->tax ? $product->tax->name . ' (' . $product->tax->rate . '%)' : '-' }}</td></tr>
                                <tr><td><strong>Description</strong></td><td>{{ $product->description ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Pricing & Stock</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td style="width:40%"><strong>Cost</strong></td><td>{{ number_format($product->cost, 2) }}</td></tr>
                                <tr><td><strong>Selling Price</strong></td><td>{{ number_format($product->price, 2) }}</td></tr>
                                <tr>
                                    <td><strong>Margin</strong></td>
                                    <td>
                                        @php $margin = $product->cost > 0 ? (($product->price - $product->cost) / $product->cost) * 100 : 0; @endphp
                                        {{ number_format($margin, 1) }}%
                                    </td>
                                </tr>
                                <tr><td><strong>Track Stock</strong></td><td>{{ $product->track_stock ? 'Yes' : 'No' }}</td></tr>
                                @if($product->track_stock)
                                <tr>
                                    <td><strong>Current Stock</strong></td>
                                    <td>
                                        <span class="badge {{ $product->stock_quantity <= ($product->minimum_stock ?? 0) ? 'badge-danger' : 'badge-success' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                </tr>
                                <tr><td><strong>Minimum Stock</strong></td><td>{{ $product->minimum_stock ?? 0 }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Audit --}}
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Audit</div></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td style="width:40%"><strong>Created</strong></td><td>{{ $product->created_at?->format('d M Y H:i') }}</td></tr>
                                <tr><td><strong>Updated</strong></td><td>{{ $product->updated_at?->format('d M Y H:i') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Variants & History --}}
                <div class="col-md-7">
                    {{-- Product Variants --}}
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Variants ({{ $product->product_variants->count() }})</div></div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Variant</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th class="text-right">Cost</th>
                                        <th class="text-right">Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->product_variants as $variant)
                                    <tr>
                                        <td><strong>{{ $variant->name }}</strong></td>
                                        <td>{{ $variant->sku ?? '-' }}</td>
                                        <td>{{ $variant->barcode ?? '-' }}</td>
                                        <td class="text-right">{{ number_format($variant->cost, 2) }}</td>
                                        <td class="text-right">{{ number_format($variant->price, 2) }}</td>
                                        <td>{{ $variant->stock_quantity ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-3">Tidak ada variant.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Stock Movements --}}
                    @if($product->track_stock && isset($stockMovements))
                    <div class="card mb-3">
                        <div class="card-header"><div class="card-title">Stock Movements (Last 20)</div></div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockMovements as $sm)
                                    <tr>
                                        <td>{{ $sm->created_at?->format('d M Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $sm->type === 'in' ? 'badge-success' : 'badge-danger' }}">
                                                {{ strtoupper($sm->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $sm->quantity }}</td>
                                        <td>{{ $sm->reference ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-3">Belum ada stock movement.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
