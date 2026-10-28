@extends('admin.layout.app')

@section('title', 'Perbarui Data Pelanggan - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; MEMBERSHIP</span>
                        <h3 class="nexora-page-header-title">Perbarui Data Pelanggan</h3>
                        <p class="nexora-page-header-desc">Edit profil data pelanggan: <strong>{{ $customer->name }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/customer/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/customer/update', $customer->id) }}" method="POST" id="customerForm">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">IDENTITAS PELANGGAN</span>
                            <h4 class="nexora-form-card-title">Profil & Kontak Pelanggan</h4>
                            <p class="nexora-form-card-subtitle">Perbarui data kontak dan domisili pelanggan.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Lengkap Pelanggan <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" value="{{ old('name', $customer->name) }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" required>
                                </div>
                                @error('phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Alamat Email</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" class="form-control nexora-input" name="email" id="email" value="{{ old('email', $customer->email) }}">
                                </div>
                                @error('email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Alamat Domisili</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="address" id="address" value="{{ old('address', $customer->address) }}">
                                </div>
                                @error('address')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/customer/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
