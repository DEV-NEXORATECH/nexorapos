@extends('admin.layout.app')

@section('title', 'Add Permission')

@section('content')
{{-- main body --}}

        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Permissions</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Permission</div>
									</div>
                                    <form action="{{ url('admin-panel/permissions/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">

                                        <div class="form-group">
											<label for="name">Category</label>
                                            <select name="permission_category_id" id="" class="form-control select2">
                                                <option value="" selected>Select Category </option>
                                                @foreach ($permissions as $permission)
                                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

										<div class="form-group">
											<label for="name">Name</label>
											<input type="text" class="form-control" name="name" id="name"  placeholder="Enter Permission Name">
										</div>

                                        <div class="form-group">
											<label for="route">Route</label>
											<input type="text" class="form-control" name="route" id="route"  placeholder="Enter Permission Route">
										</div>

                                         <div class="col-md-4 col-sm-6">
                                                            <div class="form-check permission-checkbox">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="is_show"

>
                                                                    <span class="form-check-sign">Is_Show</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                            <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/permissions/listing') }}" class="btn btn-danger">Cancel</a>
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
