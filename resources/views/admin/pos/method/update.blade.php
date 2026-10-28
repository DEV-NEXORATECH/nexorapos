@extends('admin.layout.app')

@section('title', 'Perbarui Metode Pembayaran - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; TRANSAKSI</span>
                        <h3 class="nexora-page-header-title">Perbarui Metode Pembayaran</h3>
                        <p class="nexora-page-header-desc">Edit nama channel pembayaran: <strong>{{ $method->name }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/payment-methods/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <form action="{{ url('admin-panel/payment-methods/update', $method->id) }}" method="POST" id="methodForm">
                        @csrf

                        <div class="nexora-form-card mb-4">
                            <div class="nexora-form-card-head">
                                <div class="nexora-form-card-icon is-navy">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div>
                                    <span class="nexora-form-card-eyebrow">CHANNEL PEMBAYARAN</span>
                                    <h4 class="nexora-form-card-title">Rincian Metode Pembayaran</h4>
                                    <p class="nexora-form-card-subtitle">Perbarui nama channel penerimaan transaksi.</p>
                                </div>
                            </div>
                            <div class="nexora-form-card-body">
                                <div class="row">
                                    @include('admin.partials.company-selector')
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="nexora-form-label">Nama Metode Pembayaran <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap">
                                        <i class="fas fa-credit-card nexora-input-icon"></i>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $method->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="nexora-form-action-bar">
                            <a href="{{ url('admin-panel/payment-methods/listing') }}" class="btn btn-outline-secondary btn-round">
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
