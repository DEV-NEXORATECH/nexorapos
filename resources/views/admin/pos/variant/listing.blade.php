@extends('admin.layout.app')

@section('title', 'Product Variant')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-layer-group"></i> Product Variants</h4>
                <a href="{{ url('admin-panel/variants/add') }}" class="btn btn-secondary" style="border: none;">Add New Variant
                </a>
            </div>

            <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Variant Name</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th>Price</th>
                                        <th>Cost</th>
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $v)
                                        <tr>
                                            <td>{{ $v->id }}</td>
                                            <td>{{ $v->product->name }}</td>
                                            <td>{{ $v->name }}</td>
                                            <td>{{ $v->sku }}</td>
                                            <td>{{ $v->barcode }}</td>
                                            <td>{{ $v->price }}</td>
                                            <td>{{ $v->cost }}</td>
                                            <td class="text-nowrap">{{ $v->company?->name ?? 'System' }}</td>
                                            <td class="text-nowrap">{{ $v->user?->name ?? $v->creator?->name ?? '-' }}</td>
                                            <td class="text-nowrap">{{ $v->created_at ? $v->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/variants/edit', $v->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/variants/delete', $v->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
