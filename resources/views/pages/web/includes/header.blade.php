<!DOCTYPE html>
<html dir="ltr" class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>@isset($blog) {{ $blog->title }} @else Default Title @endisset</title>
    <meta name="keywords" content="@isset($blog) {{ $blog->keyword }} @else Default Key @endisset" />
    <meta name="description" content="@isset($blog) {{ $blog->description }} @else Default description @endisset" />
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('home/img/bg/mini.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;family=Radio+Canada:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('home/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/fontawesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/magnific-popup.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/slick.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/jquery.datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/fm.revealator.jquery.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('home/css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

</head>

<body class="home2">
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo">
                <a href="{{ route('home') }}"><img src="{{ asset('home/img/keracab_logo.png') }}" alt="keracab" /></a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li><a href="index.html">Home</a> </li>
                    <li><a href="about.html">About Us</a> </li>
                    <li><a href="booking.html">Booking</a> </li>
                    <li><a href="blog.html">Blog</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>


    <header class="th-header header-layout8">
        <div class="header-top">
            <div class="container">
                <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto">
                        <div class="header-social">
                            <span class="social-title">Follow Us On:</span> <a href="https://www.facebook.com"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a target="_blank" href="https://www.instagram.com/keracabsofficial/"><i
                                    class="fab fa-instagram"></i></a>

                            <a target="_blank" href="https://www.linkedin.com/company/keracabs"><i
                                    class="fab fa-linkedin-in"></i></a>

                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="header-right">
                            <div class="header-links">
                                <ul>
                                    <li><i class="fa-thin fa-envelope"></i><a
                                            href="mailto:info@keracabs.com">info@keracabs.com</a></li>
                                    <li><i class="fa-thin fa-phone"></i><a href="tel:9446045678">+91 9446045678</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper">
            <div class="menu-area">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="{{ route('home') }}"><img src="{{ asset('home/img/keracab_logo.png') }}"
                                        alt="keracab" /></a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu d-none d-lg-block">
                                <ul>
                                    <li>
                                        <a href="{{ route('home') }}">Home</a>

                                    </li>
                                    <li>
                                        <a href="{{ route('about') }}">About Us</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('bookings') }}">Booking</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('driver') }}">Drivers Registraion</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('blogs') }}">Blog</a>
                                    </li>

                                    <li><a href="{{ route('contacts') }}">Contact</a></li>
                                </ul>
                            </nav>
                            <button class="th-menu-toggle d-inline-block d-lg-none"><i class="far fa-bars"></i></button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="logo-bg"></div>
        </div>
    </header>
