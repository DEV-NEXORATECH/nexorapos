@extends('admin.layout.app')

@section('title', 'All Stores')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-store"></i> All Stores</h4>
                <a href="{{ url('admin-panel/stores/add') }}" class="btn btn-secondary" style="border: none;">Add New Store
                </a>
            </div>

            <table class="table nexora-data-table" id="storesTable" style="width:100%">
                <thead>
                                    <tr>
                                        <th>Store Code</th>
                                        <th>Store Name</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stores as $store)
                                        <tr>
                                            <td><strong>{{ $store->store_code ?? $store->code ?? '-' }}</strong></td>
                                            <td>{{ $store->name }}</td>
                                            <td>{{ $store->created_at?->format('d M Y') }}</td>
                                            <td style="white-space:nowrap;">
                                                <a href="{{ url('admin-panel/stores/show', $store->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ url('admin-panel/stores/edit', $store->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="{{ url('admin-panel/stores/delete', $store->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus store ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="fas fa-store fa-2x mb-2" style="color:var(--color-blue-500)"></i>
                                                <p>Belum ada store.</p>
                                            </td>
                                        </tr>
                                    @endforelse
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
