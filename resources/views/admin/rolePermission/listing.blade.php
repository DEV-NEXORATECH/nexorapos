@extends('admin.layout.app')

@section('title', 'Role Permission')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Role_Permissions_Pivot</h4>
                <a href="{{ url('admin-panel/role-permissions/add') }}" class="btn btn-secondary" style="border: none;">Assign Permissions</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Permissions</th>
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role )
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->role }}</td>
                                            <td>{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                                            <td class="text-nowrap">{{ $role->company?->name ?? 'System' }}</td>
                                            <td class="text-nowrap">{{ $role->user?->name ?? $role->creator?->name ?? '-' }}</td>
                                            <td class="text-nowrap">{{ $role->created_at ? $role->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/role-permissions/edit', $role->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/role-permissions/delete', $role->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
