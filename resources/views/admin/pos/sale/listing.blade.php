@extends('admin.layout.app')

@section('title', 'Sales')

@section('content')
<style>
    .sales-list-page { min-width: 0; }
    #salesTable { width: 100% !important; min-width: 1500px; }
    #salesTable th, #salesTable td { white-space: nowrap; vertical-align: middle; }
    @media (max-width: 768px) {
        .sales-list-page .content { padding-left: 10px !important; padding-right: 10px !important; }
    }
</style>
<div class="main-panel sales-list-page">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Sales</h4>
                <a href="{{ url('admin-panel/pos/sell') }}" class="btn btn-secondary" style="border: none;">Add New Sale</a>
            </div>
                            <div class="nexora-table-scroll">
                            <table class="table nexora-data-table" id="salesTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Store</th>
                                        <th>Customer</th>
                                        <th>Cashier</th>
                                        <th>Items</th>
                                        <th>Subtotal</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Grand Total</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                        <th>Sale Status</th>
                                        <th>Created</th>
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
            ajax: '{{ url("admin-panel/sales/listing") }}',
            autoWidth: false,
            columns: [
                { data: 'invoice', name: 'invoice_number' },
                { data: 'date', name: 'created_at' },
                { data: 'store', name: 'store.name' , orderable: false, searchable: false },
                { data: 'customer', name: 'customer.name' , orderable: false, searchable: false },
                { data: 'cashier', name: 'user.name' , orderable: false, searchable: false },
                { data: 'items', name: 'items' },
                { data: 'subtotal', name: 'sub_total' },
                { data: 'discount', name: 'total_discount' },
                { data: 'tax', name: 'total_tax' },
                { data: 'grand_total', name: 'grand_total' },
                { data: 'paid', name: 'paid_amount' },
                { data: 'change', name: 'change_amount' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'sale_status', name: 'sale_status' },
                { data: 'created', name: 'created_at_time' },
                { data: 'company', name: 'company.name' , orderable: false, searchable: false },
                { data: 'created_by', name: 'user.name' , orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at_full' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
