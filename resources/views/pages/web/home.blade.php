@include('pages.web.includes.header')
@if ($slides->isNotEmpty())
    <div class="th-hero-wrapper hero-8">
        <div class="hero-slider-8 th-carousel" id="heroSlide8" data-slide-show="1" data-md-slide-show="1" data-fade="true">
            @foreach ($slides as $slide)
                <div class="th-hero-slide">
                    <div class="th-hero-bg"
                        data-bg-src="{{ \App\Http\Helpers\BlogHelper::getBannerImagePath($slide->image) }}"></div>
                    <div class="container">
                        <div class="hero-style8">

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
<section class="space-top" id="about-sec">
    <div class="container">
        <div class="row reverse-column">
            <div class="col-xl-6 mb-40 mb-xl-0">
                <div class="img-box8 wow fadeInLeft">
                    <div class="img1"><img src="{{ asset('home/img/bg/2.jpg') }}" alt="About" /></div>
                    <div class="year-counter">
                        <div class="year-counter_number">6+</div>
                        <div class="media-body"><span class="year-counter_title">Years Experiences</span></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight">
                <div class="title-area mb-35">
                    <span class="sub-title style2"><img src="{{ asset('home/img/bg/mini.png') }}" alt="shape" /><span
                            class="sub-title2">ABOUT OUR COMPANY</span></span>
                    <h2 class="sec-title">Ride into the Future with Kera Cabs</h2>
                    <p class="mb-20">Welcome to Kera Cabs, where convenience meets reliability on the road! Our
                        commitment to excellence is evident in every aspect of our service. At Kera Cabs, we
                        redefine customer support as our dedicated team ensures direct and immediate replies to your
                        inquiries, whether through chat or with the assistance of our advanced technology since we
                        believe in the personal touch. </p>
                </div>
                <div class="about-feature-wrap style2">
                    <div class="about-feature">
                        <div class="about-icons"><img src="{{ asset('home/img/icon/about-feature_1.svg') }}"
                                class="abt-icn" alt="Icon" /></div>
                        <div class="media-body">
                            <h3 class="about_title">Safe Guarantee</h3>
                            <p class="about_text">Safety is paramount in every journey with us.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about_icons"><img src="{{ asset('home/img/icon/about-feature_2.svg') }}"
                                class="abt-icn" alt="Icon" /></div>
                        <div class="media-body">
                            <h3 class="about_title">Punctual & Professional</h3>
                            <p class="about_text">Count on timely pickups and courteous drivers for a hassle-free
                                experience.</p>
                        </div>
                    </div>
                </div>
                <div class="btn-group style2">
                    <a href="about.html" class="th-btn radius-btn">Discover More<i
                            class="fa-regular fa-arrow-right ms-2"></i></a>
                    <span class="about-call-text">
                        <a href="tel:9446045678" class="about-call-btn"><i class="fa-solid fa-phone"></i></a>+91
                        9446045678
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="space">
    <div class="container">
        <div class="counter-wrap style3">
            <div class="counter-line"></div>
            <div class="row gy-40">
                <div class="col-sm-6 col-lg-3 col-6">
                    <div class="counter-card style3 wow fadeInUp">
                        <div class="counter-card_icon" data-bg-src="{{ asset('home/img/bg/sh1.png') }}"><img
                                src="{{ asset('home/img/icon/counter_3_1.svg') }}" alt="icon" /></div>
                        <h3 class="counter-card_number"><span class="counter-number">32.5</span>k<span
                                class="counter-plus">+</span></h3>
                        <p class="counter-card_text">Special Vehicles</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 col-6">
                    <div class="counter-card style3 wow fadeInUp">
                        <div class="counter-card_icon" data-bg-src="{{ asset('home/img/bg/sh1.png') }}"><img
                                src="{{ asset('home/img/icon/counter_3_2.svg') }}" alt="icon" /></div>
                        <h3 class="counter-card_number"><span class="counter-number">13.8</span>k<span
                                class="counter-plus">+</span></h3>
                        <p class="counter-card_text">People Pickup</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 col-6">
                    <div class="counter-card style3 wow fadeInUp">
                        <div class="counter-card_icon" data-bg-src="{{ asset('home/img/bg/sh1.png') }}"><img
                                src="{{ asset('home/img/icon/counter_3_3.svg') }}" alt="icon" /></div>
                        <h3 class="counter-card_number"><span class="counter-number">26.6</span>k<span
                                class="counter-plus">+</span></h3>
                        <p class="counter-card_text">Road Trips Done</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 col-6">
                    <div class="counter-card style3 wow fadeInUp">
                        <div class="counter-card_icon" data-bg-src="{{ asset('home/img/bg/sh1.png') }}"><img
                                src="{{ asset('home/img/icon/counter_3_4.svg') }}" alt="icon" /></div>
                        <h3 class="counter-card_number"><span class="counter-number">65.2</span>k<span
                                class="counter-plus">+</span></h3>
                        <p class="counter-card_text">Satisfied Clients</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="taxi-area2" id="taxi-sec" data-bg-src="{{ asset('home/img/bg/taxi_bg_3.jpg') }}">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-xl-7 col-xxl-6 wow fadeInLeft">
                <div class="title-area">
                    <span class="sub-title style2"><img src="{{ asset('home/img/bg/mini.png') }}"
                            alt="shape" /><span class="sub-title2">WHY CHOOSE US</span></span>
                    <h2 class="sec-title">We’re The Best Taxi Service In Your Town</h2>
                </div>
                <div class="taxi-tabs-wrapper">
                    <div class="nav nav-tabs taxi-tabs-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-step1-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-step1" type="button">Our Mission</button>
                        <button class="nav-link" id="nav-step2-tab" data-bs-toggle="tab" data-bs-target="#nav-step2"
                            type="button">Our Vision</button>
                        <button class="nav-link" id="nav-step3-tab" data-bs-toggle="tab" data-bs-target="#nav-step3"
                            type="button">Why Keracabs</button>
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="nav-step1" role="tabpanel">
                            <div class="taxi-list">
                                <p class="taxi-title">Our goal is to simplify travel, enhance convenience, and
                                    elevate experiences, ensuring
                                    that every ride with us is a step towards a smarter, more connected world.

                                </p>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-step2" role="tabpanel">
                            <div class="taxi-list">
                                <p class="taxi-title">At Kera Cabs, our vision is to redefine transportation,
                                    making it accessible and efficient
                                    for everyone. We strive to provide seamless connectivity through innovative
                                    technology
                                    and exceptional service</p>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-step3" role="tabpanel">
                            <div class="taxi-list">
                                <p class="taxi-title">
                                    Choose Kera Cabs for your journey and discover the difference. With a commitment
                                    to
                                    reliability, safety, and convenience, we're your trusted partner in
                                    transportation.
                                    Experience seamless booking, courteous drivers, and top-notch service every time
                                    you
                                    ride with us. Join the Kera Cabs community and let us take you where you need to
                                    go,
                                    with ease and peace of mind</p>
                                <div class="taxi-list_wrapper">
                                    <div class="checklist">
                                        <ul>
                                            <li>Reliability: Punctual pickups and timely arrivals.
                                            </li>
                                            <li>Safety: Your safety is our priority</li>
                                            <li>Ease of Booking: Quick, simple, and stress-free.</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-xxl-6">
                <div class="taxi-image wow fadeInRight"><img src="{{ asset('home/img/bg/keracar.png') }}"
                        alt="" /></div>
            </div>
        </div>
    </div>
