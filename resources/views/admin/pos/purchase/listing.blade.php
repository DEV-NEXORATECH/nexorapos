@extends('admin.layout.app')

@section('title', 'Add New Purchase')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Purchase</h4>
                <a href="{{ url('admin-panel/purchase/add') }}" class="btn btn-secondary" style="border: none;">Add New Purchase</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered" id="purchasesTable">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Reference</th>
                                        <th class="text-right">Total Amount</th>
                                        <th>Status</th>
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
            ajax: '{{ url("admin-panel/purchase/listing") }}',
            columns: [
                { data: 'purchase_number', name: 'purchase_number' },
                { data: 'date', name: 'date' },
                { data: 'supplier', name: 'supplier.name' , orderable: false, searchable: false },
                { data: 'reference', name: 'reference_number' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'status', name: 'status' , orderable: false, searchable: false },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'created_by', searchable: false, orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
