@php
    $settings=settings();
@endphp

<!--================Header Menu Area =================-->
  <header class="header_area">
    <div class="main_menu">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container box_1620">
          <!-- Brand and toggle get grouped for better mobile display -->
          <a class="navbar-brand logo_h" href="index.html"><img src="{{ asset('front/img/logo.png') }}" alt=""></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
           <ul class="nav navbar-nav menu_nav justify-content-center">
    <li class="nav-item active"><a class="nav-link" href="{{ route('homePage') }}">Home</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('archivePage') }}">Archive</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('catPage') }}">Category</a></li>

    {{-- <li class="nav-item submenu dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
           aria-expanded="false">Pages</a>
        <ul class="dropdown-menu">
            <li class="nav-item"><a class="nav-link" href="{{ route('blogPage') }}">Blog Details</a></li>
        </ul>
    </li> --}}

    <li class="nav-item"><a class="nav-link" href="{{ route('contactPage') }}">Contact</a></li>

    {{-- @auth
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-primary fw-bold"  href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        Profile
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
    @endauth --}}
</ul>

            <ul class="nav navbar-nav navbar-right navbar-social">
                @foreach ($settings as $setting)
                    <li><a href="{{ $setting->value }}" title="Social Link"><i class="{{ $setting->key }}"></i></a></li>
                @endforeach


            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
