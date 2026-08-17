@extends('admin.layout.app')

@section('title', 'Users')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-users"></i> Users</h4>
                <div class="d-flex align-items-center">
                    @if(auth()->user() && auth()->user()->isSuperAdmin())
                        @php
                            $allCompanies = \App\Models\Company::orderBy('name')->get();
                        @endphp
                        <select class="form-control mr-3" id="superAdminCompanyFilter" style="min-width: 200px;">
                            <option value="">All Companies</option>
                            @foreach($allCompanies as $c)
                                <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->company_code }})</option>
                            @endforeach
                        </select>
                    @endif
                    <a href="{{ url('admin-panel/users/add') }}" class="btn btn-secondary" style="border: none;">Add New User</a>
                </div>
            </div>

            <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Store</th>
                        <th>Shift</th>
                        <th>License</th>
                        <th>Status</th>
                        <th class="min-w-120 text-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                                    <!-- AJAX loaded by DataTables -->
                                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .min-w-120 {
        min-width: 120px;
    }
    .nexora-table-wrapper { background: #fff; border: 1px solid #DCEEFF; border-radius: 12px; overflow: hidden; padding: 16px; }
</style>

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
            ajax: '{{ url("admin-panel/users/listing") }}',
            columns: [
                { data: 'nama', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role.name' , orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'store', name: 'store.name' , orderable: false, searchable: false },
                { data: 'shift', name: 'shift' , orderable: false, searchable: false },
                { data: 'license', name: 'license' },
                { data: 'status', name: 'status' , orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
