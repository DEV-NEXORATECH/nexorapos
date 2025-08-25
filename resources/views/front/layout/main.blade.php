@php
    $categories = categories();
    $writerPosts = writerPosts();
@endphp

<section class="blog-post-area section-margin mt-4">
    <div class="container">
        <div class="row">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                @foreach ($writerPosts as $post)
                    <div class="single-recent-blog-post mb-5 card shadow-sm border-0">
                        <div class="thumb position-relative overflow-hidden rounded-top">
                            <img class="img-fluid w-100"
                                 src="{{ asset('admin/uploads/'. $post->image) }}"
                                 alt="{{ $post->title }}"
                                 style="height: 250px; object-fit: cover; transition: transform 0.5s;">
                            <ul class="thumb-info list-unstyled position-absolute bottom-0 start-0 bg-dark bg-opacity-75 text-white p-2 m-0 rounded-end">
                                <li class="d-inline me-3"><i class="ti-user"></i> {{ $post->users->name }}</li>
                                <li class="d-inline me-3"><i class="ti-notepad"></i> {{ $post->created_at->format('M d, Y') }}</li>
                                <li class="d-inline"><i class="ti-comments"></i> 2 Comments</li>
                            </ul>
                        </div>
                        <div class="details mt-3 p-3">
                            <a href="{{ route('blogdetails', $post->id) }}" class="text-dark text-decoration-none">
                                <h3 class="hover-text-primary">{{ $post->title }}</h3>
                            </a>
                            <p class="text-muted">{{ Str::limit($post->content, 250) }}</p>
                            <a href="{{ route('blogdetails', $post->id) }}" class="btn btn-outline-primary btn-sm mt-2">Read More <i class="ti-arrow-right"></i></a>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="blog-pagination justify-content-center d-flex mt-4">
                            {{ $writerPosts->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
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


                    <!-- Categories -->
                    <div class="single-sidebar-widget post-category-widget mb-4 card p-3 shadow-sm border-0">
                        <h4 class="single-sidebar-widget__title mb-3">Categories</h4>
                        <ul class="cat-list mt-2 list-unstyled">
                            @foreach ($categories as $cats)
                                <li class="mb-2">
                                    <a href="{{ route('blogdetails', $post->id) }}" class="d-flex justify-content-between text-decoration-none text-dark p-2 rounded hover-bg-primary">
                                        <p>{{ $cats->name }}</p>
                                        <p>{{ $cats->id }}</p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Popular Posts -->
                    <div class="single-sidebar-widget popular-post-widget mb-4 card p-3 shadow-sm border-0">
                        <h4 class="single-sidebar-widget__title mb-3">Popular Posts</h4>
                        <div class="popular-post-list">
                            @foreach($writerPosts->take(3) as $post)
                                <div class="single-post-list d-flex mb-3">
                                    <div class="details">
                                        <a href="{{ route('blogdetails', $post->id) }}" class="text-decoration-none text-dark hover-text-primary">
                                            <h6>{{ $post->title }}</h6>
                                        </a>
                                        <p class="text-muted small">{{ $post->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="single-sidebar-widget tag_cloud_widget mb-4 card p-3 shadow-sm border-0">
                        <h4 class="single-sidebar-widget__title mb-3">Tags</h4>
                        <ul class="list-inline mt-2">
                            @foreach($categories as $tag)
                                <li class="list-inline-item mb-1">
                                    <a href="{{ route('blogdetails', $post->id) }}" class="badge bg-primary text-white px-2 py-1">{{ $tag->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom Hover Styles -->
<style>
    .hover-text-primary:hover {
        color: #0d6efd !important;
    }
    .hover-bg-primary:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .single-recent-blog-post .thumb:hover img {
        transform: scale(1.05);
    }
</style>
