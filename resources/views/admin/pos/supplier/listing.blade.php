@extends('admin.layout.app')

@section('title', 'Suppliers')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-truck"></i> Suppliers</h4>
                <a href="{{ url('admin-panel/supplier/add') }}" class="btn btn-secondary" style="border: none;">Add New Supplier</a>
            </div>

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
                                            <a href="{{ url('admin-panel/supplier/edit', $sup->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/supplier/delete', $sup->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
