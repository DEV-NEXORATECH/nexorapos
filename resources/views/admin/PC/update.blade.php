@extends('admin.layout.app')

@section('title', 'Update Category')

@section('content')

<div class="main-panel">
				<div class="content">
					<div class="container-fluid">


                         <h4 class="page-title">Category</h4>
						<div>
							<div >
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Category</div>
									</div>
                                    <form action="{{ url('admin-panel/permission-categories/update' , $category->id )}}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="name">Name</label>
											<input type="text" class="form-control" name="name" id="name" value="{{ $category->name }}" placeholder="Enter Category Name">
										</div>

                                        <div class="form-group">
											<label for="display_order">Display Order</label>
											<input type="number" class="form-control" name="display_order" id="display_order" value="{{ $category->display_order }}"  placeholder="Display Order">
										</div>

                                        <div class="form-group">
											<label for="icons">Icon</label>
											<input type="text" class="form-control" name="icons" id="icons" value="{{ $category->icons }}"  placeholder="Add Category Icon">
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
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




@endsection
