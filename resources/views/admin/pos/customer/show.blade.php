@extends('admin.layout.app')

@section('title', 'Detail Pelanggan — ' . $customer->name)

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Modern Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> MASTER DATA POS &bull; DETAIL MEMBER</span>
                        <h3 class="nexora-page-header-title">{{ $customer->name }}</h3>
                        <p class="nexora-page-header-desc">Kode Member: <span class="badge badge-light border font-weight-bold px-2 py-1">{{ $customer->customer_code ?? '-' }}</span></p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/customer/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <a href="{{ url('admin-panel/customer/edit', $customer->id) }}" class="btn btn-warning" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 8px 16px; color: #fff;">
                        <i class="fas fa-edit mr-1"></i> Edit Data
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Customer Profile & Info --}}
                <div class="col-lg-4 col-md-5">
                    <div class="nexora-form-card mb-4">
                        <div class="nexora-form-card-head">
                            <div class="nexora-form-card-icon is-navy">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            <div>
                                <span class="nexora-form-card-eyebrow">RINGKASAN</span>
                                <h4 class="nexora-form-card-title">Profil Pelanggan</h4>
                            </div>
                        </div>
                        <div class="nexora-form-card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Kode Member</td>
                                        <td class="font-weight-bold">{{ $customer->customer_code ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Lengkap</td>
                                        <td class="font-weight-bold">{{ $customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Telepon</td>
                                        <td>
                                            @if($customer->phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="text-success font-weight-bold">
                                                    <i class="fab fa-whatsapp mr-1"></i>{{ $customer->phone }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Email</td>
                                        <td>{{ $customer->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Alamat</td>
                                        <td>{{ $customer->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Bergabung Pada</td>
                                        <td>{{ $customer->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Stats --}}
                    <div class="row">
                        <div class="col-6">
                            <div class="card p-3 text-center border-0 shadow-sm" style="border-radius: 12px; background: #f8fafc;">
                                <div class="text-muted small mb-1">Total Transaksi</div>
                                <h3 class="mb-0 font-weight-bold text-dark">{{ $customer->total_transactions ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card p-3 text-center border-0 shadow-sm" style="border-radius: 12px; background: #eef2ff;">
                                <div class="text-muted small mb-1">Total Belanja</div>
                                <h4 class="mb-0 font-weight-bold text-primary">Rp {{ number_format($customer->total_spend ?? 0, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales History --}}
                <div class="col-lg-8 col-md-7">
                    <div class="card nexora-table-card mb-5">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px; border-bottom: 1px solid #E6EBF3; padding: 20px 24px;">
                            <div>
                                <span class="eyebrow-tag"><span class="dot"></span> RIWAYAT BELANJA</span>
                                <h4 class="card-title mb-0" style="font-size: 17px; font-weight: 700; color: #0F1F3D;">Daftar Transaksi</h4>
                            </div>
                            <span class="badge badge-light border">{{ count($customer->sales ?? []) }} Transaksi</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No. Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Outlet / Toko</th>
                                        <th class="text-right">Total Transaksi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Pembayaran</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->sales as $sale)
                                    <tr>
                                        <td><strong>{{ $sale->invoice_number ?? '#'.$sale->id }}</strong></td>
                                        <td>{{ $sale->created_at?->format('d M Y') }}</td>
                                        <td>{{ optional($sale->store)->name ?? '-' }}</td>
                                        <td class="text-right font-weight-bold text-primary">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @php
                                                $ssBadge = match($sale->status) {
                                                    'completed' => 'badge-success',
                                                    'cancelled', 'void' => 'badge-danger',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $ssBadge }} px-2 py-1">{{ ucfirst($sale->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $psBadge = match($sale->payment_status ?? 'UNPAID') {
                                                    'PAID' => 'badge-success',
                                                    'PARTIAL' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $psBadge }} px-2 py-1">{{ $sale->payment_status ?? 'UNPAID' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('admin-panel/pos/invoice', $sale->id) }}" class="nexora-action-btn is-primary" title="Lihat Invoice"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-receipt fa-2x mb-2 d-block text-muted opacity-50"></i>
                                            Belum ada riwayat transaksi untuk pelanggan ini.
                                        </td>
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
