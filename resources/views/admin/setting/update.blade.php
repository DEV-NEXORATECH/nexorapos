@extends('admin.layout.app')

@section('title', 'Update Setting')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Setting</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Setting</div>
									</div>
                                    <form action="{{ url('admin-panel/settings/update/'.$setting->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">

									    <div class="form-group">
												<label for="key">Setting Key</label>
												<input type="text" name="key" class="form-control" value="{{ $setting->key }}">
										</div>

									    <div class="form-group">
												<label for="value">Setting Value</label>
												<input type="text" name="value" class="form-control" value="{{ $setting->value }}">
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
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
