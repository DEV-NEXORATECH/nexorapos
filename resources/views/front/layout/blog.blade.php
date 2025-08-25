@php
    $categories=categories();
@endphp
<section class="blog-post-area section-margin">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
            <div class="main_blog_details">
                <img class="img-fluid" src="{{ asset('admin/uploads/'. $post->image) }}" alt="">
                <a href="#"><h4>{{ $post->title }}</h4></a>
                <div class="user_details">
                  <div class="float-left">
                    <a href="#">{{ $post->categories->name }}</a>
                    <a href="#">{{ $post->status }}</a>
                  </div>
                  <div class="float-right mt-sm-0 mt-3">
                    <div class="media">
                      <div class="media-body">
                        <h5>{{ $post->users->name }}</h5>
                        <p>{{ $post->created_at->format('M d, Y')}}</p>
                      </div>
                      <div class="d-flex">
                        <img width="42" height="42" src="{{ asset('front/img/blog/user-img.png') }}" alt="">
                      </div>
                    </div>
                  </div>
                </div>


           <p>{{ $post->content }}</p>
               <div class="news_d_footer flex-column flex-sm-row">
                 <a href="#"><span class="align-middle mr-2"><i class="ti-heart"></i></span>Lily and 4 people like this</a>
                 <a class="justify-content-sm-center ml-sm-auto mt-sm-0 mt-2" href="#"><span class="align-middle mr-2"><i class="ti-themify-favicon"></i></span>06 Comments</a>
                 <div class="news_socail ml-sm-auto mt-sm-0 mt-2">
               <a href="#"><i class="fab fa-facebook-f"></i></a>
               <a href="#"><i class="fab fa-twitter"></i></a>
               <a href="#"><i class="fab fa-dribbble"></i></a>
               <a href="#"><i class="fab fa-behance"></i></a>
             </div>
               </div>
              </div>


          <div class="navigation-area">
                  <div class="row">
                      <div class="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center">
                          <div class="thumb">
                              <a href="#"><img class="img-fluid" src="{{ asset('front/img/blog/prev.jpg') }}" alt=""></a>
                          </div>
                          <div class="arrow">
                              <a href="#"><span class="lnr text-white lnr-arrow-left"></span></a>
                          </div>
                          <div class="detials">
                              <p>Prev Post</p>
                              <a href="#"><h4>A Discount Toner</h4></a>
                          </div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center">
                          <div class="detials">
                              <p>Next Post</p>
                              <a href="#"><h4>Cartridge Is Better</h4></a>
                          </div>
                          <div class="arrow">
                              <a href="#"><span class="lnr text-white lnr-arrow-right"></span></a>
                          </div>
                          <div class="thumb">
                              <a href="#"><img class="img-fluid" src="{{ asset('front/img/blog/next.jpg') }}" alt=""></a>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="comments-area">
                    <h4>05 Comments</h4>
                    <div class="comment-list">
                        <div class="single-comment justify-content-between d-flex">
                            <div class="user justify-content-between d-flex">
                                <div class="thumb">
                                    <img src="{{ asset('front/img/blog/c1.jpg') }}" alt="">
                                </div>
                                <div class="desc">
                                    <h5><a href="#">Emilly Blunt</a></h5>
                                    <p class="date">December 4, 2017 at 3:12 pm </p>
                                    <p class="comment">
                                        Never say goodbye till the end comes!
                                    </p>
                                </div>
                            </div>
                            <div class="reply-btn">
                                    <a href="" class="btn-reply text-uppercase">reply</a>
                            </div>
                        </div>
                    </div>
                    <div class="comment-list left-padding">
                        <div class="single-comment justify-content-between d-flex">
                            <div class="user justify-content-between d-flex">
                                <div class="thumb">
                                    <img src="{{ asset('front/img/blog/c2.jpg') }}" alt="">
                                </div>
                                <div class="desc">
                                    <h5><a href="#">Elsie Cunningham</a></h5>
                                    <p class="date">December 4, 2017 at 3:12 pm </p>
                                    <p class="comment">
                                        Never say goodbye till the end comes!
                                    </p>
                                </div>
                            </div>
                            <div class="reply-btn">
                                    <a href="" class="btn-reply text-uppercase">reply</a>
                            </div>
                        </div>
                    </div>
                    <div class="comment-list left-padding">
                        <div class="single-comment justify-content-between d-flex">
                            <div class="user justify-content-between d-flex">
                                <div class="thumb">
                                    <img src="{{ asset('front/img/blog/c3.jpg') }}" alt="">
                                </div>
                                <div class="desc">
                                    <h5><a href="#">Annie Stephens</a></h5>
                                    <p class="date">December 4, 2017 at 3:12 pm </p>
                                    <p class="comment">
                                        Never say goodbye till the end comes!
                                    </p>
                                </div>
                            </div>
                            <div class="reply-btn">
                                    <a href="" class="btn-reply text-uppercase">reply</a>
                            </div>
                        </div>
                    </div>
                    <div class="comment-list">
                        <div class="single-comment justify-content-between d-flex">
                            <div class="user justify-content-between d-flex">
                                <div class="thumb">
                                    <img src="{{ asset('front/img/blog/c4.jpg') }}" alt="">
                                </div>
                                <div class="desc">
                                    <h5><a href="#">Maria Luna</a></h5>
                                    <p class="date">December 4, 2017 at 3:12 pm </p>
                                    <p class="comment">
                                        Never say goodbye till the end comes!
                                    </p>
                                </div>
                            </div>
                            <div class="reply-btn">
                                    <a href="" class="btn-reply text-uppercase">reply</a>
                            </div>
                        </div>
                    </div>
                    <div class="comment-list">
                        <div class="single-comment justify-content-between d-flex">
                            <div class="user justify-content-between d-flex">
                                <div class="thumb">
                                    <img src="{{ asset('front/img/blog/c5.jpg') }}" alt="">
                                </div>
                                <div class="desc">
                                    <h5><a href="#">Ina Hayes</a></h5>
                                    <p class="date">December 4, 2017 at 3:12 pm </p>
                                    <p class="comment">
                                        Never say goodbye till the end comes!
                                    </p>
                                </div>
                            </div>
                            <div class="reply-btn">
                                    <a href="" class="btn-reply text-uppercase">reply</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="comment-form">
                    <h4>Leave a Reply</h4>
                     <form action="{{ route('contactPage') }}" method="POST" id="contactForm" class="form-contact contact_form" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <input class="form-control" name="name" id="name" type="text" placeholder="Enter your name">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" name="email" id="email" type="email" placeholder="Enter email address" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" name="subject" id="subject" type="text" placeholder="Enter Subject">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <textarea class="form-control different-control w-100" name="message" id="message" cols="30" rows="5" placeholder="Enter Message"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center text-md-right mt-3">
                            <button type="submit" class="button button--active button-contactForm">Send Message</button>
                        </div>
                    </form>
                </div>
        </div>

        <!-- Start Blog Post Siddebar -->
        <div class="col-lg-4 sidebar-widgets">
            <div class="widget-wrap">
              <div class="single-sidebar-widget newsletter-widget mb-4">
                        <h4 class="widget-title">Newsletter</h4>

                        <form action="{{ url('frontPanel/newsletter') }}" method="POST">
                            @csrf

                            <div class="form-group mt-3">
                                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                            </div>

                            <button type="submit" class="btn btn-primary mt-2 w-100">Subscribe</button>
                        </form>
                    </div>


              <div class="single-sidebar-widget post-category-widget">
                <h4 class="single-sidebar-widget__title">Catgory</h4>
                <ul class="cat-list mt-20">
                  @foreach ($categories as $cats)
                    <li>
                    <a href="#" class="d-flex justify-content-between">
                      <p>{{ $cats->name }}</p>
                      <p>(03)</p>
                    </a>
                  </li>

                    @endforeach

                </ul>
              </div>

              {{-- <div class="single-sidebar-widget popular-post-widget">
                <h4 class="single-sidebar-widget__title">Popular Post</h4>
                <div class="popular-post-list">
                  <div class="single-post-list">
                    <div class="thumb">
                      <img class="card-img rounded-0" src="{{ asset('front/img/blog/thumb/thumb1.png')}}" alt="">
                      <ul class="thumb-info">
                        <li><a href="#">Adam Colinge</a></li>
                        <li><a href="#">Dec 15</a></li>
                      </ul>
                    </div>
                    <div class="details mt-20">
                      <a href="blog-single.html">
                        <h6>Accused of assaulting flight attendant miktake alaways</h6>
                      </a>
                    </div>
                  </div>
                  <div class="single-post-list">
                    <div class="thumb">
                      <img class="card-img rounded-0" src="{{ asset('front/img/blog/thumb/thumb2.png')}}" alt="">
                      <ul class="thumb-info">
                        <li><a href="#">Adam Colinge</a></li>
                        <li><a href="#">Dec 15</a></li>
                      </ul>
                    </div>
                    <div class="details mt-20">
                      <a href="blog-single.html">
                        <h6>Tennessee outback steakhouse the
                          worker diagnosed</h6>
                      </a>
                    </div>
                  </div>
                  <div class="single-post-list">
                    <div class="thumb">
                      <img class="card-img rounded-0" src="{{ asset('front/img/blog/thumb/thumb3.png') }}" alt="">
                      <ul class="thumb-info">
                        <li><a href="#">Adam Colinge</a></li>
                        <li><a href="#">Dec 15</a></li>
                      </ul>
                    </div>
                    <div class="details mt-20">
                      <a href="blog-single.html">
                        <h6>Tennessee outback steakhouse the
                          worker diagnosed</h6>
                      </a>
                    </div>
                  </div>
                </div>
              </div>

                <div class="single-sidebar-widget tag_cloud_widget">
                  <h4 class="single-sidebar-widget__title">Popular Post</h4>
                  <ul class="list">
                    <li>
                        <a href="#">project</a>
                    </li>
                    <li>
                        <a href="#">love</a>
                    </li>
                    <li>
                        <a href="#">technology</a>
                    </li>
                    <li>
                        <a href="#">travel</a>
                    </li>
                    <li>
                        <a href="#">software</a>
                    </li>
                    <li>
                        <a href="#">life style</a>
                    </li>
                    <li>
                        <a href="#">design</a>
                    </li>
                    <li>
                        <a href="#">illustration</a>
                    </li>
                  </ul>
                </div> --}}
              </div>
            </div>
          </div>
        <!-- End Blog Post Siddebar -->
      </div>
  </section>
