@extends('admin.layout.app')

@section('title', 'Tambah Produk Baru - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; TAMBAH PRODUK</span>
                        <h3 class="nexora-page-header-title">Tambah Produk Baru</h3>
                        <p class="nexora-page-header-desc">Daftarkan item barang dagangan baru ke dalam katalog inventaris kasir POS.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/products/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Katalog
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/products/add') }}" method="POST">
                @csrf

                <!-- CARD 1: Informasi Dasar Produk -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">IDENTITAS PRODUK</span>
                            <h4 class="nexora-form-card-title">Informasi & Barcode Produk</h4>
                            <p class="nexora-form-card-subtitle">Nama barang, kategori, serta kode SKU unik untuk pelacakan transaksi.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector')

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nama Produk <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-cube input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Kopi Susu Gula Aren 250ml" required>
                                </div>
                                @error('name')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Kategori Produk <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-tag input-icon"></i>
                                    <select name="category_id" id="category_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Kode SKU (Stock Keeping Unit)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-barcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="sku" id="sku" value="{{ old('sku') }}" placeholder="Contoh: BEV-KOP-01">
                                </div>
                                @error('sku')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Barcode / EAN</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-qrcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik kode barcode">
                                </div>
                                @error('barcode')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Penetapan Harga & Pajak -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">STRUKTUR HARGA</span>
                            <h4 class="nexora-form-card-title">Penetapan Biaya Modal, Harga Jual & Pajak</h4>
                            <p class="nexora-form-card-subtitle">Tentukan harga pokok pembelian, harga jual konsumen, serta tarif pajak berlaku.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Harga Pokok Modal (Cost Price)</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-coins input-icon"></i>
                                    <input type="number" step="0.01" min="0" class="form-control nexora-input" name="cost" id="cost" value="{{ old('cost') }}" placeholder="0">
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Biaya modal per unit barang.</small>
                                @error('cost')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Harga Jual Kasir (Selling Price) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-rupiah-sign input-icon"></i>
                                    <input type="number" step="0.01" min="0" class="form-control nexora-input" name="price" id="price" value="{{ old('price') }}" placeholder="0" required>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Harga resmi saat ditransaksikan di kasir.</small>
                                @error('price')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="nexora-input-label">Tarif Pajak (Tax) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-percent input-icon"></i>
                                    <select name="tax_id" id="tax_id" class="form-control nexora-input select2" required>
                                        <option value="">-- Pilih Pajak --</option>
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->id }}" @selected(old('tax_id') == $tax->id)>{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('tax_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Manajemen Stok & Inventaris -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-amber">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">INVENTARIS & STOK</span>
                            <h4 class="nexora-form-card-title">Pelacakan Persediaan Gudang</h4>
                            <p class="nexora-form-card-subtitle">Atur apakah kuantitas barang ini dipantau secara otomatis saat terjual.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Lacak Stok Otomatis (Track Stock) <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-boxes-stacked input-icon"></i>
                                    <select name="track_stock" id="track_stock" class="form-control nexora-input select2" required>
                                        <option value="1" @selected(old('track_stock', '1') === '1')>Ya (Kurangi stok otomatis saat transaksi)</option>
                                        <option value="0" @selected(old('track_stock') === '0')>Tidak (Stok tidak terbatas / Jasa)</option>
                                    </select>
                                </div>
                                @error('track_stock')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/products/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Produk Baru
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
