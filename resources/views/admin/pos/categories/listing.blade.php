@extends('admin.layout.app')

@section('title', 'Add New Tax')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Categories</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">CAtegory</div>
                            <div>
                                <a href="{{ url('admin-panel/categories/add') }}" class="btn btn-primary">Add New Category</a>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/categories/edit/'.$category->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/categories/delete/'.$category->id) }}" class="btn btn-sm btn-danger delete-category"><i class="fas fa-trash"></i></a>
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
