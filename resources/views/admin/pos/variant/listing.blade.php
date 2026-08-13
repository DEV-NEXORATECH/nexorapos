@extends('admin.layout.app')

@section('title', 'Product Variant')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4" style="align-items: center;">
                <h4 class="mt-1 mb-2" style="color: #fff;"><i class="fas fa-layer-group"></i> Product Variants</h4>
                <a href="{{ url('admin-panel/variants/add') }}" class="btn" style="background-color: #FFFFFF !important; color: #0058A6 !important; border: none !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                    + Add New Variant
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
                                        <th>Action</th>
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
                                            <td>
                                                <a href="{{ url('admin-panel/variants/edit', $v->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/variants/delete', $v->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
