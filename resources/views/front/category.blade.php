@extends('front.layout.app')

@section('title', 'Category Page')

@section('content')

@include('front.layout.header')

<main class="site-main">
    <!--================Hero Banner start =================-->
     <section class="mb-30px">
    <div class="container">
      <div class="hero-banner hero-banner--sm">
        <div class="hero-banner__content">
          <h1>Category Page</h1>
          <nav aria-label="breadcrumb" class="banner-breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Category Page</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

    </main>

    @include('front.layout.archive')


@include('front.layout.footer')
@endsection
