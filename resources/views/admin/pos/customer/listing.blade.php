@extends('admin.layout.app')

@section('title', 'Customers')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Customers</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Customers</div>
                            <div>
                                <a href="{{ url('admin-panel/customer/add') }}" class="btn btn-primary">Add New Customer</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Customer Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Transactions</th>
                                        <th>Total Spend</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($customers as $cus)
                                        <tr>
                                            <td>{{ $cus->customer_code ?? '-' }}</td>
                                            <td><strong>{{ $cus->name }}</strong></td>
                                            <td>{{ $cus->phone ?? '-' }}</td>
                                            <td>{{ $cus->email ?? '-' }}</td>
                                            <td>{{ $cus->address ?? '-' }}</td>
                                            <td>{{ $cus->total_transactions }}</td>
                                            <td class="text-right">{{ number_format($cus->total_spend, 2) }}</td>
                                            <td style="white-space:nowrap;">
                                                <a href="{{ url('admin-panel/customer/show', $cus->id) }}" class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ url('admin-panel/customer/edit', $cus->id) }}" class="btn btn-sm btn-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="{{ url('admin-panel/customer/delete', $cus->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus customer ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-users fa-2x mb-2" style="color:var(--color-blue-500)"></i>
                                                <p>Belum ada customer.</p>
                                            </td>
                                        </tr>
                                    @endforelse
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
