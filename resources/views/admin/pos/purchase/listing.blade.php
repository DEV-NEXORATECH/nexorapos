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
                            <table class="table table-bordered" id="purchasesTable">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Reference</th>
                                        <th class="text-right">Total Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $pur)
                                        <tr>
                                            <td><strong>{{ $pur->purchase_number ?? 'PUR-'.$pur->id }}</strong></td>
                                            <td>{{ $pur->created_at?->format('d M Y') }}</td>
                                            <td>{{ optional($pur->supplier)->name ?? '-' }}</td>
                                            <td>{{ $pur->reference ?? '-' }}</td>
                                            <td class="text-right">{{ number_format($pur->total_amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $stBadge = match($pur->status) {
                                                        'RECEIVED' => 'badge-success',
                                                        'PENDING' => 'badge-warning',
                                                        'CANCELLED' => 'badge-danger',
                                                        default => 'badge-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $stBadge }}">{{ $pur->status }}</span>
                                            </td>
                                            <td style="white-space:nowrap;">
                                                <a href="{{ url('admin-panel/purchase/show/'.$pur->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                                @if($pur->status !== 'RECEIVED' && $pur->status !== 'CANCELLED')
                                                    <a href="{{ url('admin-panel/purchase/edit/'.$pur->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                @endif
                                                <form action="{{ url('admin-panel/purchase/delete/'.$pur->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus/cancel purchase ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-truck-loading fa-2x mb-2" style="color:var(--color-blue-500)"></i>
                                                <p>Belum ada purchase.</p>
                                            </td>
                                        </tr>
                                    @endforelse
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
