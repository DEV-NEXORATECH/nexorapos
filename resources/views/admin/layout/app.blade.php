<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Fonts: Poppins & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (Ready Dashboard - Bootstrap 4) -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/ready.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/pos/custome.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-theme.css') }}?v={{ file_exists(public_path('admin/custom/css/nexora-theme.css')) ? filemtime(public_path('admin/custom/css/nexora-theme.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-strict-override.css') }}?v={{ file_exists(public_path('admin/custom/css/nexora-strict-override.css')) ? filemtime(public_path('admin/custom/css/nexora-strict-override.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('admin/custom/css/nexora-modern.css') }}?v={{ file_exists(public_path('admin/custom/css/nexora-modern.css')) ? filemtime(public_path('admin/custom/css/nexora-modern.css')) : time() }}">

    <!-- Font Awesome & Line Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        (function() {
            var savedTheme = localStorage.getItem('nexora_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('theme-dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('theme-dark');
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    @php
        $usesDataTable = request()->is('admin-panel/*/listing')
            || request()->is('admin-panel/*/*/listing')
            || request()->is('admin-panel/products/recycle');
        $usesDashboardChart = request()->is('admin-panel/dashboard');
        $usesSelect2 = request()->is('admin-panel/*/add')
            || request()->is('admin-panel/*/*/add')
            || request()->is('admin-panel/*/edit/*')
            || request()->is('admin-panel/*/*/edit/*')
            || request()->is('admin-panel/settings*')
            || request()->is('profile');
    @endphp

    @if($usesDataTable)
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    @endif

    @if($usesSelect2)
        <!-- Select2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    @endif
</head>
<body>
    @include('admin.layout.message')
    <div class="wrapper">
    @php
        $isKasir = auth()->check() && strtolower(auth()->user()->role?->role) === 'kasir';
    @endphp

    @if(!$isKasir)
        <div class="main-header">
            @include('admin.layout.navbar')
        </div>
        @include('admin.layout.sidebar')
    @else
        <style>
            .main-panel { width: 100% !important; margin-top: 0 !important; padding-top: 0 !important; height: 100vh !important; }
        </style>
    @endif
        @php
            $billingCompany = auth()->user()?->company;
            $showBillingNotice = $billingCompany && ! auth()->user()?->isSuperAdmin();
            $licenseSnapshot = $showBillingNotice ? $billingCompany->licenseSnapshot() : null;
            $licenseInactive = $licenseSnapshot['is_locked'] ?? false;
            $enabledFeatureCount = $showBillingNotice ? $billingCompany->enabledFeaturesCount() : 0;
        @endphp

        @if($licenseSnapshot && $licenseSnapshot['is_locked'])
            <div class="main-panel nexora-global-notice-panel">
                <div class="content pb-0">
                    <div class="container-fluid">
                        <div class="nexora-billing-alert nexora-billing-alert-danger">
                            <strong>License {{ $licenseSnapshot['label'] }}.</strong>
                            <span>Fitur operasional dikunci. Expiry {{ optional($licenseSnapshot['expiry_date'])->format('d M Y') ?? '-' }}. {{ $billingCompany->block_reason ?: 'Hubungi Super Admin untuk aktivasi kembali.' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($licenseSnapshot && $licenseSnapshot['is_warning'])
            <div class="main-panel nexora-global-notice-panel">
                <div class="content pb-0">
                    <div class="container-fluid">
                        <div class="nexora-billing-alert nexora-billing-alert-warning">
                            <strong>{{ $licenseSnapshot['status'] === 'GRACE_PERIOD' ? 'Grace period berjalan.' : 'Reminder pembayaran.' }}</strong>
                            <span>
                                Jatuh tempo {{ optional($licenseSnapshot['expiry_date'])->format('d M Y') }}.
                                @if($licenseSnapshot['status'] === 'GRACE_PERIOD')
                                    Sisa grace {{ max($licenseSnapshot['remaining_grace_days'], 0) }} hari sebelum dikunci.
                                @else
                                    Sisa {{ max($licenseSnapshot['remaining_days'], 0) }} hari.
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
        @include('admin.layout.footer')

        <!-- Modal -->
        <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdatePro" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h6 class="modal-title"><i class="la la-frown-o"></i> Under Development</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><!-- Bootstrap 4 -->
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Currently the pro version of the <b>Ready Dashboard</b> Bootstrap is in progress development</p>
                        <p><b>We'll let you know when it's done</b></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button><!-- Bootstrap 4 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('admin/assets/js/core/jquery.3.2.1.min.js') }}"></script>

    @if($usesDashboardChart)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('admin/custom/js/chart.js') }}?v={{ file_exists(public_path('admin/custom/js/chart.js')) ? filemtime(public_path('admin/custom/js/chart.js')) : time() }}"></script>
    @endif

    @if($usesSelect2)
        <!-- Select2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    @endif

    @if($usesDataTable)
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    @endif

    <!-- Ready Dashboard Scripts (Bootstrap 4 + Plugins + Chartist) -->
    <script src="{{ asset('admin/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/ready.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.NexoraPopup = {
            classify(message) {
                const text = String(message || '').toLowerCase();
                if (text.includes('delete') || text.includes('deleted')) {
                    return { title: 'Data Deleted', detail: 'Record berhasil dihapus dari sistem.' };
                }
                if (text.includes('update') || text.includes('updated')) {
                    return { title: 'Data Updated', detail: 'Perubahan berhasil disimpan.' };
                }
                if (text.includes('add') || text.includes('added') || text.includes('create') || text.includes('registered') || text.includes('submitted')) {
                    return { title: 'Data Created', detail: 'Data baru berhasil dibuat dan tersimpan.' };
                }
                if (text.includes('restore')) {
                    return { title: 'Data Restored', detail: 'Data berhasil dikembalikan.' };
                }

                return { title: 'Success', detail: 'Proses berhasil dijalankan.' };
            },
            success(options) {
                const payload = typeof options === 'string' ? { message: options } : options;
                const meta = this.classify(payload.message);
                const message = String(payload.message || meta.detail);
                const detail = String(payload.detail || meta.detail);

                Swal.fire({
                    icon: 'success',
                    title: payload.title || meta.title,
                    text: `${message}\n${detail}`,
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#0058A6',
                    timer: payload.timer || 2600,
                    timerProgressBar: true,
                });
            },
            error(options) {
                const payload = typeof options === 'string' ? { message: options } : options;
                const message = String(payload.message || 'Terjadi error saat memproses request.');
                const detail = String(payload.detail || 'Cek input, permission, koneksi database, atau coba ulang prosesnya.');

                Swal.fire({
                    icon: 'error',
                    title: payload.title || 'Action Failed',
                    text: `${message}\n${detail}`,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#0058A6',
                });
            },
            validation(messages) {
                const list = (messages || []).map((message) => `- ${message}`).join('\n');

                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Needed',
                    text: `Beberapa input belum sesuai:\n${list}\nPerbaiki field yang ditandai, lalu submit ulang.`,
                    confirmButtonText: 'Perbaiki',
                    confirmButtonColor: '#0058A6',
                });
            },
        };
    </script>

    <!-- Session Alerts -->
    @if(session('success'))
        <script>
            window.NexoraPopup.success({
                message: @json(session('success')),
            });
        </script>
    @endif

    @if(session('failed'))
        <script>
            window.NexoraPopup.error({
                message: @json(session('failed')),
                detail: 'Request gagal diproses. Jika muncul lagi, cek data input dan permission user.',
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            window.NexoraPopup.validation(@json($errors->all()));
        </script>
    @endif

    @if(isset($licenseSnapshot) && $licenseSnapshot && $licenseSnapshot['is_locked'] && ! request()->is('admin-panel/settings/license'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'License {{ $licenseSnapshot['label'] }}',
                @php
                    $licenseLockedText = 'Company '.$billingCompany->name.' tidak bisa memakai fitur operasional. '.
                        'Status: '.$licenseSnapshot['label'].'. '.
                        'Fitur aktif: '.$licenseSnapshot['active_features'].'/'.$licenseSnapshot['total_features'].'. '.
                        ($licenseSnapshot['expiry_date'] ? 'Jatuh tempo: '.$licenseSnapshot['expiry_date']->format('d M Y').'. ' : '').
                        'Hubungi Super Admin untuk mengaktifkan license atau memperpanjang pembayaran.';
                @endphp
                text: @json($licenseLockedText),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                backdrop: true,
            });
        </script>
    @elseif(isset($licenseSnapshot) && $licenseSnapshot && $licenseSnapshot['is_warning'])
        <script>
            const licensePopupKey = @json('license-warning-'.$billingCompany->id.'-'.$licenseSnapshot['status'].'-'.optional($licenseSnapshot['expiry_date'])->format('Ymd'));
            const shouldShowLicenseWarning = @json($licenseSnapshot['status'] === 'GRACE_PERIOD') || ! sessionStorage.getItem(licensePopupKey);

            if (shouldShowLicenseWarning) {
                Swal.fire({
                    icon: 'warning',
                    title: @json($licenseSnapshot['status'] === 'GRACE_PERIOD' ? 'Grace Period License' : 'Reminder Pembayaran'),
                    @php
                        $licenseWarningText = 'Paket '.$licenseSnapshot['package'].' jatuh tempo pada '.
                            optional($licenseSnapshot['expiry_date'])->format('d M Y').'. '.
                            ($licenseSnapshot['status'] === 'GRACE_PERIOD'
                                ? 'Sisa grace '.max($licenseSnapshot['remaining_grace_days'], 0).' hari sebelum fitur dikunci. '
                                : 'Sisa '.max($licenseSnapshot['remaining_days'], 0).' hari. ').
                            'Jika lewat masa aktif/grace, fitur operasional otomatis diblok.';
                    @endphp
                    text: @json($licenseWarningText),
                    confirmButtonText: 'Lihat Status License',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#0058A6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = @json(url('admin-panel/settings/license'));
                    }
                });
                sessionStorage.setItem(licensePopupKey, '1');
            }
        </script>
    @endif

    <!-- Initialize Select2 -->
    <script>
        $(document).ready(function() {
            if ($.fn.select2 && $('.select2').length) {
                $('.select2').select2();
            }
        });
    </script>

    <!-- Custom Page Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggles = document.querySelectorAll('.sidebar-toggle-control');
            toggles.forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('nexora_sidebar_collapsed', isCollapsed ? '1' : '0');
                        toggles.forEach(t => t.classList.toggle('toggled', isCollapsed));
                    } else {
                        const isNavOpen = document.documentElement.classList.toggle('nav_open');
                        toggles.forEach(t => t.classList.toggle('toggled', isNavOpen));
                    }
                });
            });

            // Restore collapsed state on desktop
            if (window.innerWidth >= 992 && localStorage.getItem('nexora_sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
                toggles.forEach(t => t.classList.add('toggled'));
            }

            document.querySelectorAll('table.table, table.dataTable').forEach(function (table) {
                if (table.closest('.table-responsive, .dataTables_wrapper, .nexora-table-scroll')) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'nexora-table-scroll';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('button[type="submit"], button:not([type])');
                    if (!button || button.dataset.loadingAttached === '1') {
                        return;
                    }

                    button.dataset.loadingAttached = '1';
                    button.dataset.originalText = button.textContent.trim();
                    const verb = button.dataset.originalText.toLowerCase().includes('update') ? 'Updating...' : 'Saving...';
                    button.textContent = verb;
                    button.disabled = true;
                });
            });

            document.querySelectorAll('.delete-category').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const href = link.getAttribute('href');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Konfirmasi Aksi',
                        text: 'Data akan dihapus, diarsipkan, atau di-void sesuai aturan module. Lanjutkan?',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#0058A6',
                    }).then((result) => {
                        if (result.isConfirmed && href) {
                            window.location.href = href;
                        }
                    });
                });
            });
            // Theme Toggle Handler (Job-Finance style)
            const themeBtn = document.querySelector('[data-theme-toggle]');
            const themeIcon = document.querySelector('[data-theme-icon]');
            const syncTheme = function() {
                const isDark = document.documentElement.classList.contains('theme-dark');
                if (document.body) {
                    document.body.classList.toggle('theme-dark', isDark);
                }
                if (themeIcon) {
                    themeIcon.className = isDark ? 'fa fa-sun text-warning' : 'fa fa-moon';
                }
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            };
            if (themeBtn) {
                syncTheme();
                themeBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('theme-dark');
                    localStorage.setItem('nexora_theme', isDark ? 'dark' : 'light');
                    syncTheme();
                    window.dispatchEvent(new CustomEvent('nexora-theme-changed', { detail: { isDark: isDark } }));
                });
            } else {
                syncTheme();
            }

            // Toast Auto-Dismissal
            document.querySelectorAll('.app-toast').forEach(function(toast) {
                const closeBtn = toast.querySelector('.app-toast-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(20px)';
                        setTimeout(() => toast.remove(), 250);
                    });
                }
                setTimeout(function() {
                    if (toast && toast.parentElement) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(20px)';
                        setTimeout(() => toast.remove(), 250);
                    }
                }, 6000);
            });
        });
    </script>
    @stack('script')
    @yield('scripts')
</body>
</html>
