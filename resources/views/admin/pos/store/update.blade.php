@extends('admin.layout.app')

@section('title', 'Update Store')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Store</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Update Store</h5>
                    
                                    <form action="{{ url('admin-panel/stores/update', $store->id) }}" method="POST" id="categoryForm">
                                        @csrf

                                        <div class="row">
                                            @include('admin.partials.company-selector')
                                        </div>
                                                            <div class="card-body p-0 mt-3">
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

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Update</button>
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
