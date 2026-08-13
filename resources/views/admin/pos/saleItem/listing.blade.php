@extends('admin.layout.app')

@section('title', 'Sale Items')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Sale Items</h4>
                <a href="{{ url('admin-panel/sale-items/add') }}" class="btn btn-secondary" style="border: none;">Add New Sale Item</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sale Status</th>
                                        <th>Product Name</th>
                                        <th>Variant Name</th>
                                        <th>Quantity</th>
                                        <th>Tax</th>
                                        <th>Discount</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ $sale->sale->status }}</td>
                                            <td>{{ $sale->product->name }}</td>
                                            <td>{{ $sale->product_variant->name }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td>{{ $sale->tax }}</td>
                                            <td>{{ $sale->discount }}</td>
                                            <td>{{ $sale->price }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/sale-items/edit', $sale->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/sale-items/delete', $sale->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
