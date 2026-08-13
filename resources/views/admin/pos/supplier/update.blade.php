@extends('admin.layout.app')

@section('title', 'Add Supplier')

@section('content')

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Supplier</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Supplier</h5>
                    
                                    <form action="{{ url('admin-panel/supplier/update', $supplier->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
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

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
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
