@extends('admin.layout.app')

@section('title', 'Add New User')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="nexora-page-head">
                <div>
                    <h4 class="page-title mb-1">Add User</h4>
                    <p class="card-category mb-0">Buat user dan assign role sesuai company access.</p>
                </div>
                <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/users/add') }}" method="POST" enctype="multipart/form-data" class="nexora-form">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">User Information</div>
                    </div>
                    <div class="card-body">
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

                            @if(auth()->user()?->isSuperAdmin())
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_id">Company *</label>
                                        <select name="company_id" id="company_id" class="form-control select2" required>
                                            <option value="">Select company</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }} - {{ $company->company_code }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Company code disetting admin, user hanya diberi akses.</small>
                                        @error('company_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
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

                <div class="card mt-3">
                    <div class="card-header">
                        <div class="card-title">Security & Profile</div>
                    </div>
                    <div class="card-body">
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
                    <div class="card-footer nexora-form-actions">
                        <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
