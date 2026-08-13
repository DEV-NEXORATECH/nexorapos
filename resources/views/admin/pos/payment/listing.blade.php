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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $pay)
                                        <tr>
                                            <td>{{ $pay->id }}</td>
                                            <td>{{ $pay->sale->status}}</td>
                                            <td>{{ $pay->method->name}}</td>
                                            <td>{{ $pay->amount }}</td>
                                            <td>{{ $pay->reference }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/payments/edit', $pay->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/payments/delete', $pay->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
