@include('pages.web.includes.header')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/abt.jpg') }}" data-overlay="title" data-opacity="4">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">Blog Details</h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Blog Details</li>
        </ul>
    </div>
</div>
<section class="th-blog-wrapper blog-details space-top space-extra-bottom">
    <div class="container">
        <div class="row gx-60">
            @if (!is_null($blog))
                <div class="col-lg-8">
                    <div class="th-blog blog-single style2">
                        <div class="blog-img"><img
                                src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($blog->image) }}"
                                alt="Blog Image" /></div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="blog.html"><i
                                        class="far fa-calendar-alt"></i>{{ $blog->created_at->format('F d, Y') }}</a>
                            </div>
                            <h2 class="blog-title">{{ $blog->title }}</h2>
                            <p>{!! $blog->description !!}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="th-blog blog-single has-post-thumbnail">
                    <p class="text-center">No blog details found.</p>
                </div>
            @endif
            @if (!is_null($blog))
                <div class="col-lg-4 ps-lg-2">
                    <aside class="sidebar-area">
                        <div class="widget">
                            <h3 class="widget_title">Recent Posts</h3>
                            <div class="recent-post-wrap">
                                @foreach ($recentPosts as $post)
                                    <div class="recent-post">
                                        <div class="media-img">
                                            <a href="{{ route('blog-detail', $post->id) }}"><img
                                                    src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($post->image) }}"
                                                    alt="Blog Image" /></a>
                                        </div>
                                        <div class="media-body">
                                            <div class="recent-post-meta">
                                                <a href="{{ route('blogs', $post->id) }}"><i
                                                        class="fas fa-calendar-alt"></i>{{ $post->created_at->format('F d, Y') }}</a>
                                            </div>
                                            <h4 class="post-title"><a class="text-inherit"
                                                    href="{{ route('blog-detail', $post->id) }}">{{ $post->title }}</a>
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
