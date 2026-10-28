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

            {{-- Form Card --}}
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
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
                                    <p class="nexora-form-card-subtitle">Perbarui aturan potongan promosi.</p>
                                </div>
                            </div>
                            <div class="nexora-form-card-body">
                                <div class="row">
                                    @include('admin.partials.company-selector')
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="nexora-form-label">Nama Diskon / Promosi <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-tag nexora-input-icon"></i>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $discount->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="type" class="nexora-form-label">Tipe Diskon <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap select-wrap">
                                                <i class="fas fa-sliders-h nexora-input-icon"></i>
                                                <select name="type" id="type" class="form-control select2">
                                                    <option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                                    <option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                                                </select>
                                            </div>
                                            @error('type')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="value" class="nexora-form-label">Nilai Diskon <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap">
                                                <i class="fas fa-coins nexora-input-icon"></i>
                                                <input type="number" min="0" step="0.01" class="form-control" name="value" id="value" value="{{ old('value', $discount->value) }}" required>
                                            </div>
                                            @error('value')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="nexora-form-action-bar">
                            <a href="{{ url('admin-panel/discounts/listing') }}" class="btn btn-outline-secondary btn-round">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-round">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }
    });
</script>
@endsection
