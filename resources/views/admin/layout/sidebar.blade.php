@php
    $data = modules(); // Get all categories with permissions

    $user = Auth::user();
    $role = $user->role;
    $permissions = $user->isSuperAdmin()
        ? null
        : ($role ? $role->permissions()->pluck('permissions.id')->unique()->toArray() : []);
@endphp

<div class="sidebar">
    <div class="scrollbar-inner sidebar-wrapper">
        <ul class="nav">
            @if ($user->isSuperAdmin())
                <li class="nav-heading">WORKSPACE</li>

                <li class="nav-item {{ request()->is('admin-panel/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/dashboard') }}">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/features*') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/features') }}">
                        <i class="fa-solid fa-toggle-on"></i>
                        <span>Company Features</span>
                    </a>
                </li>

                <li class="nav-heading">ADMINISTRASI</li>

                @php
                    $superAdminModules = [
                        [
                            'name' => 'User & Akses',
                            'icon' => 'fa-regular fa-user',
                            'items' => [
                                ['name' => 'Daftar Pengguna', 'route' => 'admin-panel/users/listing', 'icon' => 'fa-regular fa-address-card'],
                                ['name' => 'Role & Permission', 'route' => 'admin-panel/roles/listing', 'icon' => 'fa-solid fa-shield-halved'],
                            ],
                        ],
                        [
                            'name' => 'Pengaturan Sistem',
                            'icon' => 'fa-solid fa-sliders',
                            'items' => [
                                ['name' => 'App Settings', 'route' => 'admin-panel/settings/listing', 'icon' => 'fa-solid fa-gear'],
                            ],
                        ],
                        [
                            'name' => 'Master Data POS',
                            'icon' => 'fa-solid fa-database',
                            'items' => [
                                ['name' => 'Toko / Cabang', 'route' => 'admin-panel/stores/listing', 'icon' => 'fa-solid fa-store'],
                                ['name' => 'Kategori Produk', 'route' => 'admin-panel/categories/listing', 'icon' => 'fa-solid fa-tags'],
                                ['name' => 'Katalog Produk', 'route' => 'admin-panel/products/listing', 'icon' => 'fa-regular fa-file-lines'],
                                ['name' => 'Varian Produk', 'route' => 'admin-panel/variants/listing', 'icon' => 'fa-solid fa-cubes'],
                                ['name' => 'Pajak / Tax', 'route' => 'admin-panel/tax/listing', 'icon' => 'fa-solid fa-percent'],
                                ['name' => 'Data Supplier', 'route' => 'admin-panel/supplier/listing', 'icon' => 'fa-solid fa-truck'],
                                ['name' => 'Data Pelanggan', 'route' => 'admin-panel/customer/listing', 'icon' => 'fa-regular fa-address-book'],
                                ['name' => 'Diskon & Promosi', 'route' => 'admin-panel/discounts/listing', 'icon' => 'fa-solid fa-ticket'],
                                ['name' => 'Mutasi Stok', 'route' => 'admin-panel/stock/listing', 'icon' => 'fa-solid fa-right-left'],
                                ['name' => 'Metode Pembayaran', 'route' => 'admin-panel/payment-methods/listing', 'icon' => 'fa-regular fa-credit-card'],
                            ],
                        ],
                        [
                            'name' => 'Transaksi & Kasir',
                            'icon' => 'fa-solid fa-cash-register',
                            'items' => [
                                ['name' => 'Buka Kasir POS', 'route' => 'admin-panel/pos/sell', 'icon' => 'fa-solid fa-cash-register'],
                                ['name' => 'Riwayat Penjualan', 'route' => 'admin-panel/sales/listing', 'icon' => 'fa-solid fa-bag-shopping'],
                                ['name' => 'Rekonsiliasi Harian', 'route' => 'admin-panel/sales/reconciliation', 'icon' => 'fa-solid fa-scale-balanced'],
                                ['name' => 'Pembelian Barang', 'route' => 'admin-panel/purchase/listing', 'icon' => 'fa-solid fa-cart-shopping'],
                                ['name' => 'Catatan Pembayaran', 'route' => 'admin-panel/payments/listing', 'icon' => 'fa-regular fa-money-bill-1'],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($superAdminModules as $index => $module)
                    @php
                        $routes = collect($module['items'])->pluck('route')->all();
                        $isModuleActive = collect($routes)->contains(fn ($route) => request()->is($route) || request()->is($route.'/*'));
                    @endphp
                    <li class="nav-item dropdown {{ $isModuleActive ? 'show' : '' }}">
                        <a href="#superAdminMenu{{ $index }}" class="nav-link dropdown-toggle" data-toggle="collapse" role="button" aria-expanded="{{ $isModuleActive ? 'true' : 'false' }}" aria-controls="superAdminMenu{{ $index }}">
                            <i class="{{ $module['icon'] }}"></i>
                            <span>{{ $module['name'] }}</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="collapse list-unstyled {{ $isModuleActive ? 'show' : '' }}" id="superAdminMenu{{ $index }}">
                            @foreach ($module['items'] as $item)
                                <li class="nav-item {{ request()->is($item['route']) || request()->is($item['route'].'/*') ? 'active' : '' }}">
                                    <a href="{{ url($item['route']) }}" class="nav-link">
                                        <i class="{{ $item['icon'] ?? 'fas fa-chevron-right' }}"></i>
                                        <span class="nav-link-text">{{ $item['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            @else
                <li class="nav-heading">WORKSPACE</li>

                <li class="nav-item {{ request()->is('admin-panel/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/dashboard') }}">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/business-process') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/business-process') }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Business Process</span>
                    </a>
                </li>

                <li class="nav-heading">INFORMASI & LISENSI</li>

                <li class="nav-item {{ request()->is('admin-panel/settings/license') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/settings/license') }}">
                        <i class="fas fa-certificate"></i>
                        <span>Status Lisensi</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/settings/company') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/settings/company') }}">
                        <i class="fas fa-building"></i>
                        <span>Profil Perusahaan</span>
                    </a>
                </li>

                <li class="nav-heading">MODUL OPERASIONAL</li>

                @foreach ($data as $category)
                    @php
                        $filteredPermissions = $category->permissions->whereIn('id', $permissions);
                    @endphp

                    @if ($filteredPermissions->isNotEmpty())
                        @php
                        $currentRoute = Route::current() ? Route::current()->uri() : '';
                        $allRoutes = $filteredPermissions->pluck('route')->toArray();
                        $isCategoryActive = in_array($currentRoute, $allRoutes) || collect($allRoutes)->contains(fn($r) => request()->is($r) || request()->is($r.'/*'));

                        $categoryIcons = [
                            'pos' => 'fas fa-cash-register',
                            'sales' => 'fas fa-bag-shopping',
                            'products' => 'fas fa-box-open',
                            'customers' => 'fas fa-address-book',
                            'suppliers' => 'fas fa-truck',
                            'payments' => 'fas fa-money-bill-wave',
                            'reports' => 'fas fa-chart-line',
                            'stores' => 'fas fa-store',
                            'taxes' => 'fas fa-percent',
                            'discounts' => 'fas fa-ticket',
                            'roles' => 'fas fa-shield-halved',
                            'settings' => 'fas fa-gear',
                        ];
                        $categoryIcon = $categoryIcons[$category->feature_key ?? ''] ?? ($category->icon ?: 'fas fa-layer-group');
                        @endphp
                        
                        <li class="nav-item dropdown {{ $isCategoryActive ? 'show' : '' }}">
                            <a href="#categoryDropdown{{ $category->id }}" class="nav-link dropdown-toggle" data-toggle="collapse" role="button" aria-expanded="{{ $isCategoryActive ? 'true' : 'false' }}" aria-controls="categoryDropdown{{ $category->id }}">
                                <i class="{{ $categoryIcon }}"></i>
                                <span>{{ $category->name }}</span>
                                <i class="fas fa-chevron-down submenu-arrow"></i>
                            </a>
                            <ul class="collapse list-unstyled {{ $isCategoryActive ? 'show' : '' }}" id="categoryDropdown{{ $category->id }}">
                                @foreach ($filteredPermissions as $permission)
                                    @if ($permission->route)
                                        <li class="nav-item {{ request()->is($permission->route) || request()->is($permission->route.'/*') ? 'active' : '' }}">
                                            <a href="{{ url($permission->route) }}" class="nav-link">
                                                <i class="{{ $permission->icon ?? 'fas fa-chevron-right' }}"></i>
                                                <span class="nav-link-text">{{ $permission->name }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>
