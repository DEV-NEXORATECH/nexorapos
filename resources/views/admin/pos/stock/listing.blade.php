@extends('admin.layout.app')

@section('title', 'Stock Movement')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i>  Stock</h4>
                <a href="{{ url('admin-panel/stock/add') }}" class="btn btn-secondary" style="border: none;">Move Stock</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Store Name</th>
                                        <th>Product Name</th>
                                        <th>Variant Name</th>
                                        <th>Quantity</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stocks as $stock)
                                        <tr>
                                            <td>{{ $stock->id }}</td>
                                            <td>{{ $stock->store->name }}</td>
                                            <td>{{ $stock->product->name }}</td>
                                            <td>{{ $stock->product_variant->name }}</td>
                                            <td>{{ $stock->quantity }}</td>
                                            <td>{{ $stock->type }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/stock/edit', $stock->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/stock/delete', $stock->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
