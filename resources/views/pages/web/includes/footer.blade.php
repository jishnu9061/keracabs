<footer class="footer-wrapper footer-layout7" data-bg-src="{{ asset('home/img/bg/footer_bg_2.jpg') }}">
    <div class="widget-area">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-md-6 col-xl-auto">
                    <div class="widget footer-widget">
                        <h3 class="widget_title">About Company</h3>
                        <div class="th-widget-about">
                            <p class="footer-text">Welcome to Kera Cabs, where convenience meets reliability on the
                                road! Our commitment to excellence is evident in every aspect of our service.
                            </p>

                        </div>
                        <div class="th-social style2">
                            <a target="_blank" href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                            <a target="_blank" href="https://www.instagram.com/keracabsofficial/"><i
                                    class="fab fa-instagram"></i></a>

                            <a target="_blank" href="https://www.linkedin.com/company/keracabs"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="widget widget_nav_menu footer-widget">
                        <h3 class="widget_title">Quick link</h3>
                        <div class="menu-all-pages-container">
                            <ul class="menu">
                                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('about') }}">About Us</a></li>
                                <li><a href="{{ route('bookings') }}">Booking</a></li>
                                <li><a href="{{ route('blogs') }}">Blog</a></li>
                                <li><a href="{{ route('contacts') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="widget footer-widget">
                        <h3 class="widget_title">Contact Details</h3>
                        <div class="th-widget-about">
                            <h4 class="footer-info-title">Phone Number</h4>
                            <p class="footer-info"><i class="fa-sharp fa-solid fa-phone"></i><a class="text-inherit"
                                    href="tel:9446045678">+91 9446045678</a></p>
                            <h4 class="footer-info-title">Email Address</h4>
                            <p class="footer-info"><i class="fas fa-envelope"></i><a class="text-inherit"
                                    href="mailto:info@keracabs.com">info@keracabs.com</a></p>
                            <h4 class="footer-info-title">Office Location</h4>
                            <p class="footer-info"><i class="fas fa-map-marker-alt"></i>2nd Floor, ABS building ,
                                Opp.Edakkad Village office, Thottada, kizhunna PO,<br> Kannur-670007</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="copyright-wrap style2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <p class="copyright-text"><i class="fal fa-copyright"></i><span id="year"></span><a
                            href="#"> Kera Cabs.</a> All Rights Reserved.</p>
                </div>

            </div>
        </div>
    </div>
</footer>
<div class="scroll-top">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
        </path>
    </svg>
</div>
<div class="whatsappDiv">
    <a href="https://api.whatsapp.com/send?phone=919446045678"><img src="{{ asset('home/img/whatsapp.png') }}"></a>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</div>
<script src="{{ asset('home/js/vendor/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('home/js/slick.min.js') }}"></script>
<script src="{{ asset('home/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('home/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('home/js/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('home/js/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ asset('home/js/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('home/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('home/js/nice-select.min.js') }}"></script>
<script src="{{ asset('home/js/jquery.datetimepicker.min.js') }}"></script>
<script src="{{ asset('home/js/circle-progress.js') }}"></script>
<script src="{{ asset('home/js/fm.revealator.jquery.min.js') }}"></script>
<script src="{{ asset('home/js/wow.min.js') }}"></script>
<script src="{{ asset('home/js/main.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let dates;
    let yeares = document.getElementById('year');
    dates = new Date().getFullYear();
    yeares.innerText = dates;
</script>
</body>

</html>
