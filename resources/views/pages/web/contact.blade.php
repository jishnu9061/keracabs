@include('pages.web.includes.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/cont.jpg') }}" data-overlay="title" data-opacity="4">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">Contact</h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Contact</li>
        </ul>
    </div>
</div>

<section class="space" id="contact-sec">
    <div class="container">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="nav-one" role="tabpanel" aria-labelledby="nav-one-tab">
                <div class="row gy-30 justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="contact-box">
                            <div class="contact-box_content">
                                <div class="contact-box_icon"><i class="fal fa-headset"></i></div>
                                <div class="contact-box_info">
                                    <p class="contact-box_text">Call Us</p>
                                    <h5 class="contact-box_link"><a href="tel:9446045678">+91 9446045678</a></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="contact-box">
                            <div class="contact-box_content">
                                <div class="contact-box_icon"><i class="fal fa-envelope-open-text"></i></div>
                                <div class="contact-box_info">
                                    <p class="contact-box_text">Mail us</p>
                                    <h5 class="contact-box_link"><a href="mailto:info@keracabs.com">info@keracabs.com</a></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="contact-box">
                            <div class="contact-box_content">
                                <div class="contact-box_icon"><i class="fal fa-map-location-dot"></i></div>
                                <div class="contact-box_info">
                                    <p class="contact-box_text">Address</p>
                                    <h5 class="contact-box_link">2nd Floor, ABS building, Opp. Edakkad Village office, Thottada, kizhunna PO, Kannur -670007.</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="space-bottom position-relative">
    <div class="container">
        <div class="contact-form-wrapper">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="map-sec">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d7810.034822010591!2d75.41374549059213!3d11.834054353545536!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1s2nd%20Floor%2CABS%20building%20%2COpp.%20Edakkad%20Village%20office%2CThottada%2Ckizhunna%20PO%2CKannur%20-670007.!5e0!3m2!1sen!2sin!4v1716887967496!5m2!1sen!2sin"
                            width="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form action="{{ route('send-mail') }}" method="POST" class="contact-form" id="contactForm">
                        @csrf
                        <div class="title-area mb-30 text-center text-lg-start">
                            <h2 class="sec-title">Get A <span class="text-theme">Free</span> Quote</h2>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Your Name" />
                                <i class="fal fa-user"></i>
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" />
                                <i class="fal fa-envelope"></i>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone Number" />
                                <i class="fa-light fa-phone"></i>
                                <div class="invalid-feedback">Please enter your phone number.</div>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" />
                                <i class="fa-light fa-pen"></i>
                                <div class="invalid-feedback">Please enter a subject.</div>
                            </div>
                            <div class="form-group col-12">
                                <textarea name="message" id="message" cols="30" rows="3" class="form-control" placeholder="Message"></textarea>
                                <i class="fal fa-comment"></i>
                                <div class="invalid-feedback">Please enter your message.</div>
                            </div>
                            <div class="form-btn col-12 text-center">
                                <button type="submit" class="th-btn fw-btn">Send Message<i class="fa-regular fa-arrow-right"></i></button>
                            </div>
                        </div>
                        <p class="form-messages mb-0 mt-3"></p>
                    </form>
                </div>
            </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRsN/hYZyoD1t3R2TgC8X5dB2T2syuYrO6+rLdU5e" crossorigin="anonymous">
</script>
<script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#contactForm').on('submit', function(event) {
            event.preventDefault();
            $('#contactForm button').prop('disabled', true);

            if (!validateForm()) {
                $('#contactForm button').prop('disabled', false);
                return false;
            }

            $.blockUI({ message: '<h4>Processing...</h4>' });

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $.unblockUI();
                    $('#contactForm')[0].reset();
                    toastr.success('Contact submitted successfully');
                    setTimeout(function() {
                        location.reload();
                    }, 5000);
                },
                error: function(xhr) {
                    $.unblockUI();
                    $('#contactForm button').prop('disabled', false);

                    if (xhr.status === 422) { 
                        var errors = xhr.responseJSON.errors;
                        $('.form-messages').empty();
                        $.each(errors, function(key, messages) {
                            var input = $('#' + key);
                            input.addClass('is-invalid');
                            var feedback = input.siblings('.invalid-feedback');
                            feedback.text(messages.join(' '));
                            feedback.show();
                        });
                    } else {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Oops! An error occurred.';
                        $('.form-messages').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    }
                }
            });
        });

        function validateForm() {
            var valid = true;
            var fields = ['name', 'email', 'phone', 'subject', 'message'];

            fields.forEach(function(field) {
                var value = $('[name="' + field + '"]').val().trim();
                var $field = $('[name="' + field + '"]');

                if (value === "" || (field === 'email' && !isValidEmail(value))) {
                    valid = false;
                    $field.addClass('is-invalid');
                } else {
                    $field.removeClass('is-invalid');
                }
            });

            return valid;
        }

        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>

