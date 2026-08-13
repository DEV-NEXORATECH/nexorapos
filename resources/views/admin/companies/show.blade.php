@extends('admin.layout.app')

@section('title', 'Company Detail')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Company Detail</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Company Information</div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Company Name</th>
                                    <td>{{ $company->name }}</td>
                                </tr>
                                <tr>
                                    <th>Company Code</th>
                                    <td><code>{{ $company->company_code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Legal Name</th>
                                    <td>{{ $company->legal_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NPWP</th>
                                    <td>{{ $company->npwp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $company->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $company->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $company->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="nexora-status {{ $company->is_active ? 'nexora-status-success' : 'nexora-status-danger' }}">
                                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    @php $license = $company->licenseSnapshot(); @endphp
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">License & Usage</div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Package</th>
                                    <td>{{ $license['package'] }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="nexora-status nexora-status-{{ $license['badge_class'] }}">
                                            {{ $license['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <td>{{ $license['expiry_date'] ? $license['expiry_date']->format('Y-m-d') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Users Used</th>
                                    <td>{{ $license['current_users'] }} / {{ $license['max_users'] ?: 'Unlimited' }}</td>
                                </tr>
                                <tr>
                                    <th>Active Features</th>
                                    <td>{{ $license['active_features'] }} modules</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <div class="card-title">Administrators</div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-bordered">
                                @foreach($company->users as $user)
                                    @if($user->role?->role === 'Admin')
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge badge-primary badge-pill">{{ $user->status }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <a href="{{ url('admin-panel/companies/listing') }}" class="btn btn-default">Back to List</a>
                    <a href="{{ url('admin-panel/companies/edit', $company->id) }}" class="btn btn-primary">Edit Company</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
