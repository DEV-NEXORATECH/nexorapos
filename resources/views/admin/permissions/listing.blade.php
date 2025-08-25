@extends('admin.layout.app')

@section('title', 'Permissions')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Permissions</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Permissions</div>
                            <div>
                                <a href="{{ url('admin-panel/permissions/add') }}" class="btn btn-primary">Add New Permission</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Permission_Category</th>
                                        <th>Name</th>
                                        <th>Route</th>
                                        <th>Is_Show</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                       <td>{{ $permission->permission_category->name ?? 'No Category' }}</td>

                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->route }}</td>
                                        <td>@if($permission->is_show == '1')
                                            <p>Yes</p>
                                            @else
                                            <p>Null</p>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('admin-panel/permissions/edit', $permission->id) }}" class="btn btn-success"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/permissions/delete', $permission->id) }}" class="btn btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
