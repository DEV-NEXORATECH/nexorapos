@extends('admin.layout.app')

@section('title', 'Add Role')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Roles</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Role</div>
									</div>
                                    <form action="{{ url('admin-panel/roles/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="role">Role</label>
											<input type="text" class="form-control" name="role" id="role"  placeholder="Enter Role">
										</div>



                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-danger">Cancel</a>
										</div>

                                    </div>
                                    </form>

                                </d iv>
                                </div>
                                </div>


                  </div>
                  </div>

                  </div>



@endsection
