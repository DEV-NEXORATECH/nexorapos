@extends('admin.layout.app')

@section('title', 'Update User')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-user-edit"></i> Edit User</h4>
                <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/users/update', $user->id) }}" method="POST" enctype="multipart/form-data" class="nexora-form">
                @csrf
                <div class="nexora-control-card is-success mb-4">
                    <h5><i class="fas fa-user"></i> User Information</h5>
                    <div class="card-body p-0 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone *</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role *</label>
                                    <select name="role_id" id="role" class="form-control select2" required>
                                        <option value="">Select role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
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
                                                <option value="{{ $company->id }}" @selected(old('company_id', $user->company_id) == $company->id)>{{ $company->name }} - {{ $company->company_code }}</option>
                                            @endforeach
                                        </select>
                                        @error('company_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="nexora-control-card is-warning mb-4">
                    <h5><i class="fas fa-shield-alt"></i> Security & Profile</h5>
                    <div class="card-body p-0 mt-3">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Current Image</label>
                                    <div class="nexora-image-preview">
                                        @if($user->profile_image)
                                            <img src="{{ asset('admin/profile_images/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                        @else
                                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    <input type="file" name="image" id="profile_image" class="form-control" accept="image/png,image/jpeg,image/jpg,image/gif">
                                    @error('image')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                                    @error('password')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Password Confirmation</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                    @error('password_confirmation')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nexora-form-actions mt-3">
                        <a href="{{ url('admin-panel/users/listing') }}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
