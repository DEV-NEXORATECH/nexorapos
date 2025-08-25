@php
    $settings=settings();

@endphp
@php
    $footer=settings()->whereIn('key', ['about_us', 'newsletter', 'facebook','instagram','youtube', 'twitter'])->pluck('value', 'key')->toArray();
@endphp


<footer class="footer-area section-padding">
    <div class="container">
      <div class="row">
        <div class="col-lg-3  col-md-6 col-sm-6">
          <div class="single-footer-widget">
            <h6>About Us</h6>
            <p>
             <p>
                {{ $footer['about_us'] ?? 'About Us Text Here...' }}
            </p>
            </p>
          </div>
        </div>
        <div class="col-lg-4  col-md-6 col-sm-6">
          <div class="single-footer-widget">
            <h6>Newsletter</h6>
            <p> {{ $footer['newsletter'] ?? 'About Us Text Here...' }}</p>
            <div class="" id="mc_embed_signup">

           <form method="POST" action="{{ url('frontPanel/newsletter') }}" class="form-inline">
                @csrf
                <div class="d-flex flex-row">
                    <input class="form-control" name="email" placeholder="Enter Email"
                        onfocus="this.placeholder = ''"
                        onblur="this.placeholder = 'Enter Email'"
                        required type="email">

                    <button type="submit" class="click-btn btn btn-default">
                        <span class="lnr lnr-arrow-right"></span>
                    </button>
                </div>
            </form>


            </div>
          </div>
        </div>
        <div class="col-lg-3  col-md-6 col-sm-6">
          <div class="single-footer-widget mail-chimp">
            <h6 class="mb-20">Instragram Feed</h6>
            <ul class="instafeed d-flex flex-wrap">
              <li><img src="{{ asset('front/img/instagram/i1.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i2.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i3.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i4.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i5.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i6.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i7.jpg')}}" alt=""></li>
              <li><img src="{{ asset('front/img/instagram/i8.jpg')}}" alt=""></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="single-footer-widget">
            <h6>Follow Us</h6>
            <p>Let us be social</p>
            <div class="footer-social d-flex align-items-center">
                <a href="{{ $footer['facebook'] ?? '#' }}" ><i class="fab fa-facebook-f"></i></a>
                <a href="{{ $footer['instagram'] ?? '#' }}" ><i class="fab fa-instagram"></i></a>
                <a href="{{ $footer['youtube'] ?? '#' }}" ><i class="fab fa-youtube"></i></a>
                <a href="{{ $footer['twitter'] ?? '#' }}" ><i class="fab fa-twitter"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom d-flex justify-content-center align-items-center flex-wrap">
        <p class="footer-text m-0">
             {{ $footer['Copyright'] ?? 'About Us Text Here...' }}
        </p>
      </div>
    </div>
  </footer>
