@extends('admin.layout.app')

@section('title', 'Add Role')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">


            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Update Role</div>
                        </div>
                        <form action="{{ url('admin-panel/roles/update', $roles->id) }}" method="POST" id="categoryForm">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role">Role Name *</label>
                                            <input type="text" class="form-control" name="role" id="role" value="{{ $roles->role }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="section-title"><span style="font-weight: bold;">Role Permissions</span></h4>
                                        <div class="permission-categories">
                                            @foreach($permissionCategories as $category)
                                            <div class="category-card mb-4">
                                                <div class="category-header">
                                                    <h5 style="background-color: #f5f5f5">{{ $category->name }}</h5>
                                                </div>
                                                <div class="category-body">
                                                    <div class="row">
                                                        @foreach($category->permissions as $permission)
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="form-check permission-checkbox">
                                                                <label class="form-check-label">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                       {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}
>
                                                                    <span class="form-check-sign">{{ $permission->name }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Role</button>
                                <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

