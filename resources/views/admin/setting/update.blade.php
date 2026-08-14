@extends('admin.layout.app')

@section('title', 'Update Setting')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Setting</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-warning mb-4">
                    <h5><i class="fas fa-folder"></i> Update Setting</h5>
                    
                                    <form action="{{ url('admin-panel/settings/update/'.$setting->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">

									    <div class="form-group">
												<label for="key">Setting Key</label>
												<input type="text" name="key" class="form-control" value="{{ $setting->key }}">
										</div>

									    <div class="form-group">
												<label for="value">Setting Value</label>
												<input type="text" name="value" class="form-control" value="{{ $setting->value }}">
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Update</button>
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
@section('scripts')


@endsection
