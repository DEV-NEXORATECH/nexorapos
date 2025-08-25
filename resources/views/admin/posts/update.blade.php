@extends('admin.layout.app')

@section('title', 'Update Post')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">Post</h4>
            <div>
                <div class="">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Update Post</div>
                        </div>

                        <form action="{{ url('admin-panel/posts/update/' . $posts->id) }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                            @csrf
                            {{-- If your route uses PUT/PATCH method --}}
                            @method('PUT')

                            <div class="card-body">

                                {{-- User Name --}}

                                <div class="form-group">
											<label for="user_id">User Name</label>
                                            <select name="user_id" id="user_id" class="form-control">
                                                <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>

                                            </select>
										</div>

                                {{-- Category Name --}}
                                <div class="form-group">
                                    <label for="category_id">Category Name</label>
                                    <select name="category_id" id="category_id" class="form-control select2">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == $posts->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Post Title --}}
                                <div class="form-group">
                                    <label for="title">Post Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $posts->title }}" placeholder="Enter Post Title Here">
                                </div>

                                {{-- Old Image --}}
                                <div class="form-group">
                                    <label for="old_image">Old Image</label><br>
                                    <img src="{{ asset('admin/uploads/' . $posts->image) }}" width="50px" height="50px" alt="Post Image">
                                </div>

                                {{-- New Image Upload --}}
                                <div class="form-group">
                                    <label for="image">Feature Image</label>
                                    <input type="file" name="image" class="form-control">
                                </div>

                                {{-- Content --}}
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea class="form-control tinymce-editor" id="content" name="content" rows="5" placeholder="Post Content Type Here....">{{ $posts->content }}</textarea>
                                </div>

                                {{-- Submit Button --}}
                                <div class="card-action">
                                    <button class="btn btn-success" type="submit">Update</button>
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
