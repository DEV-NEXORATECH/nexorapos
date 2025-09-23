@extends('admin.layout.app')

@section('title', 'All Discounts')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">All Discounts</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">All Discounts</div>
                            <div>
                                <a href="{{ url('admin-panel/discounts/add') }}" class="btn btn-primary">Add New Discount</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Discount Name</th>
                                        <th>Discount Type</th>
                                        <th>Discount Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($discounts as $discount)
                                        <tr>
                                            <td>{{ $discount->id }}</td>
                                            <td>{{ $discount->name }}</td>
                                            <td>{{ $discount->type }}</td>
                                            <td>{{ $discount->value }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/discounts/edit', $discount->id) }}" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/discounts/delete', $discount->id) }}" class="btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
