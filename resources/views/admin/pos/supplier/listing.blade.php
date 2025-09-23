@extends('admin.layout.app')

@section('title', 'Suppliers')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Suppliers</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Suppliers</div>
                            <div>
                                <a href="{{ url('admin-panel/supplier/add') }}" class="btn btn-primary">Add New Supplier</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $sup)
                                    <tr>
                                         <td>{{ $sup->id }}</td>
                                        <td>{{ $sup->name }}</td>
                                        <td>{{ $sup->email }}</td>
                                        <td>{{ $sup->address }}</td>
                                        <td>{{ $sup->phone }}</td>
                                        <td>
                                            <a href="{{ url('admin-panel/supplier/edit', $sup->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/supplier/delete', $sup->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
