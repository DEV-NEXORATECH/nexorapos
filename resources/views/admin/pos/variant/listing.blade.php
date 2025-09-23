@extends('admin.layout.app')

@section('title', 'Product Variant')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Product Variant</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Product Variant</div>
                            <div>
                                <a href="{{ url('admin-panel/variants/add') }}" class="btn btn-primary">Add New Product Variant</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
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
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
