@extends('admin.layout.app')

@section('title', 'Add New User')

@section('content')

<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">User</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New User</div>
									</div>
                                    <form action="{{ url('admin-panel/users/add') }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-body">

									    <div class="form-group">
												<label for="name">User Name</label>
												<input type="text" name="name" id="name" class="form-control" placeholder="Name">
										</div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                        </div>

                                        <div class="form-group">
                                            <label for="profile_image">Profile Image</label>
                                            <input type="file" name="image" id="profile_image" class="form-control">
                                        </div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
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
