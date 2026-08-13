@php
    $data = modules(); // Get all categories with permissions

    $user=Auth::user();
    $role=$user->role;
    $permissions=$user->isSuperAdmin()
        ? null
        : ($role ? $role->permissions()->pluck('permissions.id')->unique()->toArray() : []);

@endphp

<div class="sidebar">
    <div class="scrollbar-inner sidebar-wrapper">
        <ul class="nav">
            @if ($user->isSuperAdmin())
                <li class="nav-item {{ request()->is('admin-panel/dashboard') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/dashboard') }}">
                        <i class="la la-dashboard"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/features*') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/features') }}">
                        <i class="la la-toggle-on"></i>
                        <p>Company Features</p>
                    </a>
                </li>

                @php
                    $superAdminModules = [
                        [
                            'name' => 'User Management',
                            'icon' => 'la la-users',
                            'items' => [
                                ['name' => 'Users', 'route' => 'admin-panel/users/listing', 'icon' => 'la la-user-plus'],
                                ['name' => 'Roles & Access', 'route' => 'admin-panel/roles/listing', 'icon' => 'la la-key'],
                            ],
                        ],
                        [
                            'name' => 'System Setup',
                            'icon' => 'la la-cog',
                            'items' => [
                                ['name' => 'App Settings', 'route' => 'admin-panel/settings/listing', 'icon' => 'la la-sliders'],
                            ],
                        ],
                        [
                            'name' => 'POS Master',
                            'icon' => 'la la-database',
                            'items' => [
                                ['name' => 'Stores', 'route' => 'admin-panel/stores/listing', 'icon' => 'la la-home'],
                                ['name' => 'Categories', 'route' => 'admin-panel/categories/listing', 'icon' => 'la la-tags'],
                                ['name' => 'Products', 'route' => 'admin-panel/products/listing', 'icon' => 'la la-cube'],
                                ['name' => 'Product Variants', 'route' => 'admin-panel/variants/listing', 'icon' => 'la la-cubes'],
                                ['name' => 'Tax', 'route' => 'admin-panel/tax/listing', 'icon' => 'la la-percent'],
                                ['name' => 'Suppliers', 'route' => 'admin-panel/supplier/listing', 'icon' => 'la la-truck'],
                                ['name' => 'Customers', 'route' => 'admin-panel/customer/listing', 'icon' => 'la la-address-book'],
                                ['name' => 'Discounts', 'route' => 'admin-panel/discounts/listing', 'icon' => 'la la-ticket'],
                                ['name' => 'Stock Movements', 'route' => 'admin-panel/stock/listing', 'icon' => 'la la-exchange'],
                                ['name' => 'Payment Methods', 'route' => 'admin-panel/payment-methods/listing', 'icon' => 'la la-credit-card'],
                            ],
                        ],
                        [
                            'name' => 'Transactions',
                            'icon' => 'la la-shopping-cart',
                            'items' => [
                                ['name' => 'POS Sell', 'route' => 'admin-panel/pos/sell', 'icon' => 'la la-shopping-cart'],
                                ['name' => 'Sales', 'route' => 'admin-panel/sales/listing', 'icon' => 'la la-shopping-bag'],
                                ['name' => 'Sales Reconciliation', 'route' => 'admin-panel/sales/reconciliation', 'icon' => 'la la-balance-scale'],
                                ['name' => 'Purchases', 'route' => 'admin-panel/purchase/listing', 'icon' => 'la la-cart-plus'],
                                ['name' => 'Payments', 'route' => 'admin-panel/payments/listing', 'icon' => 'la la-money'],
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
                        </a>
                        <ul class="collapse list-unstyled ml-3 {{ $isModuleActive ? 'show' : '' }}" id="superAdminMenu{{ $index }}">
                            @foreach ($module['items'] as $item)
                                <li class="nav-item {{ request()->is($item['route']) || request()->is($item['route'].'/*') ? 'active' : '' }}">
                                    <a href="{{ url($item['route']) }}" class="nav-link">
                                        <i class="{{ $item['icon'] ?? 'la la-angle-right' }}"></i>
                                        <span class="nav-link-text">{{ $item['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            @else
                <li class="nav-item {{ request()->is('admin-panel/business-process') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/business-process') }}">
                        <i class="la la-sitemap"></i>
                        <p>Business Process</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/settings/license') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/settings/license') }}">
                        <i class="la la-certificate"></i>
                        <p>License Status</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin-panel/settings/company') ? 'active' : '' }}">
                    <a href="{{ url('admin-panel/settings/company') }}">
                        <i class="la la-building"></i>
                        <p>Company Settings</p>
                    </a>
                </li>

                @foreach ($data as $category)
                    @php
                        $filteredPermissions = $category->permissions->whereIn('id', $permissions);
                    @endphp

                    @if ($filteredPermissions->isNotEmpty())
                        @php

                        $currentRoute = Route::current()->uri();
                        $allRoutes = $filteredPermissions->pluck('route')->toArray();
                        $isCategoryActive = in_array($currentRoute, $allRoutes);

                        @endphp
                        <li class="nav-item dropdown {{ $isCategoryActive ? 'show' : '' }}">
                            <a href="#categoryDropdown{{ $category->id }}" class="nav-link dropdown-toggle" data-toggle="collapse" role="button" aria-expanded="{{ $isCategoryActive ? 'true' : 'false' }}" aria-controls="categoryDropdown{{ $category->id }}">
                                <i class="{{ $category->icons ?? $category->icon ?? 'la la-circle' }}"></i>
                                <span>{{ $category->name }}</span>
                            </a>
                            <ul class="collapse list-unstyled ml-3 {{ $isCategoryActive ? 'show' : '' }}" id="categoryDropdown{{ $category->id }}">
                                @foreach ($filteredPermissions as $permission)
                                    @if ($permission->route)
                                        <li class="nav-item {{ request()->is($permission->route) ? 'active' : '' }}">
                                            <a href="{{ url($permission->route) }}" class="nav-link">
                                                <i class="{{ $permission->icon ?? 'la la-angle-right' }}"></i>
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
