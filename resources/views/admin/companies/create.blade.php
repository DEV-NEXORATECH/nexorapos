@extends('admin.layout.app')

@section('title', 'Add Company')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Create Company</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Set up a new SaaS tenant</div>
                        </div>
                        <form action="{{ url('admin-panel/companies/add') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <h5>Company Information</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="legal_name">Legal Name</label>
                                    <input type="text" class="form-control" id="legal_name" name="legal_name" value="{{ old('legal_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="company_code">Company Code</label>
                                    <input type="text" class="form-control" id="company_code" value="[Generated automatically after save]" disabled readonly>
                                    <small class="form-text text-muted">Kode akan di-generate otomatis sebagai identitas unik tenant.</small>
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                                </div>

                                <h5 class="mt-4">Contact & Legal</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="npwp">NPWP</label>
                                    <input type="text" class="form-control" id="npwp" name="npwp" value="{{ old('npwp') }}">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                </div>

                                <h5 class="mt-4">Initial Company Admin</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="admin_name">Admin Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" value="{{ old('admin_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_email">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="{{ old('admin_email') }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_password">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password">
                                </div>

                                <h5 class="mt-4">Status</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="is_active">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="is_active" name="is_active" required>
                                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">Create Company</button>
                                <a href="{{ url('admin-panel/companies/listing') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
