@extends('admin.layout.app')

@section('title', 'Company Settings')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="nexora-company-settings-head">
                <div>
                    <span>Company Profile</span>
                    <h4 class="page-title">Company Settings</h4>
                    <p>Atur identitas perusahaan untuk struk, invoice, dan tampilan POS.</p>
                </div>
                <div class="nexora-company-code-pill">
                    <small>Company Code</small>
                    <strong>{{ $company->company_code }}</strong>
                </div>
            </div>

            <form action="{{ url('admin-panel/settings/company/update') }}" method="POST" enctype="multipart/form-data" class="nexora-company-settings">
                @csrf

                <section class="nexora-company-logo-panel">
                    <div class="nexora-company-logo-preview">
                        @if($company->logo)
                            <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}">
                        @else
                            <span>{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h5>Logo Perusahaan</h5>
                        <p>Logo ini dipakai untuk header aplikasi, struk, dan invoice.</p>
                        <input type="file" name="logo" class="form-control-file" accept="image/png,image/jpeg,image/webp">
                        @error('logo')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                </section>

                <section class="nexora-company-form-panel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nama Brand / Toko</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Nama Legal / PT</label>
                            <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $company->legal_name) }}" placeholder="PT Nama Perusahaan">
                            @error('legal_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>NPWP</label>
                            <input type="text" name="npwp" class="form-control" value="{{ old('npwp', $company->npwp) }}" placeholder="00.000.000.0-000.000">
                            @error('npwp') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email Perusahaan</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}" placeholder="billing@company.com">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Kota</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}">
                            @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $company->postal_code) }}">
                            @error('postal_code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Alamat Lengkap</label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Alamat untuk invoice dan struk">{{ old('address', $company->address) }}</textarea>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="nexora-company-settings-actions">
                        <button type="submit" class="btn btn-primary">Save Company Settings</button>
                    </div>
                </section>
            </form>
        </div>
    </div>
</div>
@endsection
