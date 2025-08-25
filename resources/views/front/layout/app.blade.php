<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title', 'myBlog')</title>
	<link rel="icon" href="{{ asset('front/img/Fevicon.png') }}" type="image/png">

  <link rel="stylesheet" href="{{ asset('front/vendors/bootstrap/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('front/vendors/fontawesome/css/all.min.css')}}">
  <link rel="stylesheet" href="{{ asset('front/vendors/themify-icons/themify-icons.css')}}">
  <link rel="stylesheet" href="{{ asset('front/vendors/linericon/style.css')}}">
  <link rel="stylesheet" href="{{ asset('front/vendors/owl-carousel/owl.theme.default.min.css')}}">
  <link rel="stylesheet" href="{{ asset('front/vendors/owl-carousel/owl.carousel.min.css')}}">

  <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>
<body>



    @yield('content')
    {{-- @include('front.layout.header') --}}
  <!--================Header Menu Area =================-->


    <!--================Hero Banner end =================-->

    <!--================ Blog slider start =================-->
    {{-- @include('front.layout.slider') --}}
    <!--================ Blog slider end =================-->

    <!--================ Start Blog Post Area =================-->
   {{-- @include('front.layout.main') --}}
    <!--================ End Blog Post Area =================-->


  <!--================ Start Footer Area =================-->
  {{-- @include('front.layout.footer') --}}
  <!--================ End Footer Area =================-->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="{{ asset('front/vendors/jquery/jquery-3.2.1.min.js')}}"></script>
  <script src="{{ asset('front/vendors/bootstrap/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('front/vendors/owl-carousel/owl.carousel.min.js')}}"></script>
  <script src="{{ asset('front/js/jquery.ajaxchimp.min.js')}}"></script>
  <script src="{{ asset('front/js/mail-script.js')}}"></script>
  <script src="{{ asset('front/js/main.js')}}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('failed'))
<script>
    Swal.fire({
        icon: 'error',
        title: "Error",
        text: "{{ session('failed') }}",
        showConfirmButton: true
    });
</script>
@endif

@if(session('duplicate'))
<script>
    Swal.fire({
        icon: 'info',
        title: 'Already Subscribed',
        text: "{{ session('duplicate') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

</body>

</html>
