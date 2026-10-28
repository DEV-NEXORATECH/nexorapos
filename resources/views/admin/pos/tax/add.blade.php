@extends('admin.layout.app')

@section('title', 'Tambah Pajak Baru - NexoraPOS')

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
                        <h3 class="nexora-page-header-title">Tambah Pajak Baru</h3>
                        <p class="nexora-page-header-desc">Daftarkan jenis tarif pajak baru untuk operasional toko dan transaksi POS.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/tax/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/tax/add') }}" method="POST" id="taxForm">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">SKEMA PAJAK</span>
                            <h4 class="nexora-form-card-title">Informasi Tarif Pajak</h4>
                            <p class="nexora-form-card-subtitle">Tentukan nama dan persentase tarif pajak yang akan diterapkan pada transaksi kasir.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Pajak <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-tag input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" placeholder="Misal: PPN 11%, Service Charge, PB1..." value="{{ old('name') }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Persentase Pajak (%) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-percent input-icon"></i>
                                    <input type="number" min="0" max="100" step="0.01" class="form-control nexora-input" name="rate" id="rate" placeholder="Misal: 11.00" value="{{ old('rate') }}" required>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Masukkan angka persentase tanpa tanda persen (contoh: 11 untuk 11%).</small>
                                @error('rate')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/tax/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Pajak
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
