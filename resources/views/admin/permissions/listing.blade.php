@extends('admin.layout.app')

@section('title', 'Permissions')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Permissions</h4>
                <a href="{{ url('admin-panel/permissions/add') }}" class="btn btn-secondary" style="border: none;">Add New Permission</a>
            </div>

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
                                            <a href="{{ url('admin-panel/permissions/edit', $permission->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/permissions/delete', $permission->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
