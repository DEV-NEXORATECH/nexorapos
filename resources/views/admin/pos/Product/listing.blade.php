@extends('admin.layout.app')

@section('title', 'Add New Product')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4" style="align-items: center;">
                <h4 class="mt-1 mb-2" style="color: #fff;"><i class="fas fa-box"></i> Products</h4>
                <div class="d-flex" style="gap: 10px;">
                    <a href="{{ url('admin-panel/products/add') }}" class="btn" style="background-color: #FFFFFF !important; color: #0058A6 !important; border: none !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <a href="{{ url('admin-panel/products/recycle') }}" class="btn btn-danger" style="white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        <i class="fa-solid fa-recycle"></i> Recycle Bin
                    </a>
                    <a href="{{ url('admin-panel/products/export') }}" class="btn btn-success" style="white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        <i class="fas fa-file-export"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" id="importModal" style="white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        <i class="fas fa-file-import"></i> Import Excel
                    </button>
                    <!-- Hidden file input -->
                    <input type="file" id="fileInput" style="display:none" accept=".xlsx,.xls,.csv">
                </div>
            </div>

            <table class="table nexora-data-table" id="productsTable" style="width:100%">
                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Variants</th>
                                        <th>Stock</th>
                                        <th class="text-right">Cost</th>
                                        <th class="text-right">Selling Price</th>
                                        <th>Tax</th>
                                        <th>Track Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ optional($product->category)->name ?? '-' }}</td>
                                        <td>{{ $product->product_variants->count() }}</td>
                                        <td>
                                            @if($product->track_stock)
                                                <span class="badge {{ $product->stock_quantity <= ($product->minimum_stock ?? 0) ? 'badge-danger' : 'badge-success' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                                @if($product->minimum_stock > 0)
                                                <small class="text-muted">(min: {{ $product->minimum_stock }})</small>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($product->cost, 2) }}</td>
                                        <td class="text-right">{{ number_format($product->price, 2) }}</td>
                                        <td>{{ optional($product->tax)->name ?? '-' }} {{ $product->tax ? '('.$product->tax->rate.'%)' : '' }}</td>
                                        <td>{{ $product->track_stock ? 'Yes' : 'No' }}</td>
                                        <td style="white-space:nowrap;">
                                            <a href="{{ url('admin-panel/products/show', $product->id) }}" class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></a>
                                            <a href="{{ url('admin-panel/products/edit', $product->id) }}" class="btn btn-sm btn-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="{{ url('admin-panel/products/delete', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus/arsipkan produk ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-box-open fa-2x mb-2" style="color:var(--color-blue-500)"></i>
                                            <p>Belum ada produk.</p>
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

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script src="{{ asset('admin/custom/product.js') }}"></script>
@endsection
