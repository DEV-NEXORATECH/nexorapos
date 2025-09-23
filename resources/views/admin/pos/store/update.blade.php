@extends('admin.layout.app')

@section('title', 'Update Store')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Store</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Store</div>
									</div>
                                    <form action="{{ url('admin-panel/stores/update', $store->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Store Name</label>
											<input type="text" class="form-control" name="name" id="name" value="{{ $store->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="code">Store Code</label>
											<input type="text" class="form-control" name="code" id="code" value="{{ $store->code }}">
                                            @error('code')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
											 <a href="{{ url('admin-panel/stores/listing') }}" class="btn btn-danger">Cancel</a>
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
