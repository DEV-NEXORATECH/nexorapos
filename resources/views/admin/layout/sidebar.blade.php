@php
    $data = modules(); // Get all categories with permissions

    $user=Auth::user();
    $role=$user->role;
    $permissions=$role->permissions->pluck('id')->unique()->toArray();

@endphp

<div class="sidebar">
    <div class="scrollbar-inner sidebar-wrapper">
        <div class="user">
            <div class="photo">
                <img src="{{ asset('admin/profile_images/'. Auth::user()->profile_image) }}">
            </div>
            <div class="info">
                <a class="" data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                    <span>
                        {{-- Assuming only one role per user, update if multiple --}}

                        <span class="user-level">{{ Auth::user()->name }}</span>
                        <span class="caret"></span>
                    </span>
                </a>
                <div class="clearfix"></div>

                <div class="collapse in" id="collapseExample" aria-expanded="true">
                    <ul class="nav">
                        <li>
                            <a href="{{ route('profile.edit') }}">
                                <span class="link-collapse">My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}">
                                <span class="link-collapse">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ul class="nav">
            @foreach ($data as $category)
                @php
                    // Only show permissions that the user has
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
                            <i class="{{ $category->icons }}"></i>
                            <span>{{ $category->name }}</span>
                        </a>
                        <ul class="collapse list-unstyled ml-3 {{ $isCategoryActive ? 'show' : '' }}" id="categoryDropdown{{ $category->id }}">
                            @foreach ($filteredPermissions as $permission)
                                @if ($permission->route)
                                    <li class="nav-item {{ request()->is($permission->route) ? 'active' : '' }}">
                                        <a href="{{ url($permission->route) }}" class="nav-link">
                                            <span class="nav-link-text">{{ $permission->name }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
