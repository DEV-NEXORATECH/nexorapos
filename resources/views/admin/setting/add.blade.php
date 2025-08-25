@extends('admin.layout.app')

@section('title', 'New Setting')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Setting</h4>

                         <div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Setting</div>

									</div>
                                    <form action="{{ url('admin-panel/settings/add') }}" method="POST" id="categoryForm">
                                        @csrf

                                        <div class="card-body">
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
