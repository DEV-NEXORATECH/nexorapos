@extends('admin.layout.app')

@section('title', 'Role Permission')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Role_Permissions_Pivot</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Role_Permissions_Pivot</div>
                            <div>
                                <a href="{{ url('admin-panel/role-permissions/add') }}" class="btn btn-primary">Assign Permissions</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Permissions</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role )
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->role }}</td>
                                            <td>{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/role-permissions/edit', $role->id) }}" class="btn btn-success"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/role-permissions/delete', $role->id) }}" class="btn btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
