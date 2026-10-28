@extends('admin.layout.app')

@section('title', 'Tambah Supplier Baru - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; DISTRIBUTOR</span>
                        <h3 class="nexora-page-header-title">Tambah Supplier Baru</h3>
                        <p class="nexora-page-header-desc">Daftarkan supplier baru untuk kebutuhan restok dan pengadaan inventaris toko.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/supplier/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <form action="{{ url('admin-panel/supplier/add') }}" method="POST" id="supplierForm">
                        @csrf

                        <div class="nexora-form-card mb-4">
                            <div class="nexora-form-card-head">
                                <div class="nexora-form-card-icon is-navy">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <span class="nexora-form-card-eyebrow">DATA VENDOR</span>
                                    <h4 class="nexora-form-card-title">Informasi & Kontak Supplier</h4>
                                    <p class="nexora-form-card-subtitle">Lengkapi profil nama perusahaan dan nomor kontak narahubung.</p>
                                </div>
                            </div>
                            <div class="nexora-form-card-body">
                                <div class="row">
                                    @include('admin.partials.company-selector')
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="nexora-form-label">Nama Supplier / PT / CV <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-building nexora-input-icon"></i>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Misal: PT Sumber Makmur Sentosa" value="{{ old('name') }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email" class="nexora-form-label">Email Supplier</label>
                                            <div class="nexora-input-wrap">
                                                <i class="fas fa-envelope nexora-input-icon"></i>
                                                <input type="email" class="form-control" name="email" id="email" placeholder="contoh@supplier.com" value="{{ old('email') }}">
                                            </div>
                                            @error('email')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="phone" class="nexora-form-label">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap">
                                                <i class="fas fa-phone nexora-input-icon"></i>
                                                <input type="text" class="form-control" name="phone" id="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                                            </div>
                                            @error('phone')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address" class="nexora-form-label">Alamat Lengkap</label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-map-marker-alt nexora-input-icon"></i>
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Jalan, Gedung, Nomor, Kota..." value="{{ old('address') }}">
                                    </div>
                                    @error('address')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="nexora-form-action-bar">
                            <a href="{{ url('admin-panel/supplier/listing') }}" class="btn btn-outline-secondary btn-round">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-round">
                                <i class="fas fa-save mr-1"></i> Simpan Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
