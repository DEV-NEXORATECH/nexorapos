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
                        @if(auth()->user() && auth()->user()->isSuperAdmin())
                            <th>Company</th>
                        @endif
                        <th>License</th>
                        <th>Status</th>
                        <th class="min-w-120 text-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        @php
                            $company = $user->company;
                            $license = $company && ! $user->isSuperAdmin() ? $company->licenseSnapshot() : null;
                            $initial = strtoupper(substr($user->name ?: $user->email, 0, 1));
                        @endphp
                        <tr>
                            <td>
                                <div class="nexora-table-user">
                                    @if($user->profile_image)
                                        <img src="{{ asset('admin/profile_images/'. $user->profile_image) }}" alt="{{ $user->name }}">
                                    @else
                                        <span>{{ $initial }}</span>
                                    @endif
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <small>ID #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="nexora-status nexora-status-info">{{ $user->role?->role ?? '-' }}</span>
                            </td>
                            
                            @if(auth()->user() && auth()->user()->isSuperAdmin())
                                <td>
                                    <strong>{{ $company?->name ?? 'Platform' }}</strong>
                                    <small class="d-block text-muted">{{ $company?->company_code ?? 'SUPER ADMIN' }}</small>
                                </td>
                            @endif

                            <td>
                                @if($license)
                                    <span class="badge badge-{{ $license['badge_class'] }}">{{ $license['label'] }}</span>
                                @else
                                    <span class="badge badge-info">Platform</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $user->isSuperAdmin() ? 'badge-success' : 'badge-info' }}">
                                    {{ $user->isSuperAdmin() ? 'Super Admin' : 'Active' }}
                                </span>
                            </td>
                            <td>
                                <div class="nexora-action-group">
                                    <a href="{{ url('admin-panel/users/edit',$user->id) }}" class="nexora-action-btn is-warning" title="Edit user"><i class="fas fa-edit"></i></a>
                                    <a href="{{ url('admin-panel/users/delete', $user->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete user"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Use retrieve: true to prevent reinitialization warning
        var table = $('#categoriesTable').DataTable({ retrieve: true });
        
        $('#superAdminCompanyFilter').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            // Filter by the Company column (Index 3 if Super Admin)
            table.column(3).search(val ? val : '', true, false).draw();
        });
    });
</script>

@endsection
