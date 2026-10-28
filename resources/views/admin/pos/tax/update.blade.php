@extends('admin.layout.app')

@section('title', 'Perbarui Pajak - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; PERPAJAKAN</span>
                        <h3 class="nexora-page-header-title">Perbarui Pajak</h3>
                        <p class="nexora-page-header-desc">Edit rincian nama atau persentase tarif pajak: <strong>{{ $tax->name }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/tax/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <form action="{{ url('admin-panel/tax/update', $tax->id) }}" method="POST" id="taxForm">
                        @csrf

                        <div class="nexora-form-card mb-4">
                            <div class="nexora-form-card-head">
                                <div class="nexora-form-card-icon is-navy">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div>
                                    <span class="nexora-form-card-eyebrow">SKEMA PAJAK</span>
                                    <h4 class="nexora-form-card-title">Informasi Tarif Pajak</h4>
                                    <p class="nexora-form-card-subtitle">Perbarui nilai persentase atau nama tarif pajak.</p>
                                </div>
                            </div>
                            <div class="nexora-form-card-body">
                                <div class="row">
                                    @include('admin.partials.company-selector')
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="nexora-form-label">Nama Pajak <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-tag nexora-input-icon"></i>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $tax->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="rate" class="nexora-form-label">Persentase Pajak (%) <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-percent nexora-input-icon"></i>
                                        <input type="number" min="0" max="100" step="0.01" class="form-control" name="rate" id="rate" value="{{ old('rate', $tax->rate) }}" required>
                                    </div>
                                    <small class="form-text text-muted">Masukkan angka persentase tanpa tanda persen.</small>
                                    @error('rate')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="nexora-form-action-bar">
                            <a href="{{ url('admin-panel/tax/listing') }}" class="btn btn-outline-secondary btn-round">
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
