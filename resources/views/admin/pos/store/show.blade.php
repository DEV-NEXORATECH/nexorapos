@extends('admin.layout.app')

@section('title', 'Detail Toko — ' . $store->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; DETAIL TOKO</span>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h3 class="nexora-page-header-title mb-0">{{ $store->name }}</h3>
                            <span class="badge badge-light px-2 py-1" style="font-weight: 700; color: #0058A6; font-size: 12px; background: #EBF4FC; border: 1px solid #BFDBFE;">
                                {{ $store->store_code ?? $store->code ?? '-' }}
                            </span>
                        </div>
                        <p class="nexora-page-header-desc">Ringkasan performa penjualan dan informasi cabang toko ini.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right d-flex align-items-center" style="gap: 10px;">
                    <a href="{{ url('admin-panel/stores/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <a href="{{ url('admin-panel/stores/edit', $store->id) }}" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px 20px; background: #0058A6; border: none; box-shadow: 0 4px 12px rgba(0, 88, 166, 0.25);">
                        <i class="fas fa-edit mr-1"></i> Edit Data Toko
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Store Information & KPI --}}
                <div class="col-lg-5 mb-4">
                    <div class="card nexora-form-card mb-4">
                        <div class="nexora-form-card-head">
                            <div class="nexora-form-card-icon is-navy">
                                <i class="fas fa-circle-info"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">PROFIL CABANG</span>
                                <h4 class="nexora-form-card-title">Informasi Utama Toko</h4>
                            </div>
                        </div>
                        <div class="nexora-form-card-body p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; color: #64748B; padding: 14px 20px;">Kode Toko</td>
                                        <td style="font-weight: 700; color: #0F1F3D; padding: 14px 20px;">{{ $store->store_code ?? $store->code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 14px 20px;">Nama Outlet</td>
                                        <td style="font-weight: 600; color: #0F1F3D; padding: 14px 20px;">{{ $store->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 14px 20px;">Perusahaan</td>
                                        <td style="padding: 14px 20px;">{{ optional($store->company)->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 14px 20px;">Waktu Terdaftar</td>
                                        <td style="padding: 14px 20px;">{{ $store->created_at?->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748B; padding: 14px 20px;">Terakhir Diperbarui</td>
                                        <td style="padding: 14px 20px;">{{ $store->updated_at?->format('d M Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Stats --}}
                    <div class="row">
                        <div class="col-6">
                            <div class="p-3 rounded-lg" style="background: #FFFFFF; border: 1px solid #E6EBF3; border-radius: 14px; box-shadow: 0 4px 14px rgba(15, 31, 61, 0.04);">
                                <small style="color: #64748B; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Total Transaksi</small>
                                <h3 class="mb-0 mt-1" style="font-size: 24px; font-weight: 800; color: #0058A6;">{{ number_format($store->sales()->count()) }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-lg" style="background: #FFFFFF; border: 1px solid #E6EBF3; border-radius: 14px; box-shadow: 0 4px 14px rgba(15, 31, 61, 0.04);">
                                <small style="color: #64748B; font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Total Omzet</small>
                                <h3 class="mb-0 mt-1" style="font-size: 20px; font-weight: 800; color: #10B981;">Rp {{ number_format($store->sales()->where('status', 'completed')->sum('total'), 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Sales --}}
                <div class="col-lg-7 mb-4">
                    <div class="card nexora-table-card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #E6EBF3; padding: 18px 24px;">
                            <div>
                                <span class="eyebrow-tag"><span class="dot"></span> TRANSAKSI OUTLET</span>
                                <h4 class="card-title mb-0" style="font-size: 16px; font-weight: 700; color: #0F1F3D;">Riwayat Penjualan Terbaru (10 Terakhir)</h4>
                            </div>
                            @if($store->sales()->count() > 10)
                                <a href="{{ url('admin-panel/sales/listing') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 600; font-size: 12px;">
                                    Lihat Semua &rarr;
                                </a>
                            @endif
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table mb-0" style="font-size: 13px;">
                                <thead>
                                    <tr style="background: #F8FAFC;">
                                        <th style="padding: 12px 18px;">Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th class="text-right">Total</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 70px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($store->sales()->latest()->take(10)->get() as $sale)
                                    <tr>
                                        <td style="padding: 12px 18px; font-weight: 700; color: #0058A6;">{{ $sale->invoice_number ?? '#'.$sale->id }}</td>
                                        <td style="color: #64748B;">{{ $sale->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ optional($sale->customer)->name ?? 'Walk-in' }}</td>
                                        <td class="text-right" style="font-weight: 700; color: #0F1F3D;">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $badgeStyle = match($sale->status) {
                                                    'completed' => 'background: #DEF7EC; color: #03543F; border: 1px solid #BCF0DA;',
                                                    'cancelled', 'void' => 'background: #FDE8E8; color: #9B1C1C; border: 1px solid #FBD5D5;',
                                                    default => 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;',
                                                };
                                            @endphp
                                            <span class="badge" style="border-radius: 6px; font-weight: 700; font-size: 11px; padding: 4px 8px; {{ $badgeStyle }}">{{ strtoupper($sale->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="btn-action btn-action-primary" title="Lihat Struk Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4" style="color: #94A3B8;">Belum ada riwayat transaksi pada cabang ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
