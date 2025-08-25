@extends('admin.layout.app')

@section('title', 'New Post')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Post</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Add New Post</div>
									</div>
                                    <form action="{{ url('admin-panel/posts/add') }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-body">

										<div class="form-group">
											<label for="user_id">User Name</label>
                                            <select name="user_id" id="user_id" class="form-control">
                                                <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>

                                            </select>
										</div>

										<div class="form-group">
											<label for="category_id">Category Name</label>
                                            <select name="category_id" id="category_id" class="form-control select2">
                                                <option value="" selected>Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
										</div>

									    <div class="form-group">
												<label for="title">Post Title</label>
												<input type="text" name="title" class="form-control" placeholder="Enter Post Title Here">
										</div>

                                        <div>
                                            <label for="image">Feature Image</label>
                                            <input type="file" name="image" class="form-control">
                                        </div>

                                        <div>
                                           <label for="content">Content</label>
												<textarea class="form-control tinymce-editor" id="content" name="content" rows="5" placeholder="Post Content Type Here...."></textarea>
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
