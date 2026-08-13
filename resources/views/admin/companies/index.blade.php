@extends('admin.layout.app')

@section('title', 'Companies')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Companies</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Companies (Tenants)</div>
                            <div>
                                <a href="{{ url('admin-panel/companies/add') }}" class="btn btn-primary">Add New Company</a>
                            </div>
                        </div>

                        <div class="card-body">
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
                                                    <a href="{{ url('admin-panel/companies/show', $company->id) }}" class="nexora-action-btn" title="Detail"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ url('admin-panel/companies/edit', $company->id) }}" class="nexora-action-btn" title="Edit"><i class="fas fa-edit"></i></a>
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
