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
                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                                    <!-- AJAX loaded by DataTables -->
                                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.nexora-data-table')) {
            $('.nexora-data-table').DataTable().destroy();
        }
        $('.nexora-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/roles/listing") }}',
            columns: [
                { data: 'role', name: 'role' },
                { data: 'users', name: 'users', orderable: false, searchable: false },
                { data: 'access', name: 'access', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
