@extends('admin.layout.app')

@section('title', 'Perbarui Pembelian - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> TRANSAKSI & KASIR &bull; PENGADAAN</span>
                        <h3 class="nexora-page-header-title">Perbarui Pembelian</h3>
                        <p class="nexora-page-header-desc">Edit rincian faktur pengadaan: <strong>{{ $purchase->purchase_number ?: 'PUR-'.$purchase->id }}</strong></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/purchase/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 500;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('failed') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ url('admin-panel/purchase/update', $purchase->id) }}" method="POST" id="purchaseForm">
                @csrf

                <!-- Section 1: Dokumen Pembelian -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">DOKUMEN PENGADAAN</span>
                            <h4 class="nexora-form-card-title">Informasi Faktur & Supplier</h4>
                            <p class="nexora-form-card-subtitle">Perbarui rekanan vendor dan status pemrosesan pengadaan barang.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            @include('admin.partials.company-selector', ['selectedCompanyId' => $purchase->company_id])

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Supplier / Vendor <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-truck input-icon"></i>
                                    <select name="supplier_id" id="supplier_id" class="form-control nexora-input select2" required>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchase->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('supplier_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Toko / Lokasi Penerima</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-store input-icon"></i>
                                    <select name="store_id" id="store_id" class="form-control nexora-input select2">
                                        <option value="">-- Pilih Toko / Outlet --</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" @selected(old('store_id', $purchase->store_id ?? '') == $store->id)>{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('store_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Status Faktur Pembelian <span class="text-danger">*</span></label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-info-circle input-icon"></i>
                                    <select name="status" id="status" class="form-control nexora-input select2" required>
                                        <option value="PENDING" @selected(old('status', $purchase->status) == 'PENDING')>PENDING (Menunggu Kedatangan Barang)</option>
                                        <option value="RECEIVED" @selected(old('status', $purchase->status) == 'RECEIVED')>RECEIVED (Barang Diterima & Stok Otomatis Masuk)</option>
                                        <option value="CANCELLED" @selected(old('status', $purchase->status) == 'CANCELLED')>CANCELLED (Dibatalkan)</option>
                                    </select>
                                </div>
                                @error('status')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Nomor Referensi / No. Surat Jalan</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-barcode input-icon"></i>
                                    <input type="text" class="form-control nexora-input" name="reference_number" id="reference_number" value="{{ old('reference_number', $purchase->reference_number) }}" placeholder="Nomor surat jalan supplier">
                                </div>
                                @error('reference_number')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Rincian Barang Pembelian -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="nexora-form-card-icon is-navy mr-3">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">DAFTAR ITEM BARANG</span>
                                <h4 class="nexora-form-card-title">Barang yang Dibeli</h4>
                                <p class="nexora-form-card-subtitle">Perbarui item produk, kuantitas unit, dan harga beli (modal).</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addRow" style="border-radius: 8px; font-weight: 700; padding: 8px 16px;">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="nexora-form-card-body p-0">
                        <div class="table-responsive">
                            <table class="table nexora-data-table mb-0" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="min-width: 280px;">Produk Barang</th>
                                        <th style="width: 140px;">Kuantitas (Qty)</th>
                                        <th style="width: 200px;">Harga Beli Satuan (Rp)</th>
                                        <th style="width: 180px;" class="text-right">Subtotal (Rp)</th>
                                        <th style="width: 80px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchase->purchase_items as $idx => $item)
                                        <tr>
                                            <td>
                                                <select name="items[{{ $idx }}][product_id]" class="form-control nexora-input product-select select2" required>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost ?? $product->price ?? 0 }}" @selected($product->id == $item->product_id)>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $idx }}][quantity]" class="form-control nexora-input qty-input text-center" value="{{ $item->quantity }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="items[{{ $idx }}][cost]" class="form-control nexora-input cost-input text-right" value="{{ $item->cost }}" min="0" required>
                                            </td>
                                            <td class="text-right align-middle font-weight-bold subtotal-display" style="font-size: 15px; color: #0F1F3D;">
                                                Rp {{ number_format($item->quantity * $item->cost, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-link text-danger remove-row p-1" title="Hapus Baris">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>
                                                <select name="items[0][product_id]" class="form-control nexora-input product-select select2" required>
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost ?? $product->price ?? 0 }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[0][quantity]" class="form-control nexora-input qty-input text-center" value="1" min="1" required></td>
                                            <td><input type="number" step="0.01" name="items[0][cost]" class="form-control nexora-input cost-input text-right" value="0" min="0" required></td>
                                            <td class="text-right align-middle font-weight-bold subtotal-display" style="font-size: 15px; color: #0F1F3D;">Rp 0</td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-link text-danger remove-row p-1" title="Hapus Baris"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr style="background: #F8FAFC;">
                                        <th colspan="3" class="text-right font-weight-bold" style="font-size: 15px; color: #334155;">
                                            TOTAL PEMBELIAN:
                                        </th>
                                        <th class="text-right font-weight-bold" style="font-size: 18px; color: #0058A6;" id="totalDisplay">
                                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Hidden Input for Total Amount -->
                <input type="hidden" name="total_amount" id="total_amount" value="{{ $purchase->total_amount }}">

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/purchase/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
        let itemIndex = {{ count($purchase->purchase_items) ?: 1 }};

        function formatRupiah(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        }

        $('#addRow').click(function() {
            let tr = `<tr>
                <td>
                    <select name="items[${itemIndex}][product_id]" class="form-control nexora-input product-select select2" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-cost="{{ $product->cost ?? $product->price ?? 0 }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control nexora-input qty-input text-center" value="1" min="1" required></td>
                <td><input type="number" step="0.01" name="items[${itemIndex}][cost]" class="form-control nexora-input cost-input text-right" value="0" min="0" required></td>
                <td class="text-right align-middle font-weight-bold subtotal-display" style="font-size: 15px; color: #0F1F3D;">Rp 0</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-link text-danger remove-row p-1" title="Hapus Baris"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>`;
            
            const $newRow = $(tr);
            $('#itemsTable tbody').append($newRow);
            
            if ($.fn.select2) {
                $newRow.find('.select2').select2({
                    width: '100%',
                    dropdownCssClass: 'nexora-select2-dropdown'
                });
            }
            
            itemIndex++;
        });

        $(document).on('change', '.product-select', function() {
            let cost = $(this).find(':selected').data('cost') || 0;
            $(this).closest('tr').find('.cost-input').val(cost);
            calculateTotal();
        });

        $(document).on('input', '.qty-input, .cost-input', function() {
            calculateTotal();
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#itemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                alert('Minimal harus ada satu baris barang pembelian.');
            }
        });

        function calculateTotal() {
            let total = 0;
            $('#itemsTable tbody tr').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let cost = parseFloat($(this).find('.cost-input').val()) || 0;
                let subtotal = qty * cost;
                $(this).find('.subtotal-display').text(formatRupiah(subtotal));
                total += subtotal;
            });
            $('#totalDisplay').text(formatRupiah(total));
            $('#total_amount').val(total);
        }

        calculateTotal();
    });
</script>
@endsection
