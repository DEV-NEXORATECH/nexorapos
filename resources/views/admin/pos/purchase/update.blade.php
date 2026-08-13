@extends('admin.layout.app')

@section('title', 'Update Purchase')

@section('content')

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Purchase</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Purchase</div>
									</div>
                                    <form action="{{ url('admin-panel/purchase/update/'. $purchase->id) }}" method="POST" id="purchaseForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="supplier_id">Supplier Name</label>
                                            <select name="supplier_id" id="supplier_id" class="form-control" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'disabled' : 'required' }}>
                                                @foreach ($suppliers as $sup)
                                               <option value="{{ $sup->id }}" {{ $sup->id == $purchase->supplier_id ? 'selected' : ''}}>{{ $sup->name }}</option>
                                                @endforeach
                                            </select>
                                            @if(strtoupper($purchase->status) === 'RECEIVED')
                                                <input type="hidden" name="supplier_id" value="{{ $purchase->supplier_id }}">
                                            @endif
                                            @error('supplier_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'disabled' : 'required' }}>
                                                <option value="PENDING" {{ strtoupper($purchase->status) === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                                <option value="RECEIVED" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'selected' : '' }}>RECEIVED</option>
                                                <option value="CANCELLED" {{ strtoupper($purchase->status) === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                            </select>
                                            @if(strtoupper($purchase->status) === 'RECEIVED')
                                                <input type="hidden" name="status" value="{{ $purchase->status }}">
                                            @endif
                                            @error('status')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <!-- Dynamic Items Table -->
                                        <div class="form-group">
                                            <label>Purchase Items</label>
                                            <table class="table table-bordered" id="itemsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Cost</th>
                                                        <th>Subtotal</th>
                                                        @if(strtoupper($purchase->status) !== 'RECEIVED')
                                                        <th>Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchase->purchase_items as $index => $item)
                                                    <tr>
                                                        <td>
                                                            <select name="items[{{ $index }}][product_id]" class="form-control product-select" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'disabled' : 'required' }}>
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $product)
                                                                    <option value="{{ $product->id }}" data-cost="{{ $product->cost }}" {{ $product->id == $item->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if(strtoupper($purchase->status) === 'RECEIVED')
                                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty-input" value="{{ $item->quantity }}" min="1" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'readonly' : 'required' }}>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="items[{{ $index }}][cost]" class="form-control cost-input" value="{{ $item->cost }}" {{ strtoupper($purchase->status) === 'RECEIVED' ? 'readonly' : 'required' }}>
                                                        </td>
                                                        <td><span class="subtotal">{{ number_format($item->quantity * $item->cost, 2) }}</span></td>
                                                        @if(strtoupper($purchase->status) !== 'RECEIVED')
                                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @if(strtoupper($purchase->status) !== 'RECEIVED')
                                            <button type="button" class="btn btn-info btn-sm" id="addRow">Add Item</button>
                                            @endif
                                        </div>

										<div class="form-group">
											<label for="total_amount">Total Amount</label>
											<input type="text" name="total_amount" id="total_amount" class="form-control" readonly value="{{ $purchase->total_amount }}">
                                            @error('total_amount')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
                                            @if(strtoupper($purchase->status) !== 'RECEIVED')
											    <button class="btn btn-success">Update</button>
                                            @endif
											 <a href="{{ url('admin-panel/purchase/listing') }}" class="btn btn-danger">{{ strtoupper($purchase->status) === 'RECEIVED' ? 'Back' : 'Cancel' }}</a>
										</div>

                                    </div>
                                    </form>

                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>

@endsection

@if(strtoupper($purchase->status) !== 'RECEIVED')
@section('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = {{ count($purchase->purchase_items) }};

        $('#addRow').click(function() {
            let tr = `<tr>
                <td>
                    <select name="items[${itemIndex}][product_id]" class="form-control product-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-cost="{{ $product->cost }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control qty-input" value="1" min="1" required></td>
                <td><input type="number" step="0.01" name="items[${itemIndex}][cost]" class="form-control cost-input" value="0" required></td>
                <td><span class="subtotal">0.00</span></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            </tr>`;
            $('#itemsTable tbody').append(tr);
            itemIndex++;
        });

        $(document).on('change', '.product-select', function() {
            let cost = $(this).find(':selected').data('cost') || 0;
            $(this).closest('tr').find('.cost-input').val(cost);
            calculateTotal();
        });

        $(document).on('input', '.qty-input, .cost-input', function() {
            calculateTotal();
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        function calculateTotal() {
            let total = 0;
            $('#itemsTable tbody tr').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let cost = parseFloat($(this).find('.cost-input').val()) || 0;
                let subtotal = qty * cost;
                $(this).find('.subtotal').text(subtotal.toFixed(2));
                total += subtotal;
            });
            $('#total_amount').val(total.toFixed(2));
        }
    });
</script>
@endsection
@endif
