@extends('admin.layout.app')

@section('title', 'Sale Items')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Sale Items</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Sale Items</div>
                            <div>
                                <a href="{{ url('admin-panel/sale-items/add') }}" class="btn btn-primary">Add New Sale Item</a>
                            </div>
                        </div>

                        <div class="card-body">

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
                                                <a href="{{ url('admin-panel/sale-items/edit', $sale->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/sale-items/delete', $sale->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
