@extends('admin.layout.app')

@section('title', 'Add Customer')

@section('content')

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Customer</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Customer</h5>
                    
                                    <form action="{{ url('admin-panel/customer/update', $customer->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="name">Customer Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter customer Name" value="{{ $customer->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="email">Customer Email</label>
											<input type="email" class="form-control" name="email" id="email"  placeholder="Enter Customer Email" value="{{ $customer->email }}">
                                            @error('email')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="phone">Customer Phone</label>
											<input type="number" class="form-control" name="phone" id="phone"  placeholder="Enter Customer Phone" value="{{ $customer->phone }}">
                                            @error('phone')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="address">Customer Address</label>
											<input type="text" class="form-control" name="address" id="address"  placeholder="Enter Customer Address" value="{{ $customer->address }}">
                                            @error('address')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/customer/listing') }}" class="btn btn-outline-primary">Cancel</a>
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
