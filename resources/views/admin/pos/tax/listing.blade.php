@extends('admin.layout.app')

@section('title', 'Add New Tax')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-percent"></i> Taxes</h4>
                <a href="{{ url('admin-panel/tax/add') }}" class="btn btn-secondary" style="border: none;">Add New Tax</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Rate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($taxes as $tax)
                                        <td>{{ $tax->id }}</td>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ $tax->rate }}%</td>
                                        <td>
                                            <a href="{{ url('admin-panel/tax/edit', $tax->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/tax/delete', $tax->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
