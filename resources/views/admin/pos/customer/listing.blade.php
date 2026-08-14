@extends('admin.layout.app')

@section('title', 'Customers')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Customers</h4>
                <a href="{{ url('admin-panel/customer/add') }}" class="btn btn-secondary" style="border: none;">Add New Customer</a>
            </div>
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
            ajax: '{{ url("admin-panel/customer/listing") }}',
            columns: [
                { data: 'code', name: 'customer_code' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email' },
                { data: 'address', name: 'address' },
                { data: 'transactions', name: 'transactions' , orderable: false, searchable: false },
                { data: 'total_spend', name: 'total_spend' , orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
