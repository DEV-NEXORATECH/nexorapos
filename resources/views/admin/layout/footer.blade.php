<footer class="footer">
    @php
        $footerUser = auth()->user();
        $footerCompany = $footerUser?->company;
        $footerLicense = $footerCompany && ! $footerUser?->isSuperAdmin() ? $footerCompany->licenseSnapshot() : null;
    @endphp
    <div class="container-fluid">
        <div class="footer-license">
            @if(! $footerUser?->isSuperAdmin())
                <a href="{{ url('admin-panel/settings/license') }}">
                    License: {{ $footerLicense['label'] ?? 'Not Active' }}
                    @if($footerCompany)
                        - {{ $footerCompany->name }}
                    @endif
                </a>
            @else
                <a href="{{ url('admin-panel/features') }}">License & Features</a>
            @endif
        </div>

        <div class="footer-powered">
            <span>Powered by</span>
            <img src="{{ asset('admin/images/nexora-technology-logo.png') }}" alt="Nexora Technology">
        </div>
    </div>
</footer>
