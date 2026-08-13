@extends('admin.layout.app')

@section('title', 'Business Process')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div>
                    <h4 class="page-title mb-1">Business Process</h4>
                    <p class="text-muted mb-0">Flow ini otomatis mengikuti company, lisensi, dan fitur yang sedang aktif.</p>
                </div>

                @if(auth()->user()?->isSuperAdmin())
                    <form method="GET" action="{{ url('admin-panel/business-process') }}" class="d-flex align-items-center" style="gap: 8px;">
                        <select name="company_id" class="form-control" onchange="this.form.submit()">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $selectedCompany?->id === $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} - {{ $company->company_code }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            @if(! $selectedCompany)
                <div class="card">
                    <div class="card-body text-center text-muted">
                        Company belum dipilih atau user belum punya akses company.
                    </div>
                </div>
            @else
                <div class="nexora-process-hero">
                    <div>
                        <span class="nexora-status {{ $selectedCompany->isSubscriptionBlocked() ? 'nexora-status-danger' : 'nexora-status-success' }}">
                            {{ $selectedCompany->isSubscriptionBlocked() ? 'Blocked' : 'Active License' }}
                        </span>
                        <h3>{{ $selectedCompany->name }}</h3>
                        <p>
                            {{ $enabledFeatures->count() }} fitur aktif dari {{ $features->count() }} fitur.
                            @if($selectedCompany->payment_due_at)
                                Jatuh tempo {{ $selectedCompany->payment_due_at->format('d M Y') }}.
                            @endif
                        </p>
                    </div>
                    @if(auth()->user()?->isSuperAdmin())
                        <a href="{{ url('admin-panel/features') }}" class="btn btn-primary btn-sm">Manage Package</a>
                    @endif
                </div>

                @if($selectedCompany->isSubscriptionBlocked())
                    <div class="nexora-billing-alert nexora-billing-alert-danger mb-3">
                        <strong>Flow operasional sedang diblokir.</strong>
                        <span>{{ $selectedCompany->block_reason ?: 'Company melewati masa pembayaran atau status subscription tidak aktif.' }}</span>
                    </div>
                @endif

                <div class="nexora-process-flow">
                    @foreach($processSteps as $index => $step)
                        <div class="nexora-process-step">
                            <div class="nexora-process-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="nexora-process-icon"><i class="{{ $step['icon'] }}"></i></div>
                            <div class="nexora-process-copy">
                                <span>{{ $step['label'] }}</span>
                                <strong>{{ $step['title'] }}</strong>
                                <p>{{ $step['description'] }}</p>
                            </div>
                        </div>
                        @if(! $loop->last)
                            <div class="nexora-process-arrow"><i class="la la-arrow-down"></i></div>
                        @endif
                    @endforeach
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="card-title">Fitur Aktif Di Paket Ini</div>
                            </div>
                            <div class="card-body">
                                <div class="nexora-feature-pills">
                                    @forelse($enabledFeatures as $feature)
                                        <span>{{ $feature->name }}</span>
                                    @empty
                                        <p class="text-muted mb-0">Belum ada fitur aktif.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="card-title">Fitur Nonaktif</div>
                            </div>
                            <div class="card-body">
                                <div class="nexora-feature-pills is-muted">
                                    @forelse($disabledFeatures as $feature)
                                        <span>{{ $feature->name }}</span>
                                    @empty
                                        <p class="text-muted mb-0">Semua fitur aktif.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
