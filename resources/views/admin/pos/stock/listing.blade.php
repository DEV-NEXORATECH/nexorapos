@extends('admin.layout.app')

@section('title', 'Stock Movement')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title"> Stock</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title"> Stock</div>
                            <div>
                                <a href="{{ url('admin-panel/stock/add') }}" class="btn btn-primary">Move Stock</a>
                            </div>
                        </div>

                        <div class="card-body">

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
                                                <a href="{{ url('admin-panel/stock/edit', $stock->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/stock/delete', $stock->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
