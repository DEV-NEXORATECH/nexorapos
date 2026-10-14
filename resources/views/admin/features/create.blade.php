@extends('admin.layout.app')

@section('title', 'Create Company')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                
                <div class="superadmin-header-card mb-4" style="align-items: flex-start;">
                    <div>
                        <span style="color: rgba(255,255,255,0.7); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">SaaS Control Center</span>
                        <h4><i class="fas fa-building"></i> Create New Company</h4>
                        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 0;">Company code akan dibuat otomatis. Setelah company dibuat, Super Admin bisa atur paket, license, dan fitur di halaman sebelumnya.</p>
                    </div>
                    <a href="{{ url('admin-panel/features') }}" class="btn mt-2" style="background-color: rgba(255,255,255,0.15) !important; color: #FFFFFF !important; border: 1px solid rgba(255,255,255,0.25) !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                        <i class="la la-arrow-left"></i> Back to Features
                    </a>
                </div>

                <form method="POST" action="{{ url('admin-panel/features/company') }}">
                    @csrf
                    
                    <!-- Card 1: Company (Green / success) -->
                    <div class="nexora-control-card is-success mb-4">
                        <div class="nexora-control-card-head">
                            <div>
                                <span>Company</span>
                                <strong>Tenant setup</strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Company Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Bu Winny POS">
                                @error('name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Company Code</label>
                                <input type="text" class="form-control" value="Auto: NAMA-COMPANY-001" disabled readonly>
                                <small class="form-text text-muted">Dibuat otomatis dari nama company + nomor urut. Tidak bisa diedit.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Domain Toko</label>
                                <input type="text" id="company-domain-preview" class="form-control" value="Auto: nama-company.{{ config('services.hostinger.domain') }}" disabled readonly>
                                <small class="form-text text-muted">Subdomain dibuat otomatis setelah company disimpan.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Legal Name</label>
                                <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name') }}" placeholder="PT/CV/Nama legal">
                                @error('legal_name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NPWP</label>
                                <input type="text" name="npwp" class="form-control" value="{{ old('npwp') }}">
                                @error('npwp')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Company Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Company Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                                @error('address')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: License (Yellow / warning) -->
                    <div class="nexora-control-card is-warning mb-4">
                        <div class="nexora-control-card-head">
                            <div>
                                <span>License</span>
                                <strong>Package & billing</strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">License Status *</label>
                                <select name="subscription_status" class="form-control" required>
                                    <option value="active" {{ old('subscription_status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="past_due" {{ old('subscription_status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
                                    <option value="inactive" {{ old('subscription_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blocked" {{ old('subscription_status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('subscription_status')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Payment Term *</label>
                                <select name="billing_period" class="form-control nexora-billing-period" required>
                                    <option value="monthly" {{ old('billing_period', 'monthly') === 'monthly' ? 'selected' : '' }}>1 Month</option>
                                    <option value="yearly" {{ old('billing_period') === 'yearly' ? 'selected' : '' }}>1 Year</option>
                                    <option value="custom" {{ old('billing_period') === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('billing_period')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Months *</label>
                                <input type="number" name="billing_interval_months" class="form-control nexora-billing-months" value="{{ old('billing_interval_months', 1) }}" min="1" max="36" required>
                                @error('billing_interval_months')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Max Users *</label>
                                <input type="number" name="max_users" class="form-control" value="{{ old('max_users', 10) }}" min="1" max="100000" required>
                                @error('max_users')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Payment Amount</label>
                                <input type="number" name="payment_amount" class="form-control" value="{{ old('payment_amount') }}" min="0" step="1000">
                                @error('payment_amount')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">License Start</label>
                                <input type="date" name="license_started_at" class="form-control" value="{{ old('license_started_at', now()->toDateString()) }}">
                                @error('license_started_at')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="payment_due_at" class="form-control" value="{{ old('payment_due_at', now()->addMonth()->toDateString()) }}">
                                @error('payment_due_at')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Grace Period Days *</label>
                                <input type="number" name="grace_period_days" class="form-control" value="{{ old('grace_period_days', 7) }}" min="0" max="90" required>
                                @error('grace_period_days')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Admin User (Black / neutral) -->
                    <div class="nexora-control-card is-neutral mb-4">
                        <div class="nexora-control-card-head">
                            <div>
                                <span>Initial Admin</span>
                                <strong>Optional company admin account</strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Admin Name</label>
                                <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}">
                                @error('admin_name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Admin Email</label>
                                <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}">
                                @error('admin_email')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Admin Phone</label>
                                <input type="text" name="admin_phone" class="form-control" value="{{ old('admin_phone') }}">
                                @error('admin_phone')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Admin Password</label>
                                <input type="password" name="admin_password" class="form-control" placeholder="Minimal 8 karakter">
                                @error('admin_password')<div class="nexora-field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="nexora-control-actions">
                        <button type="submit" class="btn btn-primary">Create Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const companyNameInput = document.querySelector('input[name="name"]');
        const companyDomainPreview = document.querySelector('#company-domain-preview');
        const hostingerDomain = @json(config('services.hostinger.domain'));

        companyNameInput?.addEventListener('input', function () {
            const slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '') || 'nama-company';

            companyDomainPreview.value = 'Auto: ' + slug + '.' + hostingerDomain;
        });

        document.querySelector('.nexora-billing-period')?.addEventListener('change', function(e) {
            const months = document.querySelector('.nexora-billing-months');
            if (!months) return;
            
            if (e.target.value === 'monthly') {
                months.value = 1;
                months.readOnly = true;
            } else if (e.target.value === 'yearly') {
                months.value = 12;
                months.readOnly = true;
            } else {
                months.readOnly = false;
            }
        });
    </script>
@endpush
