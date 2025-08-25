@extends('admin.layout.app')

@section('title', 'Update Role Permissions')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Update Permissions for Role: {{ $role->role }}</h4>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit Permissions</div>
                </div>

                <form action="{{ url('admin-panel/role-permissions/update' , $role->id) }}" method="POST">
                    @csrf


                    <div class="card-body">

                        {{-- Role (Read-only display) --}}
                        <div class="form-group">

                            <label>Role</label>

                            <select name="role_id" id="" class="form-control">
                                <option value="{{ $role->id }}" selected title="Already Registered">{{ $role->role }}</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->role }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Permission Selector --}}
                        <div class="form-group">
                            <label>Select Permissions</label>
                            <div class="input-group mb-2">
                                <select id="permissionSelect" class="form-control">
                                    <option value="" disabled selected>Select permission</option>
                                    @foreach ($permissions as $permission)
                                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="addPermission">Add</button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Selected permissions will appear below. You can remove them before submitting.</small>
                        </div>

                        {{-- Selected Permissions --}}
                        <div class="form-group">
                            <label>Selected Permissions:</label>
                            <div id="selectedPermissions" class="p-2 border rounded bg-light" style="min-height:50px;">
                                @forelse ($role->permissions as $permission)
                                    <span class="badge badge-info mr-2 mb-2 selected-permission" data-id="{{ $permission->id }}">
                                        {{ $permission->name }}
                                        <span style="cursor:pointer;" class="ml-1 text-danger remove-permission">&times;</span>
                                        <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                                    </span>
                                @empty
                                    <p class="text-muted m-0" id="noPermissionText">No permissions selected yet</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ url('admin-panel/role-permissions/listing') }}" class="btn btn-danger">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Adding & Removing --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const permissionSelect = document.getElementById('permissionSelect');
        const addPermissionBtn = document.getElementById('addPermission');
        const selectedPermissionsDiv = document.getElementById('selectedPermissions');
        const noPermissionText = document.getElementById('noPermissionText');

        // Add Permission
        addPermissionBtn.addEventListener('click', function () {
            const selectedValue = permissionSelect.value;
            const selectedText = permissionSelect.options[permissionSelect.selectedIndex].text;

            if (selectedValue && !document.querySelector(`input[name="permissions[]"][value="${selectedValue}"]`)) {
                if (noPermissionText) noPermissionText.style.display = 'none';

                const badge = document.createElement('span');
                badge.className = 'badge badge-info mr-2 mb-2 selected-permission';
                badge.setAttribute('data-id', selectedValue);
                badge.innerHTML = `
                    ${selectedText}
                    <span style="cursor:pointer;" class="ml-1 text-danger remove-permission">&times;</span>
                `;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'permissions[]';
                input.value = selectedValue;

                badge.appendChild(input);
                selectedPermissionsDiv.appendChild(badge);
            }
        });

        // Remove Permission (event delegation)
        selectedPermissionsDiv.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-permission')) {
                const badge = e.target.closest('.selected-permission');
                badge.remove();

                if (!selectedPermissionsDiv.querySelector('input[name="permissions[]"]')) {
                    if (noPermissionText) noPermissionText.style.display = 'block';
                }
            }
        });
    });
</script>
@endsection
