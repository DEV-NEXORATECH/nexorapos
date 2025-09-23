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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Products</div>
                           <div>
                                {{-- Add New Product --}}
                                <a href="{{ url('admin-panel/products/add') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Product
                                </a>

                                <a href="{{ url('admin-panel/products/recycle') }}" class="btn btn-danger">
                                    <i class="fa-solid fa-recycle"></i> Recycle Bin
                                </a>
                                {{-- Export Excel --}}
                                <a href="{{ url('admin-panel/products/export') }}" class="btn btn-success">
                                    <i class="fas fa-file-export"></i> Export Excel
                                </a>

                                {{-- Import Excel --}}
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" id="importModal">
                                    <i class="fas fa-file-import"></i> Import Excel
                                </button>
                                <!-- Hidden file input -->
                                <input type="file" id="fileInput" style="display:none" accept=".xlsx,.xls,.csv">
                            </div>

                        </div>

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
                                    @foreach ($products as $product)
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
                                            <a href="{{ url('admin-panel/products/edit', $product->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/products/delete', $product->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
