@extends('admin.layout.app')

@section('title', 'Update')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">

            <h4 class="page-title">Update User</h4>

            <div class="">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Update User</div>
                    </div>

                    <form action="{{ url('admin-panel/users/update', $user->id) }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label for="name">User Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}">
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role_id" id="role" class="form-control select2">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Old Image --}}
                            <div class="form-group">
                                <label for="old_image">Old Image</label><br>
                                @if($user->profile_image)
                                    <img src="{{ asset('admin/profile_images/' . $user->profile_image) }}" width="50px" height="50px" alt="Profile Image">
                                @else
                                    <span>No image uploaded</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="profile_image">Profile Image</label>
                                <input type="file" name="image" id="profile_image" class="form-control">
                            </div>

                            <div class="card-action mt-3">
                                <button class="btn btn-success">Update</button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
