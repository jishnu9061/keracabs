@include('pages.web.includes.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/bok.jpg') }}" data-overlay="title" data-opacity="4">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">Booking</h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Booking</li>
        </ul>
    </div>
</div>

<div class="position-relative space">
    <div class="container">
        <form action="{{ route('send-booking') }}" method="POST" class="booking-form-area" id="bookingForm">
            @csrf
            <div class="booking-title-area">
                <h4 class="booking-title">Make Your Booking</h4>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" />
                    <i class="fal fa-user"></i>
                    <div class="invalid-feedback">Please enter your name.</div>
                </div>
                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="number" id="number" placeholder="Phone Number" />
                    <i class="fal fa-phone"></i>
                    <div class="invalid-feedback">Please enter your phone number.</div>
                </div>
                <div class="form-group col-sm-6">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" />
                    <i class="fal fa-email"></i>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="form-group col-sm-6">
                    <select class="form-control" name="vehicle" id="vehicle">
                        <option value="">-- Select car --</option>
                        <option value="Sedan">Sedan</option>
                        <option value="Suv">Suv</option>
                        <option value="Suv Premium">Suv Premium</option>
                        <option value="12 TT A/C">12 TT A/C</option>
                        <option value="17 TT A/C">17 TT A/C</option>
                        <option value="26 TT A/C">26 TT A/C</option>
                    </select>
                    <div class="invalid-feedback">Please select a vehicle.</div>
                </div>
                <div class="form-group col-12">
                    <textarea placeholder="Write a Message...." name="message" id="message" class="form-control"></textarea>
                    <i class="fa-sharp fa-light fa-pencil"></i>
                    <div class="invalid-feedback">Please write a message.</div>
                </div>
                <div class="form-btn col-12">
                    <button type="submit" class="th-btn fw-btn" id="bookTaxiBtn">Book Taxi Now <i class="fa-regular fa-arrow-right"></i></button>
                </div>
            </div>
            <p class="form-messages mb-0 mt-3"></p>
        </form>
    </div>
</div>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bookingForm').on('submit', function(event) {
            event.preventDefault();
            $('#bookTaxiBtn').prop('disabled', true);

            if (!validateForm()) {
                $('#bookTaxiBtn').prop('disabled', false);
                return false;
            }

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#bookingForm')[0].reset();
                    toastr.success('Booking submitted successfully');
                    setTimeout(function() {
                        location.reload();
                    }, 5000);
                },
                error: function(xhr) {
                    var errorMessages = xhr.responseJSON ? xhr.responseJSON.errors : {};
                    $('.form-messages').html('<div class="alert alert-danger">' +
                        (xhr.responseJSON.message || 'An error occurred') + '</div>');

                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    // Display error messages for each field
                    $.each(errorMessages, function(key, value) {
                        var field = $('[name="' + key + '"]');
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">' + value.join('<br>') + '</div>');
                    });

                    $('#bookTaxiBtn').prop('disabled', false);
                }
            });
        });

        function validateForm() {
            var valid = true;

            // Remove previous error messages
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var fields = ['name', 'number', 'email', 'vehicle', 'message'];

            fields.forEach(function(field) {
                var value = $('[name="' + field + '"]').val().trim();
                if (value === "") {
                    valid = false;
                    var fieldElement = $('[name="' + field + '"]');
                    fieldElement.addClass('is-invalid');
                    fieldElement.after('<div class="invalid-feedback">This field is required</div>');
                }
            });
            var email = $('[name="email"]').val().trim();
            if (!isValidEmail(email)) {
                valid = false;
                $('[name="email"]').addClass('is-invalid');
                $('[name="email"]').after('<div class="invalid-feedback">Please enter a valid email address</div>');
            }

            return valid;
        }

        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>
