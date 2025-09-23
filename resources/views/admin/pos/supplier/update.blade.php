@extends('admin.layout.app')

@section('title', 'Add Supplier')

@section('content')

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Supplier</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Supplier</div>
									</div>
                                    <form action="{{ url('admin-panel/supplier/update', $supplier->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Supplier Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Supplier Name" value="{{ $supplier->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="email">Supplier Email</label>
											<input type="email" class="form-control" name="email" id="email"  placeholder="Enter Supplier Email" value="{{ $supplier->email }}">
                                            @error('email')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="phone">Supplier Phone</label>
											<input type="number" class="form-control" name="phone" id="phone"  placeholder="Enter Supplier Phone" value="{{ $supplier->phone }}">
                                            @error('phone')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="form-group">
											<label for="address">Supplier Address</label>
											<input type="text" class="form-control" name="address" id="address"  placeholder="Enter Supplier Address" value="{{ $supplier->address }}">
                                            @error('address')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/categories/listing') }}" class="btn btn-danger">Cancel</a>
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
