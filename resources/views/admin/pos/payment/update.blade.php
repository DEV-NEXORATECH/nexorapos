@extends('admin.layout.app')

@section('title', 'Update Payment')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Payments</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Payment</div>
									</div>
                                    <form action="{{ url('admin-panel/payments/update', $payment->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
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

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
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
