@extends('admin.layout.app')

@section('title', 'Tambah Metode Pembayaran - NexoraPOS')

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
                        <h3 class="nexora-page-header-title">Tambah Metode Pembayaran</h3>
                        <p class="nexora-page-header-desc">Daftarkan channel penerimaan kasir POS baru untuk outlet toko.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/payment-methods/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/payment-methods/add') }}" method="POST" id="methodForm">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">CHANNEL PEMBAYARAN</span>
                            <h4 class="nexora-form-card-title">Rincian Metode Pembayaran</h4>
                            <p class="nexora-form-card-subtitle">Tentukan nama channel pembayaran yang akan tampil pada mesin kasir.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Metode Pembayaran <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-credit-card input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" placeholder="Misal: Cash / Tunai, QRIS BCA, Debit Mandiri, Transfer BRI..." value="{{ old('name') }}" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/payment-methods/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Metode
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
