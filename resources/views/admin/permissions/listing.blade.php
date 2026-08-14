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
            ajax: '{{ url("admin-panel/permissions/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'permission_category', name: 'permission_category' },
                { data: 'name', name: 'name' },
                { data: 'route', name: 'route' },
                { data: 'is_show', name: 'is_show' },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
