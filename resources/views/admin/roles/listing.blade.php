@extends('admin.layout.app')

@section('title', 'Roles & Access')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Roles & Access</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <div class="card-title mb-1">Roles & Access</div>
                                <p class="card-category mb-0">Kelola role lalu buka edit untuk checklist permission akses menu.</p>
                            </div>
                            <div>
                                <a href="{{ url('admin-panel/roles/add') }}" class="btn btn-primary">Add New Role</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
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
                                                    <a href="{{ url('admin-panel/roles/edit', $role->id) }}" class="nexora-action-btn" title="Edit role"><i class="fas fa-edit"></i></a>
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
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
