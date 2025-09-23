@extends('admin.layout.app')

@section('title', 'Add New Product')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Products</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                       
                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Tax_Rate</th>
                                        <th>Price</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th>Cost</th>
                                        <th>Track_Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deletedProducts as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->tax->rate }}</td>
                                        <td>{{ $total=$product->price * $product->tax->rate/100 +$product->price}}</td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->barcode }}</td>
                                        <td>{{ $product->cost }}</td>
                                        <td>{{ $product->track_stock }}</td>
                                        <td>
                                            <a href="{{ url('admin-panel/products/restore', $product->id) }}" class="btn-sm btn-info restore-category" title="Restore Product"><i class="fa-solid fa-trash-can-arrow-up"></i></a>
                                            <a href="{{ url('admin-panel/products/force-delete', $product->id) }}" class="btn-sm btn-danger force-delete" title="Delete Permanently"><i class="fas fa-trash"></i></a>
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
<script src="{{ asset('admin/custom/product.js') }}"></script>
@endsection
