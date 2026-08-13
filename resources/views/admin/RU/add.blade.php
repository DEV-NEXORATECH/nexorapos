@extends('admin.layout.app')

@section('title', 'Role Permission')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Assign Roles</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
            <div>
                <div>
                    <div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-folder"></i> Assign Role</h5>
                    

                        <form action="{{ url('admin-panel/user-role/add') }}" method="POST">
                            @csrf
                                                <div class="card-body p-0 mt-3">

                                {{-- Select Role --}}
                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="" selected>Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Permission Selector --}}
                                <div class="form-group">
                                    <label>Select Roles</label>
                                    <div class="input-group mb-2">
                                        <select id="permissionSelect" class="form-control" name="role_id[]">
                                            <option value="">-- Choose a Role --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->role }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="addPermission">Add</button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">You can select multiple roles, they will appear below.</small>
                                </div>

                                {{-- Selected Permissions Box --}}
                                <div class="form-group">
                                    <label>Selected Roles:</label>
                                    <div id="selectedPermissions" class="p-2 border rounded bg-light" style="min-height:50px;">
                                        <p class="text-muted m-0" id="noPermissionText">No roles selected yet</p>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="nexora-form-actions mt-3">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <a href="{{ url('admin-panel/user-role/listing') }}" class="btn btn-danger">Cancel</a>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS to handle adding/removing permissions --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const permissionSelect = document.getElementById('permissionSelect');
        const addPermissionBtn = document.getElementById('addPermission');
        const selectedPermissionsDiv = document.getElementById('selectedPermissions');
        const noPermissionText = document.getElementById('noPermissionText');

        addPermissionBtn.addEventListener('click', function() {
            const selectedValue = permissionSelect.value;
            const selectedText = permissionSelect.options[permissionSelect.selectedIndex].text;

            if (selectedValue && !document.querySelector(`input[value="${selectedValue}"]`)) {
                noPermissionText.style.display = 'none';

                // Create chip
                const chip = document.createElement('span');
                chip.className = 'badge badge-info mr-1 mb-1';
                chip.style.fontSize = '14px';
                chip.innerHTML = `${selectedText} <span style="cursor:pointer;" class="ml-1 text-danger">&times;</span>`;

                // Hidden input to send with form
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'permissions[]';
                hiddenInput.value = selectedValue;

                // Remove chip on click
                chip.querySelector('span').addEventListener('click', function() {
                    chip.remove();
                    hiddenInput.remove();
                    if (!selectedPermissionsDiv.querySelector('input')) {
                        noPermissionText.style.display = 'block';
                    }
                });

                selectedPermissionsDiv.appendChild(chip);
                selectedPermissionsDiv.appendChild(hiddenInput);
            }
        });
    });
</script>
@endsection
