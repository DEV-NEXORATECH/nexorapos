@extends('admin.layout.app')

@section('title', 'Update Role')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Update Roles for User: {{ $user->name }}</h4>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit Roles</div>
                </div>

                <form action="{{ url('admin-panel/user-role/update', $user->id) }}" method="POST">
                    @csrf

                    <div class="card-body">

                        {{-- User Selector (Wrong in original code: allowed selecting other users, may override $user variable) --}}
                        {{-- Fixed: Use hidden input instead of dropdown if updating a single user --}}
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="form-group">
                            <label>User</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>

                        {{-- Role Selector --}}
                        <div class="form-group">
                            <label>Select Roles</label>
                            <div class="input-group mb-2">
                                <select id="roleSelect" class="form-control">
                                    <option value="" disabled selected>Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="addRole">Add</button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Selected roles will appear below. You can remove them before submitting.</small>
                        </div>

                        {{-- Selected Roles --}}
                        <div class="form-group">
                            <label>Selected Roles:</label>
                            <div id="selectedRoles" class="p-2 border rounded bg-light" style="min-height:50px;">
                                @forelse ($user->roles as $role)
                                    <span class="badge badge-info mr-2 mb-2 selected-role" data-id="{{ $role->id }}">
                                        {{ $role->role }}
                                        <span style="cursor:pointer;" class="ml-1 text-danger remove-role">&times;</span>
                                        {{-- Wrong in original code: name was inconsistent with JS (permissions[]) --}}
                                        {{-- Fixed: use role_id[] consistently --}}
                                        <input type="hidden" name="role_id[]" value="{{ $role->id }}">
                                    </span>
                                @empty
                                    <p class="text-muted m-0" id="noRoleText">No Role selected yet</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ url('admin-panel/user-role/listing') }}" class="btn btn-danger">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Adding & Removing Roles --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('roleSelect');
    const addRoleBtn = document.getElementById('addRole');
    const selectedRolesDiv = document.getElementById('selectedRoles');
    const noRoleText = document.getElementById('noRoleText');

    // Add Role
    addRoleBtn.addEventListener('click', function () {
        const selectedValue = roleSelect.value;
        const selectedText = roleSelect.options[roleSelect.selectedIndex].text;

        if (selectedValue && !document.querySelector(`input[name="role_id[]"][value="${selectedValue}"]`)) {
            if (noRoleText) noRoleText.style.display = 'none';

            const badge = document.createElement('span');
            badge.className = 'badge badge-info mr-2 mb-2 selected-role';
            badge.setAttribute('data-id', selectedValue);
            badge.innerHTML = `
                ${selectedText}
                <span style="cursor:pointer;" class="ml-1 text-danger remove-role">&times;</span>
            `;

            // Fixed: use name="role_id[]" to match existing roles
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'role_id[]';
            input.value = selectedValue;

            badge.appendChild(input);
            selectedRolesDiv.appendChild(badge);
        }
    });

    // Remove Role (Event Delegation)
    selectedRolesDiv.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-role')) {
            const badge = e.target.closest('.selected-role');
            badge.remove();

            if (!selectedRolesDiv.querySelector('input[name="role_id[]"]')) {
                if (noRoleText) noRoleText.style.display = 'block';
            }
        }
    });
});
</script>
@endsection
