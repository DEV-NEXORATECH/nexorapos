@extends('admin.layout.app')

@section('title', 'Sales')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Sales</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Sales</div>
                            <div>
                                <a href="{{ url('admin-panel/pos/sell') }}" class="btn btn-primary">Add New Sale</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="salesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Store Name</th>
                                        <th>Customer Name</th>
                                        <th>User Name</th>
                                        <th>Sub Total</th>
                                        <th>Tax</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ optional($sale->store)->name ?? 'N/A' }}</td>
                                            <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
                                            <td>{{ optional($sale->user)->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($sale->subtotal, 2) }}</td>
                                            <td>{{ number_format($sale->tax, 2) }}</td>
                                            <td>{{ number_format($sale->discount, 2) }}</td>
                                            <td>{{ number_format($sale->total, 2) }}</td>
                                            <td>{{ ucfirst($sale->status) }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/pos/pdf', $sale->id) }}" class="btn btn-info"><i class="fa-regular fa-file-pdf"></i>  </a>
                                                <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" target="_blank" class="btn-sm btn-success">
                                                    <i class="fas fa-print"></i> Print
                                                </a>
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
@endsection

@section('scripts')
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
<script>
    // Optional: Add confirmation for delete
    document.querySelectorAll('.delete-sale').forEach(function(el){
        el.addEventListener('click', function(e){
            if(!confirm('Are you sure you want to delete this sale?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
