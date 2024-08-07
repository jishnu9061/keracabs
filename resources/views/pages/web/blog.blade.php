@include('pages.web.includes.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/abt.jpg') }}">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">Blog</h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Blog</li>
        </ul>
    </div>
</div>
<section class="th-blog-wrapper blog-details space-top space-extra-bottom">
    <div class="container">
        <div class="row flex-row-reverse">
            <div class="col-lg-8">
                @if (!is_null($blogs))
                    <div class="row">
                        @foreach ($blogs as $blog)
                            <div class="col-lg-6 col-xl-6">
                                <div class="th-blog blog-single has-post-thumbnail">
                                    <div class="blog-img">
                                        <a href="{{ route('blog-detail', $blog->slug) }}">
                                            <img src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($blog->image) }}"
                                                alt="{{ $blog->image_alt }}" />
                                        </a>
                                    </div>
                                    <div class="blog-content">
                                        <div class="blog-meta">
                                            <a href="{{ route('blog-detail', $blog->slug) }}">
                                                <i
                                                    class="fas fa-calendar-alt"></i>{{ $blog->created_at->format('F d, Y') }}
                                            </a>
                                        </div>
                                        <h2 class="blog-title">
                                            <a href="{{ route('blog-detail', $blog->slug) }}">{{ $blog->title }}</a>
                                        </h2>
                                        <p>{{ Str::limit(strip_tags($blog->blog_details), 150, '...') }}</p>
                                        <a href="{{ route('blog-detail', $blog->slug) }}" class="th-btn">Read Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="th-blog blog-single has-post-thumbnail">
                        <p class="text-center">No blogs found.</p>
                    </div>
                @endif
            </div>
            @if (!is_null($recentBlogs))
                <div class="col-lg-4 ps-lg-2">
                    <aside class="sidebar-area">
                        <div class="widget">
                            <h3 class="widget_title">Recent Posts</h3>
                            <div class="recent-post-wrap">
                                @foreach ($recentBlogs as $recentBlog)
                                    <div class="recent-post">
                                        <div class="media-img">
                                            <a href="{{ route('blog-detail', $recentBlog->slug) }}">
                                                <img src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($recentBlog->image) }}"
                                                    alt="{{ $recentBlog->image_alt }}" />
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <div class="recent-post-meta">
                                                <a href="{{ route('blog-detail', $recentBlog->slug) }}">
                                                    <i
                                                        class="fas fa-calendar-alt"></i>{{ $recentBlog->created_at->format('F d, Y') }}
                                                </a>
                                            </div>
                                            <h4 class="post-title">
                                                <a class="text-inherit"
                                                    href="{{ route('blog-detail', $recentBlog->slug) }}">
                                                    {{ Str::limit($recentBlog->title, 50) }}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </div>
            @else
                <div class="col-lg-4 ps-lg-2">
                    <aside class="sidebar-area">
                        <div class="widget">
                            <h3 class="widget_title">Recent Posts</h3>
                            <p>No recent blogs found.</p>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</section>


<div class="scroll-top">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
        </path>
    </svg>
</div>
<div class="whatsappDiv">
    <a href="https://api.whatsapp.com/send?phone=919446045678"><img src="{{ asset('home/img/whatsapp.png') }}"></a>
</div>
@include('pages.web.includes.footer')
