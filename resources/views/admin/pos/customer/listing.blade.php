@extends('admin.layout.app')

@section('title', 'Customers')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Customers</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Customers</div>
                            <div>
                                <a href="{{ url('admin-panel/customer/add') }}" class="btn btn-primary">Add New Customer</a>
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
                                    @foreach ($customers as $cus)
                                        <td>{{ $cus->id }}</td>
                                        <td>{{ $cus->name }}</td>
                                        <td>{{ $cus->email }}</td>
                                        <td>{{ $cus->address }}</td>
                                        <td>{{ $cus->phone }}</td>
                                        <td>
                                            <a href="{{ url('admin-panel/customer/edit', $cus->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/customer/delete', $cus->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
                                        </td>
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
