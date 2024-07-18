@include('pages.web.includes.header')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/blog.jpg') }}" data-overlay="title" data-opacity="4">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">About </h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>About</li>
        </ul>
    </div>
</div>
<section class="space" id="about-sec">
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
                    <h2 class="sec-title">Ride into the Future with Keracabs</h2>
                    <p class="mb-20">Welcome to Kera Cabs, where convenience meets reliability on the road! Our
                        commitment to excellence is evident in every aspect of our service. At Kera Cabs, we
                        redefine customer support as our dedicated team ensures direct and immediate replies
                        to your inquiries, whether through chat or with the assistance of our advanced
                        technology since we believe in the personal touch. Safety is paramount in every journey
                        with us. Our multilingual and experienced drivers, PCC certified for your peace of mind,
                        ensure that every passenger, including ladies, can travel securely at any time, to any
                        destination. With our user-friendly mobile applications, booking your ride is just a few
                        taps away. We guarantee prompt assistance and provide alternative vehicles promptly
                        in case of any vehicle issues even in remote and rural areas, ensuring your journey
                        continues without interruption. At Kera Cans, we prioritize your needs throughout your
                        journey, making every moment with us comfortable, safe, and convenient. Say goodbye
                        to waiting and hello to convenience with Kera Cabs.</p>
                </div>

                <div class="btn-group style2">
                    <span class="about-call-text">
                        <a href="tel:9446045678" class="about-call-btn"><i class="fa-solid fa-phone"></i></a>+91
                        9446045678
                    </span>
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
                    <span class="sub-title style2"><img src="{{ asset('home/img/bg/mini.png') }}" alt="shape" /><span
                            class="sub-title2">WHY CHOOSE US</span></span>
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
@include('pages.web.includes.footer')
