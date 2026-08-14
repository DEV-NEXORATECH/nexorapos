@extends('admin.layout.app')

@section('title', 'Add Role')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-user-shield"></i> Create Role</h4>
                <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/roles/add') }}" method="POST">
                @csrf
                <div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Role Information</h5>
                                        <div class="card-body p-0 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role Name *</label>
                                    <input type="text" class="form-control" name="role" id="role" value="{{ old('role') }}" placeholder="Contoh: Kasir" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="section-title mb-0"><span>Access / Permissions</span></h4>
                                    <button type="button" class="btn btn-secondary btn-sm" id="selectAllPermissions">Select All</button>
                                </div>
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
                        </div>
                    </div>
                    <div class="nexora-form-actions mt-3">
                        <button type="submit" class="btn btn-primary">Save Role</button>
                        <a href="{{ url('admin-panel/roles/listing') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')


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
