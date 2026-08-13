@extends('admin.layout.app')

@section('title', 'Add New User')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-user-plus"></i> Add User</h4>
                <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/users/add') }}" method="POST" enctype="multipart/form-data" class="nexora-form">
                @csrf
                <div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-user"></i> User Information</h5>
                    <div class="card-body p-0 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Nama user" required>
                                    @error('name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="email@company.com" required>
                                    @error('email')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone *</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="Nomor telepon" required>
                                    @error('phone')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role_id">Role *</label>
                                    <select name="role_id" id="role_id" class="form-control select2" required>
                                        <option value="">Select role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                                {{ $role->role }}
                                                @if(auth()->user()?->isSuperAdmin() && $role->company)
                                                    - {{ $role->company->company_code }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            @include('admin.partials.company-selector')
                            @if(auth()->user()?->isSuperAdmin())
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Privilege</label>
                                        <label class="nexora-permission-check">
                                            <input type="checkbox" name="is_super_admin" value="1" @checked(old('is_super_admin'))>
                                            <span>Give Super Admin access</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="nexora-control-card is-warning mb-4">
                    <h5><i class="fas fa-shield-alt"></i> Security & Profile</h5>
                    <div class="card-body p-0 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                    @error('password')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Password Confirmation *</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                                    @error('password_confirmation')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    <input type="file" name="image" id="profile_image" class="form-control" accept="image/png,image/jpeg,image/jpg,image/gif">
                                    @error('image')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nexora-form-actions mt-3">
                        <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
