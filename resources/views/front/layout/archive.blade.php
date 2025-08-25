@php
    $categories = categories(); // Fetch categories
    $writerPosts = writerPosts(); // Fetch writer posts
@endphp

<section class="blog-post-area section-margin">
    <div class="container">
        <div class="row">

            <!-- Main Blog Posts -->
            <div class="col-lg-8">
                <div class="row">
                    @foreach ($writerPosts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="single-recent-blog-post card shadow-sm">
                                <div class="thumb">
                                    <img class="card-img-top rounded-0"
                                         src="{{ asset('admin/uploads/'. $post->image) }}"
                                         style="object-fit: cover; height: 200px; width: 100%;"
                                         alt="{{ $post->title }}">
                                    <ul class="thumb-info list-unstyled d-flex justify-content-between p-2 bg-light m-0">
                                        <li><a href="#"><i class="ti-user"></i> {{ $post->users->name }}</a></li>
                                        <li><a href="#"><i class="ti-comment"></i> {{ $post->comments_count ?? 0 }} Comments</a></li>
                                    </ul>
                                </div>
                                <div class="details p-3">
                                    <a href="{{ route('blogdetails', $post->id) }}">
                                        <h5 class="card-title">{{ $post->title }}</h5>
                                    </a>
                                    <p>{{ Str::limit($post->content, 200) }}</p>
                                    <a class="btn btn-primary btn-sm" href="{{ route('blogdetails', $post->id) }}">
                                        Read More <i class="ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="blog-pagination justify-content-center d-flex">
                            {{ $writerPosts->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 sidebar-widgets">
                <div class="widget-wrap">

                    <!-- Newsletter Widget -->
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

                    <!-- Categories Widget -->
                    <div class="single-sidebar-widget post-category-widget mb-4">
                        <h4 class="widget-title">Categories</h4>
                        <ul class="cat-list mt-3 list-unstyled">
                            @foreach ($categories as $cat)
                                <li class="mb-2">
                                    <a href="{{ route('blogdetails', $post->id) }}" class="d-flex justify-content-between">
                                        <p>{{ $cat->name }}</p>
                                        <p>({{ $cat->posts_count ?? 0 }})</p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Popular Categories Widget -->
                    <div class="single-sidebar-widget popular-post-widget mb-4">
                        <h4 class="widget-title">Popular Categories</h4>
                        <ul class="list-unstyled mt-3">
                            @foreach ($categories as $cat)
                                <li class="mb-2">
                                    <a href="{{ route('blogdetails', $post->id) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tags Widget -->
                    <div class="single-sidebar-widget tag_cloud_widget mb-4">
                        <h4 class="widget-title">Tags</h4>
                        <ul class="list-inline">
                            @foreach ($categories as $cat)
                                <li class="list-inline-item mb-1">
                                    <a href="{{ route('blogdetails', $post->id) }}" class="badge bg-secondary">{{ $cat->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
