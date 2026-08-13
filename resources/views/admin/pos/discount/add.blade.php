@extends('admin.layout.app')

@section('title', 'Add Discount')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Discount</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-warning mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Discount</h5>
                    
                                    <form action="{{ url('admin-panel/discounts/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="name">Discount Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Store Name">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="type">Discount Type</label>
											<select name="type" id="type" class="form-control">
                                                <option value="percentage">Percentage</option>
                                                <option value="fixed">Fixed Amount</option>
                                            </select>
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="value">Discount Value</label>
											<input type="number" min="0" step="0.01" class="form-control" name="value" id="value"  placeholder="Enter Discount Value">
                                            @error('value')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/discounts/listing') }}" class="btn btn-secondary">Cancel</a>
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
