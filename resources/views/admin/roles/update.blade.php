@extends('admin.layout.app')

@section('title', 'Add Role')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">


            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-user-shield"></i> Update Role</h4>
                <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/roles/update', $roles->id) }}" method="POST" id="categoryForm">
                @csrf
                <div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Role Information</h5>
                    <div class="card-body p-0 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role Name *</label>
                                    <input type="text" class="form-control" name="role" id="role" value="{{ $roles->role }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="section-title mb-0" style="color: #FFFFFF !important;"><span>Role Permissions</span></h4>
                                    <button type="button" class="btn btn-secondary btn-sm" id="selectAllPermissions">Select All</button>
                                </div>
                                <div class="permission-categories">
                                    @foreach($permissionCategories as $category)
                                    <div class="nexora-permission-group mb-3">
                                        <div class="nexora-permission-group-head">
                                            <strong><i class="{{ $category->icons ?? $category->icon ?? 'la la-folder' }}"></i> {{ $category->name }}</strong>
                                            <button type="button" class="btn btn-secondary btn-sm js-select-row">Select Module</button>
                                        </div>
                                        <div class="row">
                                            @foreach($category->permissions as $permission)
                                            <div class="col-md-3 col-sm-6">
                                                <label class="nexora-permission-check">
                                                    <input type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                                    <span>{{ $permission->name }}</span>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nexora-form-actions mt-3">
                        <button type="submit" class="btn btn-primary">Update Role</button>
                        <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('selectAllPermissions')?.addEventListener('click', function () {
        document.querySelectorAll('input[name="permissions[]"]').forEach((input) => input.checked = true);
    });

    document.querySelectorAll('.js-select-row').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('.nexora-permission-group')?.querySelectorAll('input[name="permissions[]"]').forEach((input) => input.checked = true);
        });
    });
});
</script>
@endpush
