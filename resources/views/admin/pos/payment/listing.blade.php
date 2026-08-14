@extends('admin.layout.app')

@section('title', 'Add New Payment')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Payments</h4>
                <a href="{{ url('admin-panel/payments/add') }}" class="btn btn-secondary" style="border: none;">Add New Payment</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sale Status</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
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
            ajax: '{{ url("admin-panel/payments/listing") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'sale_status', name: 'sale.sale_status' , orderable: false, searchable: false },
                { data: 'method', name: 'payment_method' },
                { data: 'amount', name: 'amount' },
                { data: 'reference', name: 'reference_number' },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
