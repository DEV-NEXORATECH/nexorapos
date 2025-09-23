@extends('admin.layout.app')

@section('title', 'Add Payment Method')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Payment Methode</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Methode</div>
									</div>
                                    <form action="{{ url('admin-panel/payment-methods/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Methode Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Methode Name">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/payment-methods/listing') }}" class="btn btn-danger">Cancel</a>
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
