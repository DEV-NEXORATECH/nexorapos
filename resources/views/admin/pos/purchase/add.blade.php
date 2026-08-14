@extends('admin.layout.app')

@section('title', 'Add Purchase')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Purchase</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Purchase</h5>
                    
                                    <form action="{{ url('admin-panel/purchase/add') }}" method="POST" id="purchaseForm">
                                        @csrf

                                        <div class="row">
                                            @include('admin.partials.company-selector')
                                        </div>
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="supplier_id">Supplier Name</label>
											<select name="supplier_id" id="supplier_id" class="form-control" required>
                                                <option value="">Select Supplier</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="status">Status</label>
											<select name="status" id="status" class="form-control" required>
                                                <option value="PENDING">PENDING</option>
                                                <option value="RECEIVED">RECEIVED</option>
                                            </select>
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
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select name="items[0][product_id]" class="form-control product-select" required>
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $product)
                                                                    <option value="{{ $product->id }}" data-cost="{{ $product->cost }}">{{ $product->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="items[0][quantity]" class="form-control qty-input" value="1" min="1" required></td>
                                                        <td><input type="number" step="0.01" name="items[0][cost]" class="form-control cost-input" value="0" required></td>
                                                        <td><span class="subtotal">0.00</span></td>
                                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-info btn-sm" id="addRow">Add Item</button>
                                        </div>

										<div class="form-group">
											<label for="total_amount">Total Amount</label>
											<input type="text" name="total_amount" id="total_amount" class="form-control" readonly value="0">
                                            @error('total_amount')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/purchase/listing') }}" class="btn btn-danger">Cancel</a>
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

@section('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = 1;

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
