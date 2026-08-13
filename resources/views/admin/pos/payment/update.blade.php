@extends('admin.layout.app')

@section('title', 'Update Payment')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Payments</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Update Payment</h5>
                    
                                    <form action="{{ url('admin-panel/payments/update', $payment->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="sale_id">Sale Status</label>
											<select name="sale_id" id="sale_id" class="form-control">
                                                @foreach ($sales as $sale)
                                                    <option value="{{ $sale->id }}" {{ $sale->id ==$payment->sale_id? 'selected': '' }}>{{ $sale->status }}</option>
                                                @endforeach
                                            </select>
                                            @error('sale_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="method_id">Choose Method</label>
											<select name="method_id" id="method_id" class="form-control">
                                                @foreach ($methods as $met)
                                                    <option value="{{ $met->id }}" {{ $met->id == $payment->method_id ? 'selected':'' }}>{{ $met->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('method_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="amount">Amount</label>
											<input type="number" name="amount" id="amount" class="form-control" value="{{ $payment->amount }}">
                                            @error('amount')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="reference">Reference</label>
											<input type="text" name="reference" id="reference" class="form-control" value="{{ $payment->reference }}">
                                            @error('reference')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Update</button>
											 <a href="{{ url('admin-panel/payments/listing') }}" class="btn btn-danger">Cancel</a>
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
