@extends('admin.layout.app')

@section('title', 'Add Permission Category')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Categories</h4>
                <a href="{{ url('admin-panel/permission-categories/add') }}" class="btn btn-secondary" style="border: none;">Add New Category</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Display Order</th>
                                        <th>Icon</th>
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->display_order }}</td>
                                        <td>{{ $category->icons }}</td>
                                        <td class="text-nowrap">{{ $category->company?->name ?? 'System' }}</td>
                                            <td class="text-nowrap">{{ $category->user?->name ?? $category->creator?->name ?? '-' }}</td>
                                            <td class="text-nowrap">{{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                            <a href="{{ url('admin-panel/permission-categories/edit', $category->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="{{ url('admin-panel/permission-categories/delete', $category->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
