@extends('admin.layout.app')

@section('title', 'Add New Method')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Payment Methods</h4>
                <a href="{{ url('admin-panel/payment-methods/add') }}" class="btn btn-secondary" style="border: none;">Add New Methods</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($methods as $method)
                                        <tr>
                                            <td>{{ $method->id }}</td>
                                            <td>{{ $method->name }}</td>
                                            <td class="text-nowrap">{{ $method->company?->name ?? 'System' }}</td>
                                            <td class="text-nowrap">{{ $method->user?->name ?? $method->creator?->name ?? '-' }}</td>
                                            <td class="text-nowrap">{{ $method->created_at ? $method->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/payment-methods/edit', $method->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/payment-methods/delete', $method->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
