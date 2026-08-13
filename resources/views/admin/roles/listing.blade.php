@extends('admin.layout.app')

@section('title', 'Roles & Access')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-user-tag"></i> Roles & Access</h4>
                <a href="{{ url('admin-panel/roles/add') }}" class="btn btn-secondary" style="border: none;">Add New Role</a>
            </div>

            <table class="table nexora-data-table" id="categoriesTable">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Users</th>
                        <th>Access</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->role }}</td>
                            <td>{{ $role->users_count }} users</td>
                            <td>
                                <span class="badge badge-primary">{{ $role->permissions_count }} permissions</span>
                            </td>
                            <td>
                                <span class="nexora-status nexora-status-success">Active</span>
                            </td>
                            <td>
                                <div class="nexora-action-group">
                                    <a href="{{ url('admin-panel/roles/edit', $role->id) }}" class="nexora-action-btn is-warning" title="Edit role"><i class="fas fa-edit"></i></a>
                                    <a href="{{ url('admin-panel/roles/delete', $role->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete role"><i class="fas fa-trash"></i></a>
                                </div>
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