</section>

<section class="position-relative overflow-hidden space">
    <div class="container">
        <div class="title-area text-center">
            <span class="sub-title style2"><img src="{{ asset('home/img/bg/mini.png') }}" alt="shape" /><span
                    class="sub-title2">Our Working Process</span></span>
            <h2 class="sec-title">Step into simplicity with Kera Cabs! </br> Here's how we roll</h2>
        </div>
        <div class="process-box-wrapper style2">
            <div class="row gy-30 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="process-item wow fadeInLeft" data-bg-src="{{ asset('home/img/process_shape.png') }}">
                        <div class="process-item_icon"><img src="{{ asset('home/img/icon/process_1_1.svg') }}"
                                alt="" /></div>
                        <h3 class="process-item_title">Book with Ease</h3>
                        <p class="process-item_text">Simply hop on our user-friendly app or website to book your
                            ride in seconds.</p>
                        <span class="process-item_num">01</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-item wow fadeInUp" data-bg-src="{{ asset('home/img/process_shape.png') }}">
                        <div class="process-item_icon"><img src="{{ asset('home/img/icon/process_1_2.svg') }}"
                                alt="" /></div>
                        <h3 class="process-item_title">Track & Go</h3>
                        <p class="process-item_text">Follow your driver's progress in real-time and step out with
                            confidence when they arrive</p>
                        <span class="process-item_num">02</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-item wow fadeInRight"
                        data-bg-src="{{ asset('home/img/process_shape.png') }}">
                        <div class="process-item_icon"><img src="{{ asset('home/img/icon/process_1_3.svg') }}"
                                alt="" /></div>
                        <h3 class="process-item_title">Ride in Comfort</h3>
                        <p class="process-item_text">Experience the comfort of our well-maintained vehicles and
                            courteous drivers</p>
                        <span class="process-item_num">03</span>
                    </div>
                </div>
                <span class="process-line"><img src="{{ asset('home/img/process_line_3.png') }}"
                        alt="line" /></span>
            </div>
        </div>
    </div>
