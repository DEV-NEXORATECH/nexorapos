@extends('admin.layout.app')

@section('title', 'Perbarui Diskon - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; PROMOSI</span>
                        <h3 class="nexora-page-header-title">Perbarui Diskon</h3>
                        <p class="nexora-page-header-desc">Edit nama, jenis, atau nilai diskon: <strong>{{ $discount->name }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/discounts/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/discounts/update', $discount->id) }}" method="POST" id="discountForm">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">SKEMA DISKON</span>
                            <h4 class="nexora-form-card-title">Informasi Diskon</h4>
                            <p class="nexora-form-card-subtitle">Perbarui aturan potongan promosi kasir.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector', ['selectedCompanyId' => $discount->company_id])

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Diskon / Promosi <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-tag input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" value="{{ old('name', $discount->name) }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Tipe Diskon <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-sliders-h input-icon"></i>
                                    <select name="type" id="type" class="form-control nexora-input select2">
                                        <option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                                    </select>
                                </div>
                                @error('type')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nilai Diskon <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-coins input-icon"></i>
                                    <input type="number" min="0" step="0.01" class="form-control nexora-input" name="value" id="value" value="{{ old('value', $discount->value) }}" required>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Untuk tipe persentase masukkan angka (contoh: 10), untuk nominal masukkan rupiah (contoh: 15000).</small>
                                @error('value')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/discounts/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
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
