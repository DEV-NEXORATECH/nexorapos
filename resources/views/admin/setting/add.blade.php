@extends('admin.layout.app')

@section('title', 'New Setting')

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
                    <h5><i class="fas fa-folder"></i> Add New Setting</h5>
                    
                                    <form action="{{ url('admin-panel/settings/add') }}" method="POST" id="categoryForm">
                                        @csrf

                                                            <div class="card-body p-0 mt-3">
                                            <div class="form-group">
                                                <label for="login_method">Choose Login Method</label>
                                                <select name="login_method" id="login_method" class="form-control">
                                                    <option value="phone" {{ old('login_method', $settings->where('key', 'login_method')->first()->value ?? '') == 'phone' ? 'selected' : '' }}>Phone</option>
                                                    <option value="email" {{ old('login_method', $settings->where('key', 'login_method')->first()->value ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="newsletter">News Letter</label>
                                                <input type="text" name="newsletter" class="form-control" value="{{ $settings->where('key', 'newsletter')->first()->value ?? '' }}" placeholder="Enter News Letter Text Here...">
                                            </div>
                                            <div class="form-group">
                                                <label for="about_us">About Us</label>
                                                <textarea name="about_us" id="about_us" cols="30" rows="5" class="form-control">{{ $settings->where('key', 'about_us')->first()->value ?? '' }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="facebook">Follow On Facebook</label>
                                                <input type="text" name="facebook" class="form-control" value="{{ $settings->where('key', 'facebook')->first()->value ?? '' }}" placeholder="Enter Facebook Link Here...">
                                            </div>
                                            <div class="form-group">
                                                <label for="instagram">Follow On Instagram</label>
                                                <input type="text" name="instagram" class="form-control"
                                                    value="{{ $settings->where('key', 'instagram')->first()->value ?? '' }}"
                                                    placeholder="Enter Instagram Link Here...">
                                            </div>
                                            <div class="form-group">
                                                <label for="youtube">Follow On Youtube</label>
                                                <input type="text" name="youtube" class="form-control" value="{{ $settings->where('key', 'youtube')->first()->value ?? '' }}" placeholder="Enter Youtube Link Here...">
                                            </div>
                                            <div class="form-group">
                                                <label for="twitter">Follow On Twitter</label>
                                                <input type="text" name="twitter" class="form-control" value="{{ $settings->where('key', 'twitter')->first()->value ?? '' }}" placeholder="Enter Twitter Link Here...">
                                            </div>
                                            <div class="nexora-form-actions mt-3">
                                                <button class="btn btn-primary">Submit</button>
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
