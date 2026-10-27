@extends('admin.layout.app')

@section('title', 'Profil & Identitas Perusahaan - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> PENGATURAN SISTEM &bull; PROFIL TOKO</span>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h3 class="nexora-page-header-title mb-0">Profil Perusahaan & Toko</h3>
                            <span class="badge badge-light px-2 py-1" style="font-weight: 700; color: #0058A6; font-size: 11.5px;">{{ $company->company_code }}</span>
                        </div>
                        <p class="nexora-page-header-desc">Atur logo, nama legal, NPWP, kontak dan alamat resmi yang tercetak pada nota struk.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/dashboard') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/settings/company/update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- CARD 1: Logo Brand Perusahaan -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-image"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">BRAND ASSETS</span>
                            <h4 class="nexora-form-card-title">Logo & Identitas Visual</h4>
                            <p class="nexora-form-card-subtitle">Logo resmi toko akan otomatis disematkan pada header struk kasir, invoice PDF, dan email transaksi.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="rounded-xl d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: #F1F5F9; border: 2px dashed #CBD5E1; border-radius: 16px; overflow: hidden;">
                                    @if($company->logo)
                                        <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}" style="width: 100%; height: 100%; object-fit: contain; padding: 6px;">
                                    @else
                                        <span style="font-size: 32px; font-weight: 800; color: #0058A6;">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label class="nexora-input-label mb-1">Unggah Logo Baru</label>
                                <input type="file" name="logo" class="form-control-file" accept="image/png,image/jpeg,image/webp">
                                <div class="text-muted mt-1" style="font-size: 11.5px;">Gunakan file PNG transparan beresolusi tinggi (rekomendasi: 512x512px). Maksimal 2MB.</div>
                                @error('logo')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Identitas Resmi & Kontak -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">LEGALITAS & LOKASI</span>
                            <h4 class="nexora-form-card-title">Informasi Legal, Kontak & Alamat</h4>
                            <p class="nexora-form-card-subtitle">Data resmi perusahaan yang akan tertera sebagai identitas penjual pada dokumen POS.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Brand / Toko <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-store input-icon"></i>
                                    <input type="text" name="name" class="form-control nexora-input" value="{{ old('name', $company->name) }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Badan Usaha / PT / CV</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-building input-icon"></i>
                                    <input type="text" name="legal_name" class="form-control nexora-input" value="{{ old('legal_name', $company->legal_name) }}" placeholder="PT Nama Perusahaan Anda">
                                </div>
                                @error('legal_name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Pokok Wajib Pajak (NPWP)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-receipt input-icon"></i>
                                    <input type="text" name="npwp" class="form-control nexora-input" value="{{ old('npwp', $company->npwp) }}" placeholder="00.000.000.0-000.000">
                                </div>
                                @error('npwp')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Email Perusahaan</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="form-control nexora-input" value="{{ old('email', $company->email) }}" placeholder="billing@company.com">
                                </div>
                                @error('email')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Telepon Toko</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" name="phone" class="form-control nexora-input" value="{{ old('phone', $company->phone) }}">
                                </div>
                                @error('phone')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Kota / Wilayah</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-city input-icon"></i>
                                    <input type="text" name="city" class="form-control nexora-input" value="{{ old('city', $company->city) }}">
                                </div>
                                @error('city')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Kode Pos</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-envelopes-bulk input-icon"></i>
                                    <input type="text" name="postal_code" class="form-control nexora-input" value="{{ old('postal_code', $company->postal_code) }}">
                                </div>
                                @error('postal_code')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="nexora-input-label">Alamat Lengkap Outlet / Kantor</label>
                                <textarea name="address" class="form-control nexora-input" rows="4" style="height: auto; padding: 12px 14px; border-radius: 12px;" placeholder="Alamat lengkap yang tercetak di struk kasir">{{ old('address', $company->address) }}</textarea>
                                @error('address')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Profil Perusahaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
