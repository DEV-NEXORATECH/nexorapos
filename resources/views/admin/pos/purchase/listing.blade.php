@extends('admin.layout.app')

@section('title', 'Add New Purchase')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Purchase</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Purchase</div>
                            <div>
                                <a href="{{ url('admin-panel/purchase/add') }}" class="btn btn-primary">Add New Purchase</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Supplier Name</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchases as $pur)
                                        <tr>
                                            <td>{{ $pur->id }}</td>
                                            <td>{{ $pur->supplier->name }}</td>
                                            <td>{{ $pur->total_amount }}</td>
                                            <td>{{ $pur->status }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/purchase/edit/'.$pur->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/purchase/delete/'.$pur->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
