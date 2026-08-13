@extends('admin.layout.app')

@section('title', 'Add New Category')

@section('content')
    	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Category</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Category</h5>
                    
                                    <form action="{{ url('admin-panel/permission-categories/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="name">Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Category Name">
										</div>
										<div class="form-group">
											<label for="display_order">Display Order</label>
											<input type="number" class="form-control" name="display_order" id="display_order"  placeholder="Display Order">
										</div>

										<div class="form-group">
											<label for="icons">Icon</label>
											<input type="text" class="form-control" name="icons" id="icons"  placeholder="Add Category Icon">
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/permission-categories/listing') }}" class="btn btn-danger">Cancel</a>
										</div>

                                    </div>
                                    </form>

                                </div>
                                </div>
                                </div>


                  </div>
                  </div>

                  </div>



        {{-- Main end --}}

@endsection
