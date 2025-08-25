@extends('admin.layout.app')

@section('title', 'Users')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Users</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Users</div>
                            <div>
                                <a href="{{ url('admin-panel/users/add') }}" class="btn btn-primary">Add New User</a>
                            </div>
                        </div>

                        <div class="card-body">

                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Profile</th>
                                        <th class="min-w-120 text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <img src="{{ asset('admin/profile_images/'. $user->profile_image) }}" alt="Profile Image" width="50" height="50">
                                            </td>
                                            <td>
                                                @foreach ($user->roles as $role)
                                                    {{ $role->role }}
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ url('admin-panel/users/edit',$user->id) }}" class="btn btn-success"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/users/delete', $user->id) }}" class="btn btn-danger  delete-category"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-w-120 {
        min-width: 120px;
    }
</style>

@endsection

@section('scripts')

<!-- Your custom JS -->
<script src="{{ asset('admin/custom/js/category.js') }}"></script>

@endsection
