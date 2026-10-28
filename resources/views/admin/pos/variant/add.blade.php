@extends('admin.layout.app')

@section('title', 'Tambah Varian Produk - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; TAMBAH VARIAN</span>
                        <h3 class="nexora-page-header-title">Tambah Varian Produk Baru</h3>
                        <p class="nexora-page-header-desc">Hubungkan variasi ukuran, warna, atau model baru dengan item produk induk.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/variants/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/variants/add') }}" method="POST">
                @csrf

                <!-- CARD 1: Induk & Nama Varian -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-link"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">RELASI PRODUK</span>
                            <h4 class="nexora-form-card-title">Pilih Produk Induk & Nama Varian</h4>
                            <p class="nexora-form-card-subtitle">Pilih produk utama yang menaungi variasi ini serta nama spesifik variannya.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Pilih Produk Induk <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-box input-icon"></i>
                                    <select name="product_id" id="product_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Produk Induk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('product_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Varian <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-cube input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Ukuran L / Rasa Matcha / 500ml" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">SKU Varian</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-barcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="sku" id="sku" value="{{ old('sku') }}" placeholder="Contoh: BEV-KOP-L">
                                </div>
                                @error('sku')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Barcode Varian</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-qrcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Barcode unik varian">
                                </div>
                                @error('barcode')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Penetapan Harga Varian -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">STRUKTUR HARGA</span>
                            <h4 class="nexora-form-card-title">Harga Modal & Harga Jual Varian</h4>
                            <p class="nexora-form-card-subtitle">Tetapkan nilai nominal harga jual khusus untuk opsi varian ini.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Harga Modal Varian (Cost)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-coins input-icon"></i>
                                    <input type="number" step="0.01" min="0" class="form-control nexora-input" name="cost" id="cost" value="{{ old('cost') }}" placeholder="0">
                                </div>
                                @error('cost')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Harga Jual Kasir (Selling Price) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-rupiah-sign input-icon"></i>
                                    <input type="number" step="0.01" min="0" class="form-control nexora-input" name="price" id="price" value="{{ old('price') }}" placeholder="0" required>
                                </div>
                                @error('price')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/variants/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Varian Baru
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
