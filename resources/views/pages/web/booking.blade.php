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
        <form action="{{ route('send-booking') }}" method="POST" class="booking-form-area"
            id="bookingForm">
            @csrf
            <div class="booking-title-area">
                <h4 class="booking-title">Make Your Booking</h4>
            </div>
            <div class="row">
                <div class="form-group col-sm-6"><input type="text" class="form-control" name="name"
                        id="name" placeholder="Your Name" /> <i class="fal fa-user"></i></div>
                <div class="form-group col-sm-6"><input type="number" class="form-control" name="number"
                        id="number" placeholder="Phone Number" /> <i class="fal fa-phone"></i></div>
                <div class="form-group col-sm-6"><input type="email" class="form-control" name="email"
                        id="email" placeholder="Email" /> <i class="fal fa-email"></i></div>

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
                </div>
                <div class="form-group col-12">
                    <textarea placeholder="Write a Message...." name="message" id="message" class="form-control"></textarea> <i class="fa-sharp fa-light fa-pencil"></i>
                </div>
                <div class="form-btn col-12">
                    <button type="submit" class="th-btn fw-btn" id="bookTaxiBtn">Book Taxi Now <i
                            class="fa-regular fa-arrow-right"></i></button>
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
<script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
<script>
   $(document).ready(function() {
    $('#bookingForm').submit(function(event) {
        $('#bookTaxiBtn').prop('disabled', true);
        if (!validateForm()) {
            $('#bookTaxiBtn').prop('disabled', false);
            return false;
        }
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#bookingForm')[0].reset();
                toastr.success('Booking submitted successfully');
                setTimeout(function() {
                    location.reload();
                }, 5000);
                $('#bookTaxiBtn').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON.message;
                $('.form-messages').html('<div class="alert alert-danger">' +
                    errorMessage + '</div>');
                $('#bookTaxiBtn').prop('disabled', false);
            }
        });
    });
    function validateForm() {
        var valid = true;
        var email = $('[name="email"]').val();
        if (!isValidEmail(email)) {
            valid = false;
            $('[name="email"]').addClass('is-invalid');
        } else {
            $('[name="email"]').removeClass('is-invalid');
        }
        var name = $('[name="name"]').val().trim();
        if (name === "") {
            valid = false;
            $('[name="name"]').addClass('is-invalid');
        } else {
            $('[name="name"]').removeClass('is-invalid');
        }
        var phoneNumber = $('[name="number"]').val().trim();
        if (phoneNumber === "") {
            valid = false;
            $('[name="number"]').addClass('is-invalid');
        } else {
            $('[name="number"]').removeClass('is-invalid');
        }
        var vehicle = $('[name="vehicle"]').val();
        if (vehicle === "") {
            valid = false;
            $('[name="vehicle"]').addClass('is-invalid');
        } else {
            $('[name="vehicle"]').removeClass('is-invalid');
        }

        var message = $('[name="message"]').val().trim();
        if (message === "") {
            valid = false;
            $('[name="message"]').addClass('is-invalid');
        } else {
            $('[name="message"]').removeClass('is-invalid');
        }

        return valid;
    }
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});

</script>
