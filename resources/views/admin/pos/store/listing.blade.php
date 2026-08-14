@extends('admin.layout.app')

@section('title', 'All Stores')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-store"></i> All Stores</h4>
                <a href="{{ url('admin-panel/stores/add') }}" class="btn btn-secondary" style="border: none;">Add New Store
                </a>
            </div>

            <table class="table nexora-data-table" id="storesTable" style="width:100%">
                <thead>
                                    <tr>
                                        <th>Store Code</th>
                                        <th>Store Name</th>
                                        <th>Created At</th>
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

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#storesTable')) {
            $('#storesTable').DataTable().destroy();
        }
        $('#storesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url("admin-panel/stores/listing") }}',
            columns: [
                { data: 'code', name: 'code' },
                { data: 'name', name: 'name' },
                { data: 'created_at_short', name: 'created_at', searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
