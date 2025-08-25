


@extends('front.layout.app')

@section('title', 'Blog Details')

@section('content')

@include('front.layout.header')

<main class="site-main">
    <!--================Hero Banner start =================-->
     <section class="mb-30px">
    <div class="container">
      <div class="hero-banner hero-banner--sm">
        <div class="hero-banner__content">
          <h1>{{ $post->categories->name }}</h1>
          <nav aria-label="breadcrumb" class="banner-breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('frontPanel/dashBoard') }}">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Blog Details</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

    </main>

    @include('front.layout.blog')


@include('front.layout.footer')
@endsection

