@extends('admin.layout.app')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Header Card --}}
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <span class="nexora-page-header-eyebrow">Transaksi & Kasir</span>
                        <h4 class="nexora-page-header-title">Edit Pembayaran</h4>
                        <p class="nexora-page-header-desc">Perbarui data catatan pembayaran transaksi.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/payments/listing') }}" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; font-size: 12.5px; padding: 9px 18px; border: 1px solid #E2E8F0; color: #475569;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('failed') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Form Card --}}
            <form action="{{ url('admin-panel/payments/update', $payment->id) }}" method="POST">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">DETAIL PEMBAYARAN</span>
                            <h4 class="nexora-form-card-title">Informasi Transaksi & Pembayaran</h4>
                            <p class="nexora-form-card-subtitle">Perbarui catatan penerimaan kas dan metode bayar transaksi pelanggan.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector', ['selectedCompanyId' => $payment->company_id])

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Pilih Penjualan / Invoice <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-receipt input-icon"></i>
                                    <select name="sale_id" id="sale_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Transaksi Penjualan --</option>
                                        @foreach ($sales as $sale)
                                            <option value="{{ $sale->id }}" {{ (old('sale_id', $payment->sale_id) == $sale->id) ? 'selected' : '' }}>
                                                {{ $sale->invoice_number ?: ('#' . $sale->id) }} - Total: Rp {{ number_format((float)$sale->total, 0, ',', '.') }} ({{ strtoupper($sale->status ?? 'COMPLETED') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('sale_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-credit-card input-icon"></i>
                                    <select name="method_id" id="method_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Metode Pembayaran --</option>
                                        @foreach ($methods as $met)
                                            <option value="{{ $met->id }}" {{ (old('method_id', $payment->method_id) == $met->id) ? 'selected' : '' }}>
                                                {{ $met->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('method_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jumlah Pembayaran (Rp) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-coins input-icon"></i>
                                    <input type="number" step="0.01" name="amount" id="amount" class="form-control nexora-input" placeholder="Contoh: 50000" value="{{ old('amount', $payment->amount) }}" required>
                                </div>
                                @error('amount')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Referensi / Catatan <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-hashtag input-icon"></i>
                                    <input type="text" name="reference" id="reference" class="form-control nexora-input" placeholder="Contoh: REF-PAY-001" value="{{ old('reference', $payment->reference) }}" required>
                                </div>
                                @error('reference')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/payments/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Perbarui Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
