@extends('admin.layout.app')

@section('title', 'Input Mutasi Stok - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; PERSEDIAAN</span>
                        <h3 class="nexora-page-header-title">Input Mutasi Stok</h3>
                        <p class="nexora-page-header-desc">Catat pergerakan penambahan, pengurangan, atau penyesuaian stok per outlet.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/stock/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/stock/add') }}" method="POST" id="stockForm">
                @csrf

                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">PENYESUAIAN STOK</span>
                            <h4 class="nexora-form-card-title">Rincian Mutasi Barang</h4>
                            <p class="nexora-form-card-subtitle">Pilih lokasi outlet, barang, serta tipe penyesuaian kuantitas.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Toko / Outlet <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-store input-icon"></i>
                                    <select name="store_id" id="store_id" class="form-control nexora-input select2" required>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('store_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Tipe Mutasi <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-random input-icon"></i>
                                    <select name="type" id="type" class="form-control nexora-input select2" required>
                                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stok Masuk (Stock In)</option>
                                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stok Keluar (Stock Out)</option>
                                    </select>
                                </div>
                                @error('type')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Pilih Produk <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-box input-icon"></i>
                                    <select name="product_id" id="product_id" class="form-control nexora-input select2" required>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('product_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Varian Produk</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-layer-group input-icon"></i>
                                    <select name="product_variant_id" id="product_variant_id" class="form-control nexora-input select2">
                                        <option value="">-- Tanpa Varian (Produk Standar) --</option>
                                        @foreach ($variants as $var)
                                            <option value="{{ $var->id }}" {{ old('product_variant_id') == $var->id ? 'selected' : '' }}>{{ $var->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('product_variant_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Jumlah Unit (Qty) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-sort-numeric-up-alt input-icon"></i>
                                    <input type="number" min="1" step="1" class="form-control nexora-input" name="quantity" id="quantity" placeholder="Misal: 10" value="{{ old('quantity') }}" required>
                                </div>
                                @error('quantity')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/stock/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Mutasi Stok
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
    });
</script>
@endsection
