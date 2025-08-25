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
										<div class="card-title">Update Permission</div>
									</div>
                                    <form action="{{ url('admin-panel/permissions/update', $permissions->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="permission_category_id">Category Name</label>
                                            <select name="permission_category_id" id="permission_category_id" class="form-control select2">
                                                <option value="{{ $permissions->permission_category->id ?? '' }}" selected>
                                                {{ $permissions->permission_category->name ?? 'Select Category' }}
                                                </option>

                                                @foreach ($categories as $categorie)
                                                <option value="{{ $categorie->id }}">{{ $categorie->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
										<div class="form-group">
											<label for="name">Name</label>
											<input type="text" class="form-control" name="name" id="name"  value="{{ $permissions->name }}">
										</div>

                                        <div class="form-group">
											<label for="route">Route</label>
											<input type="text" class="form-control" name="route" id="route"  value="{{ $permissions->route }}">
										</div>

                                         <div class="col-md-4 col-sm-6">
                                                            <div class="form-check permission-checkbox">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="is_show" {{ $permissions->is_show == 1 ? 'checked': '' }}>
                                                                    <span class="form-check-sign">Is_Show</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
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
