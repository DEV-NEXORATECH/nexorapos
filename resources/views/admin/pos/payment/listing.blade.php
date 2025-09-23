@extends('admin.layout.app')

@section('title', 'Add New Payment')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Payments</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Payments</div>
                            <div>
                                <a href="{{ url('admin-panel/payments/add') }}" class="btn btn-primary">Add New Payment</a>
                            </div>
                        </div>

                        <div class="card-body">

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
                                                <a href="{{ url('admin-panel/payments/edit', $pay->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/payments/delete', $pay->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>




@endsection
