@extends('admin.layout.app')

@section('title', 'Update Discount')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Discount</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Discount</div>
									</div>
                                    <form action="{{ url('admin-panel/discounts/update', $discount->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Discount Name</label>
											<input type="text" class="form-control" name="name" id="name" value="{{ $discount->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="type">Discount Type</label>
											<input type="text" class="form-control" name="type" id="type" value="{{ $discount->type }}">
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="value">Discount Value</label>
											<input type="number" class="form-control" name="value" id="value" value="{{ $discount->value }}">
                                            @error('value')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/discounts/listing') }}" class="btn btn-danger">Cancel</a>
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
