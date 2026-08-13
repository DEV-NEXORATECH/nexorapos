@extends('admin.layout.app')

@section('title', 'Companies')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Companies</h4>
                <a href="{{ url('admin-panel/companies/add') }}" class="btn btn-secondary" style="border: none;">Add New Company</a>
            </div>
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <table class="table nexora-data-table" id="companiesTable">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Company Code</th>
                                        <th>License</th>
                                        <th>Users</th>
                                        <th>Features</th>
                                        <th>Status</th>
                                        <th class="min-w-120 text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $company)
                                        @php
                                            $license = $company->licenseSnapshot();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="nexora-table-user">
                                                    @if($company->logo)
                                                        <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}">
                                                    @else
                                                        <span>{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $company->name }}</strong>
                                                        <small>{{ $company->legal_name ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $company->company_code }}</code>
                                            </td>
                                            <td>
                                                <span class="nexora-status nexora-status-{{ $license['badge_class'] }}">
                                                    {{ $license['label'] }}
                                                </span>
                                                <small class="d-block text-muted">Due: {{ $company->payment_due_at ? $company->payment_due_at->format('Y-m-d') : '-' }}</small>
                                            </td>
                                            <td>
                                                {{ $company->users_count }} / {{ $company->max_users ?: '∞' }}
                                            </td>
                                            <td>
                                                {{ $license['active_features'] }} / {{ $license['total_features'] }}
                                            </td>
                                            <td>
                                                <span class="nexora-status {{ $company->is_active ? 'nexora-status-success' : 'nexora-status-danger' }}">
                                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="nexora-action-group">
                                                    <a href="{{ url('admin-panel/companies/show', $company->id) }}" class="nexora-action-btn is-info" title="Detail"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ url('admin-panel/companies/edit', $company->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
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
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#companiesTable').DataTable();
    });
</script>
@endsection
