@extends('admin.layout.app')

@section('title', 'Perbarui Mutasi Stok - NexoraPOS')

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
                        <h3 class="nexora-page-header-title">Perbarui Mutasi Stok</h3>
                        <p class="nexora-page-header-desc">Koreksi riwayat penyesuaian stok ID: <strong>#{{ $stock->id }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/stock/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <form action="{{ url('admin-panel/stock/update', $stock->id) }}" method="POST" id="stockForm">
                        @csrf

                        <div class="nexora-form-card mb-4">
                            <div class="nexora-form-card-head">
                                <div class="nexora-form-card-icon is-navy">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div>
                                    <span class="nexora-form-card-eyebrow">PENYESUAIAN STOK</span>
                                    <h4 class="nexora-form-card-title">Rincian Mutasi Barang</h4>
                                    <p class="nexora-form-card-subtitle">Perbarui data mutasi kuantitas stok barang.</p>
                                </div>
                            </div>
                            <div class="nexora-form-card-body">
                                <div class="row">
                                    @include('admin.partials.company-selector')
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="store_id" class="nexora-form-label">Toko / Outlet <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap select-wrap">
                                                <i class="fas fa-store nexora-input-icon"></i>
                                                <select name="store_id" id="store_id" class="form-control select2" required>
                                                    @foreach ($stores as $store)
                                                        <option value="{{ $store->id }}" {{ old('store_id', $stock->store_id) == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('store_id')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="type" class="nexora-form-label">Tipe Mutasi <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap select-wrap">
                                                <i class="fas fa-random nexora-input-icon"></i>
                                                <select name="type" id="type" class="form-control select2" required>
                                                    <option value="in" {{ old('type', $stock->type) === 'in' ? 'selected' : '' }}>Stok Masuk (Stock In)</option>
                                                    <option value="out" {{ old('type', $stock->type) === 'out' ? 'selected' : '' }}>Stok Keluar (Stock Out)</option>
                                                </select>
                                            </div>
                                            @error('type')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="product_id" class="nexora-form-label">Pilih Produk <span class="text-danger">*</span></label>
                                    <div class="nexora-input-wrap select-wrap">
                                        <i class="fas fa-box nexora-input-icon"></i>
                                        <select name="product_id" id="product_id" class="form-control select2" required>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ old('product_id', $stock->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('product_id')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="product_variant_id" class="nexora-form-label">Varian Produk</label>
                                            <div class="nexora-input-wrap select-wrap">
                                                <i class="fas fa-layer-group nexora-input-icon"></i>
                                                <select name="product_variant_id" id="product_variant_id" class="form-control select2">
                                                    <option value="">-- Tanpa Varian (Produk Standar) --</option>
                                                    @foreach ($variants as $var)
                                                        <option value="{{ $var->id }}" {{ old('product_variant_id', $stock->product_variant_id) == $var->id ? 'selected' : '' }}>{{ $var->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('product_variant_id')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="quantity" class="nexora-form-label">Jumlah Unit (Qty) <span class="text-danger">*</span></label>
                                            <div class="nexora-input-wrap">
                                                <i class="fas fa-sort-numeric-up-alt nexora-input-icon"></i>
                                                <input type="number" min="1" step="1" class="form-control" name="quantity" id="quantity" value="{{ old('quantity', $stock->quantity) }}" required>
                                            </div>
                                            @error('quantity')
                                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="nexora-form-action-bar">
                            <a href="{{ url('admin-panel/stock/listing') }}" class="btn btn-outline-secondary btn-round">
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
