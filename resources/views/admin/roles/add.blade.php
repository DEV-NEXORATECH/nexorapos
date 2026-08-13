@extends('admin.layout.app')

@section('title', 'Add Role')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="page-title mb-1">Create Role</h4>
                    <p class="card-category mb-0">Buat role dan langsung tentukan permission aksesnya.</p>
                </div>
                <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary">Back</a>
            </div>

            <form action="{{ url('admin-panel/roles/add') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Role Information</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role Name *</label>
                                    <input type="text" class="form-control" name="role" id="role" value="{{ old('role') }}" placeholder="Contoh: Kasir" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title mb-1">Access / Permissions</div>
                            <p class="card-category mb-0">Checklist permission sesuai fitur yang aktif di company.</p>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="selectAllPermissions">Select All</button>
                    </div>
                    <div class="card-body">
                        @forelse($permissionCategories as $category)
                            <div class="nexora-permission-group mb-3">
                                <div class="nexora-permission-group-head">
                                    <strong><i class="{{ $category->icons ?? $category->icon ?? 'la la-folder' }}"></i> {{ $category->name }}</strong>
                                    <button type="button" class="btn btn-secondary btn-sm js-select-row">Select Module</button>
                                </div>
                                <div class="row">
                                    @foreach($category->permissions as $permission)
                                        <div class="col-md-3 col-sm-6">
                                            <label class="nexora-permission-check">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', [])))>
                                                <span>{{ $permission->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="nexora-empty-state">
                                <strong>Belum ada permission.</strong>
                                <span>Seed permission terlebih dahulu agar role bisa diberi akses.</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Role</button>
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
