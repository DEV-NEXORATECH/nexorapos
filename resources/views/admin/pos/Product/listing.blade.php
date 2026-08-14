@extends('admin.layout.app')

@section('title', 'Add New Product')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-box"></i> Products</h4>
                <div class="d-flex" style="gap: 10px;">
                    <a href="{{ url('admin-panel/products/add') }}" class="btn btn-secondary" style="border: none;">
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
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX loaded by DataTables -->
                                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.nexora-data-table')) {
            $('.nexora-data-table').DataTable().destroy();
        }
        $('.nexora-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/products/listing") }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'sku', name: 'sku' },
                { data: 'category', name: 'category.name' , orderable: false, searchable: false },
                { data: 'variants', name: 'variants', searchable: false },
                { data: 'stock', name: 'stock_quantity' },
                { data: 'cost', name: 'purchase_price' },
                { data: 'price', name: 'price' },
                { data: 'tax', name: 'tax.name' , orderable: false, searchable: false },
                { data: 'track_stock', name: 'track_stock' },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'user.name' , orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
