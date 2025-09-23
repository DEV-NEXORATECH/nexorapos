@extends('admin.layout.app')

@section('title', 'Update Tax')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Taxes</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Tax</div>
									</div>
                                    <form action="{{ url('admin-panel/tax/update', $tax->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Tax Type</label>
											<input type="text" class="form-control" name="name" id="name"  value="{{ $tax->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
                                        <div class="form-group">
                                            <label for="rate">Tax Rate (%)</label>
                                            <input type="text" class="form-control" name="rate" id="rate"  value="{{ $tax->rate }}">
                                            @error('rate')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
											 <a href="{{ url('admin-panel/tax/listing') }}" class="btn btn-danger">Cancel</a>
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
