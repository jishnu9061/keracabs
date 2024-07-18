@include('pages.web.includes.header')

<div class="breadcumb-wrapper" data-bg-src="{{ asset('home/img/bok.jpg') }}" data-overlay="title" data-opacity="4">
    <div class="container z-index-common">
        <h1 class="breadcumb-title">Driver Registration</h1>
        <ul class="breadcumb-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Driver Registration</li>
        </ul>
    </div>
</div>
<div class="position-relative space">
    <div class="container">
        <form action="{{ route('send-registration') }}" method="POST" class="booking-form-area" id="bookingForm">
            @csrf
            <div class="booking-title-area">
                <h4 class="booking-title">Registration</h4>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Your Name *" />
                </div>

                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="number" id="number" placeholder="Phone Number *" />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" placeholder="Type of Vehicle *" />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="seating_capacity" id="seating_capacity" placeholder="Seating Capacity Including Driver *" />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number *" />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="parking_location" id="parking_location" placeholder="Parking Location *" />
                </div>
            </div>
            <div class="form-group">
                <select class="form-control" name="district" id="district">
                    <option value="">-- District *--</option>
                    <option value="Alappuzha">Alappuzha</option>
                    <option value="Ernakulam">Ernakulam</option>
                    <option value="Idukki">Idukki</option>
                    <option value="Kannur">Kannur</option>
                    <option value="Kasaragod">Kasaragod</option>
                    <option value="Kollam">Kollam</option>
                    <option value="Kottayam">Kottayam</option>
                    <option value="Kozhikode">Kozhikode</option>
                    <option value="Malappuram">Malappuram</option>
                    <option value="Palakkad">Palakkad</option>
                    <option value="Pathanamthitta">Pathanamthitta</option>
                    <option value="Thiruvananthapuram">Thiruvananthapuram</option>
                    <option value="Thrissur">Thrissur</option>
                    <option value="Wayanad">Wayanad</option>
                </select>
            </div>
            <br>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label>Vehicle photo from Front side(*)</label>
                    <input type="file" class="form-control" name="vehicle_photo">
                </div>
                <div class="form-group col-sm-6">
                    <label>Driver Image(*)</label>
                    <input type="file" class="form-control" name="driver_image" id="driver_image" >
                </div>
            </div>
            <div class="form-btn">
                <button type="submit" class="th-btn fw-btn" id="bookTaxiBtn">Register Now <i class="fa-regular fa-arrow-right"></i></button>
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
                toastr.success('Registartion completed successfully');
                setTimeout(function() {
                    location.reload();
                }, 5000);
                $('#bookTaxiBtn').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON.message;
                $('.form-messages').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                $('#bookTaxiBtn').prop('disabled', false);
            }
        });
    });

    function validateForm() {
        var valid = true;

        var name = $('[name="name"]').val().trim();
        if (name === "") {
            valid = false;
            $('[name="name"]').addClass('is-invalid');
        } else {
            $('[name="name"]').removeClass('is-invalid');
        }

        var number = $('[name="number"]').val().trim();
        if (number === "") {
            valid = false;
            $('[name="number"]').addClass('is-invalid');
        } else {
            $('[name="number"]').removeClass('is-invalid');
        }

        var vehicle_type = $('[name="vehicle_type"]').val().trim();
        if (vehicle_type === "") {
            valid = false;
            $('[name="vehicle_type"]').addClass('is-invalid');
        } else {
            $('[name="vehicle_type"]').removeClass('is-invalid');
        }

        var seating_capacity = $('[name="seating_capacity"]').val().trim();
        if (seating_capacity === "") {
            valid = false;
            $('[name="seating_capacity"]').addClass('is-invalid');
        } else {
            $('[name="seating_capacity"]').removeClass('is-invalid');
        }

        var vehicle_number = $('[name="vehicle_number"]').val().trim();
        if (vehicle_number === "") {
            valid = false;
            $('[name="vehicle_number"]').addClass('is-invalid');
        } else {
            $('[name="vehicle_number"]').removeClass('is-invalid');
        }

        var parking_location = $('[name="parking_location"]').val().trim();
        if (parking_location === "") {
            valid = false;
            $('[name="parking_location"]').addClass('is-invalid');
        } else {
            $('[name="parking_location"]').removeClass('is-invalid');
        }

        var district = $('[name="district"]').val().trim();
        if (district === "") {
            valid = false;
            $('[name="district"]').addClass('is-invalid');
        } else {
            $('[name="district"]').removeClass('is-invalid');
        }

        var vehicle_photo = $('[name="vehicle_photo"]').val().trim();
        if (vehicle_photo === "") {
            valid = false;
            $('[name="vehicle_photo"]').addClass('is-invalid');
        } else {
            $('[name="vehicle_photo"]').removeClass('is-invalid');
        }

        var driver_image = $('[name="driver_image"]').val().trim();
        if (driver_image === "") {
            valid = false;
            $('[name="driver_image"]').addClass('is-invalid');
        } else {
            $('[name="driver_image"]').removeClass('is-invalid');
        }

        return valid;
    }
});
</script>
