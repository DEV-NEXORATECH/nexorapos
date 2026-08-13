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
											<select name="type" id="type" class="form-control">
                                                <option value="percentage" {{ $discount->type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                <option value="fixed" {{ $discount->type === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                            </select>
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="value">Discount Value</label>
											<input type="number" min="0" step="0.01" class="form-control" name="value" id="value" value="{{ $discount->value }}">
                                            @error('value')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
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
