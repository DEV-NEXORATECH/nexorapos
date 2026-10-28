@extends('admin.layout.app')

@section('title', 'Varian Produk - NexoraPOS')

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
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; VARIAN ITEM</span>
                        <h3 class="nexora-page-header-title">Varian Produk</h3>
                        <p class="nexora-page-header-desc">Kelola opsi ukuran, warna, atau varian rasa untuk item barang induk pada katalog.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/variants/add') }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-plus mr-1"></i> Tambah Varian Baru &rarr;
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card nexora-table-card mb-5">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                    <div>
                        <span class="eyebrow-tag"><span class="dot"></span> DATABASE VARIAN</span>
                        <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Varian Terdaftar</h4>
                        <p class="card-category" style="margin-bottom: 0;">Seluruh variasi produk yang terhubung dengan produk induk dalam sistem.</p>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="table nexora-data-table" id="variantsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#ID</th>
                                <th>Produk Induk</th>
                                <th>Nama Varian</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">Harga Modal</th>
                                <th class="text-nowrap">Perusahaan</th>
                                <th class="text-nowrap">Dibuat Oleh</th>
                                <th class="text-nowrap">Waktu Dibuat</th>
                                <th class="text-nowrap text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($variants as $v)
                                <tr>
                                    <td><span class="badge badge-light" style="font-weight: 700; color: #64748B;">#{{ $v->id }}</span></td>
                                    <td style="font-weight: 700; color: #0058A6;">{{ optional($v->product)->name ?? '-' }}</td>
                                    <td style="font-weight: 600; color: #0F1F3D;">{{ $v->name }}</td>
                                    <td>{{ $v->sku ?? '-' }}</td>
                                    <td>{{ $v->barcode ?? '-' }}</td>
                                    <td class="text-right" style="font-weight: 700; color: #10B981;">Rp {{ number_format($v->price, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($v->cost, 0, ',', '.') }}</td>
                                    <td class="text-nowrap">{{ $v->company?->name ?? 'System' }}</td>
                                    <td class="text-nowrap">{{ $v->user?->name ?? $v->creator?->name ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $v->created_at ? $v->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="table-actions justify-content-center">
                                            <a href="{{ url('admin-panel/variants/edit', $v->id) }}" class="btn-action btn-action-primary" title="Edit Varian">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ url('admin-panel/variants/delete', $v->id) }}" class="btn-action btn-action-danger delete-category" title="Hapus Varian">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