</section>
<div class="position-relative overflow-hidden bg-top-center space"
    data-bg-src="keracabs/assets/img/bg/booking_bg_1.jpg">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 wow fadeInLeft">
                <div class="title-area text-xl-start">
                    <span class="sub-title style2"><img src="{{ asset('home/img/bg/mini.png') }}"
                            alt="shape" /><span class="sub-title2">Taxi Booking</span></span>
                    <h2 class="sec-title text-white">Book Your Taxi Online</h2>
                    <p class="booking-text">Every ride with us is a step towards a smarter, more connected world.
                    </p>
                </div>
                <div class="d-flex justify-content-center justify-content-xl-start">
                    <div class="info-card style4">

                        <div class="info-card_content">
                            <a href="booking.html" class="th-btn radius-btn">Discover More<i
                                    class="fa-regular fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-6">
                <img src="{{ asset('home/img/bg/book_car.png') }}" class="wow fadeInRight" />
            </div>
        </div>
    </div>
</div>
@if ($blogs->isNotEmpty())
    <section class="blog-area" id="blog-sec">
        <div class="container">
            <div class="title-area text-center">
                <span class="sub-title style2">
                    <img src="{{ asset('home/img/bg/mini.png') }}" alt="shape" />
                    <span class="sub-title2">Our News Update</span>
                </span>
                <h2 class="sec-title">Latest News & Blog Post</h2>
            </div>
            <div class="row th-carousel slider-shadow" data-slide-show="3" data-lg-slide-show="2"
                data-md-slide-show="2" data-sm-slide-show="1" data-arrows="true" data-xl-arrows="true"
                data-ml-arrows="true">
                @foreach ($blogs as $blog)
                    <div class="col-md-6 col-xl-4">
                        <div class="blog-item style3 wow fadeInUp">
                            <div class="blog-img">
                                <img src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($blog->image) }}"
                                    alt="blog image" />
                                <a class="blog-date" href="{{ route('blog-detail', $blog->id) }}">
                                    <span class="month">{{ $blog->created_at->format('d') }}</span>
                                    {{ $blog->created_at->format('F, Y') }}
                                </a>
                            </div>
                            <div class="blog-content">
                                <h3 class="blog-item_title mt-4">
                                    <a href="{{ route('blog-detail', $blog->id) }}">{{ $blog->title }}</a>
                                </h3>
                                <a href="{{ route('blog-detail', $blog->id) }}" class="link-btn">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
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
