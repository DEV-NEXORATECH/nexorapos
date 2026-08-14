@extends('admin.layout.app')

@section('title', 'Purchase Items')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-list"></i> Purchase Item</h4>
                <a href="{{ url('admin-panel/purchase-item/add') }}" class="btn btn-secondary" style="border: none;">Add New Purchase Item</a>
            </div>

                            {{-- Actual Categories Table --}}
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Purchase ID</th>
                                        <th>Product ID</th>
                                        <th>Quantity</th>
                                        <th>Cost</th>
                                        <th class="text-nowrap">Company</th>
                                        <th class="text-nowrap">Created By</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->purchase->status }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->cost }}</td>
                                            <td class="text-nowrap">{{ $item->company?->name ?? 'System' }}</td>
                                            <td class="text-nowrap">{{ $item->user?->name ?? $item->creator?->name ?? '-' }}</td>
                                            <td class="text-nowrap">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ url('admin-panel/purchase-items/edit/'. $item->id) }}" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/purchase-items/delete/'. $item->id) }}" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>
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
