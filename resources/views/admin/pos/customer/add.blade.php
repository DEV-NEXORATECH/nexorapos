@extends('admin.layout.app')

@section('title', 'Add Customer')

@section('content')

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Customer</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Customer</div>
									</div>
                                    <form action="{{ url('admin-panel/customer/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Customer Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Customer Name">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="email">Customer Email</label>
											<input type="email" class="form-control" name="email" id="email"  placeholder="Enter Customer Email">
                                            @error('email')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="phone">Customer Phone</label>
											<input type="number" class="form-control" name="phone" id="phone"  placeholder="Enter Customer Phone">
                                            @error('phone')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="address">Customer Address</label>
											<input type="text" class="form-control" name="address" id="address"  placeholder="Enter Customer Address">
                                            @error('address')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
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
