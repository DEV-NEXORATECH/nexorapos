@php
    $posts=writerPosts();
@endphp

<section>
      <div class="container">
        <div class="owl-carousel owl-theme blog-slider">
            @foreach ($posts as $post)
                 <div class="card blog__slide text-center">
            <div class="blog__slide__img">
              <img class="card-img rounded-0" src="{{ asset('admin/uploads/'.$post->image)}}" alt="" width="200" height="200">
            </div>
            <div class="blog__slide__content">
              <a class="blog__slide__label" href="{{ route('blogdetails', $post->id) }}">{{ $post->categories->name }}</a>
              <h3><a href="{{ route('blogdetails', $post->id) }}">{{ $post->title }}</a></h3>
              <p>{{ $post->created_at }}</p>
            </div>
          </div>
            @endforeach


        </div>
      </div>
    </section>
