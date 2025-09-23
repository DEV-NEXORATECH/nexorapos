
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
                                    <form action="{{ url('admin-panel/purchase/update/'. $purchase->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="supplier_id">Supplier Name</label>
                                            <select name="supplier_id" id="supplier_id" class="form-control">
                                                @foreach ($suppliers as $sup)
                                               <option value="{{ $sup->id }}" {{ $sup->id == $purchase->supplier_id ? 'selected' : ''}}>{{ $sup->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="status">Status</label>
											<input type="text" name="status" id="status" class="form-control" value="{{ $purchase->status }}">
                                            @error('status')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="total_amount">Total Amount</label>
											<input type="text" name="total_amount" id="total_amount" class="form-control" value="{{ $purchase->total_amount }}">
                                            @error('total_amount')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
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
