@extends('admin.layout.app')

@section('title', 'Manage Features: ' . $company->name)

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                
                @php
                    $license = $company->licenseSnapshot();
                    $statusClass = match ($license['status']) {
                        'ACTIVE' => 'is-success',
                        'EXPIRING_SOON', 'GRACE_PERIOD' => 'is-warning',
                        'OVERDUE', 'BLOCKED' => 'is-danger',
                        default => 'is-neutral',
                    };
                @endphp

                <div class="superadmin-header-card mb-4" style="align-items: flex-start;">
                    <div>
                        <span style="color: rgba(255,255,255,0.7); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">SaaS Control Center</span>
                        <h4 class="mt-1 mb-2"><i class="fas fa-building"></i> {{ $company->name }}</h4>
                        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 0;">Code: {{ $company->company_code }} | Status: {{ $license['label'] }} | Atur license dan fitur khusus untuk company ini.</p>
                    </div>
                    <a href="{{ url('admin-panel/features') }}" class="btn mt-2" style="background-color: rgba(255,255,255,0.15) !important; color: #FFFFFF !important; border: 1px solid rgba(255,255,255,0.25) !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        &laquo; Back to List
                    </a>
                </div>

                <div class="nexora-control-grid">
                    <form method="POST" action="{{ url('admin-panel/features/billing') }}" class="nexora-billing-form nexora-control-card {{ $statusClass }} mb-4">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">

                        <div class="nexora-control-card-head">
                            <div>
                                <span>Billing</span>
                                <strong>Payment & access status</strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Billing Status</label>
                                <select name="subscription_status" class="form-control">
                                    <option value="active" {{ ($company->subscription_status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="past_due" {{ $company->subscription_status === 'past_due' ? 'selected' : '' }}>Past Due</option>
                                    <option value="inactive" {{ $company->subscription_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blocked" {{ $company->subscription_status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Payment Term</label>
                                <select name="billing_period" class="form-control nexora-billing-period">
                                    <option value="monthly" {{ ($company->billing_period ?? 'monthly') === 'monthly' ? 'selected' : '' }}>1 Month</option>
                                    <option value="yearly" {{ $company->billing_period === 'yearly' ? 'selected' : '' }}>1 Year</option>
                                    <option value="custom" {{ $company->billing_period === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Months</label>
                                <input type="number" name="billing_interval_months" class="form-control nexora-billing-months" value="{{ $company->billing_interval_months ?: 1 }}" min="1" max="36">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="payment_amount" class="form-control" value="{{ $company->payment_amount }}" min="0" step="1000" placeholder="Optional">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">License Start</label>
                                <input type="date" name="license_started_at" class="form-control" value="{{ optional($company->license_started_at)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Maximum Users</label>
                                <input type="number" name="max_users" class="form-control" value="{{ $company->max_users ?: 10 }}" min="1" max="100000">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Payment Due Date</label>
                                <input type="date" name="payment_due_at" class="form-control" value="{{ optional($company->payment_due_at)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grace Period Days</label>
                                <input type="number" name="grace_period_days" class="form-control" value="{{ $company->grace_period_days ?? 7 }}" min="0" max="90">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Billing Note</label>
                                <input type="text" name="block_reason" class="form-control" value="{{ $company->block_reason }}" placeholder="Contoh: invoice belum dibayar">
                            </div>
                        </div>

                        <div class="nexora-control-actions">
                            <button type="submit" name="billing_action" value="save" class="btn btn-secondary">Save Billing</button>
                            <button type="submit" name="billing_action" value="mark_paid" class="btn btn-primary">Mark Paid & Extend</button>
                            @if((float) $company->payment_amount > 0)
                                <button type="button" class="btn btn-success" id="payCompanyQrisBtn">
                                    <i class="fas fa-qrcode"></i> Bayar via QRIS
                                </button>
                            @endif
                        </div>
                    </form>

                    <form method="POST" action="{{ url('admin-panel/features/update') }}" class="nexora-control-card is-neutral">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">

                        <div class="nexora-control-card-head">
                            <div>
                                <span>Modules</span>
                                <strong>Feature access control</strong>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Save Features</button>
                        </div>

                        <div class="nexora-feature-toggle-grid">
                            @foreach ($features as $feature)
                                @php
                                    $toggle = $companyFeatures[$feature->key] ?? null;
                                    $enabled = $toggle ? $toggle->is_enabled : true;
                                @endphp
                                <label class="nexora-feature-toggle {{ $enabled ? 'is-enabled' : 'is-disabled' }}">
                                    <input type="checkbox" name="features[]" value="{{ $feature->key }}" {{ $enabled ? 'checked' : '' }}>
                                    <span class="nexora-toggle-visual"></span>
                                    <span class="nexora-feature-toggle-copy">
                                        <strong>{{ $feature->name }}</strong>
                                        <small>{{ $feature->description ?: 'Module access' }}</small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @if((float) $company->payment_amount > 0)
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif
    <script>
        document.getElementById('payCompanyQrisBtn')?.addEventListener('click', async function () {
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan QRIS...';

            try {
                const response = await fetch('{{ url('admin-panel/features/payment/qris') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({company_id: {{ $company->id }}}),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Pembayaran gagal dibuat.');

                window.snap.pay(data.snap_token, {
                    onSuccess: () => Swal.fire('Berhasil', 'Pembayaran diterima. Status license akan diperbarui otomatis.', 'success').then(() => window.location.reload()),
                    onPending: () => Swal.fire('Menunggu pembayaran', 'Selesaikan pembayaran QRIS pada jendela Midtrans.', 'info'),
                    onError: () => Swal.fire('Gagal', 'Pembayaran QRIS gagal.', 'error'),
                    onClose: () => { button.disabled = false; button.innerHTML = '<i class="fas fa-qrcode"></i> Bayar via QRIS'; },
                });
            } catch (error) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-qrcode"></i> Bayar via QRIS';
                Swal.fire('Gagal', error.message, 'error');
            }
        });

        document.querySelectorAll('.nexora-billing-form').forEach((form) => {
            const period = form.querySelector('.nexora-billing-period');
            const months = form.querySelector('.nexora-billing-months');

            if (!period || !months) {
                return;
            }

            const syncMonths = () => {
                if (period.value === 'monthly') {
                    months.value = 1;
                    months.readOnly = true;
                } else if (period.value === 'yearly') {
                    months.value = 12;
                    months.readOnly = true;
                } else {
                    months.readOnly = false;
                }
            };

            period.addEventListener('change', syncMonths);
            syncMonths();
        });

        document.querySelectorAll('.nexora-feature-toggle input[type="checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const card = checkbox.closest('.nexora-feature-toggle');
                if (!card) {
                    return;
                }

                card.classList.toggle('is-enabled', checkbox.checked);
                card.classList.toggle('is-disabled', !checkbox.checked);
            });
        });
    </script>
@endpush
