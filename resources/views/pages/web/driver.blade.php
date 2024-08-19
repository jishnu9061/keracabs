{{-- @include('pages.web.includes.header')

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
        <form action="{{ route('send-registration') }}" method="POST" class="booking-form-area" id="bookingForm" enctype="multipart/form-data">
            @csrf
            <div class="booking-title-area">
                <h4 class="booking-title">Registration</h4>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Your Name *" required />
                </div>

                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="number" id="number" placeholder="Phone Number *" required />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="whatsapp_number" id="whatsapp" placeholder="WhatsApp Number *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" placeholder="Type of Vehicle *" required />
                </div>
                @error('vehicle_type')
                <span class="text-danger">{{ $message }}</span>
            @enderror
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="seating_capacity" id="seating_capacity" placeholder="Seating Capacity Including Driver *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="parking_location" id="parking_location" placeholder="Parking Location *" required />
                </div>
                <div class="form-group col-sm-6">
                    <select class="form-control" name="district" id="district" required>
                        <option value="">-- District * --</option>
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
            </div>
            <br>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label>Vehicle photo from Front side (*)</label>
                    <input type="file" class="form-control" name="vehicle_photo" accept="image/*" required>
                </div>
                <div class="form-group col-sm-6">
                    <label>Driver Image (*)</label>
                    <input type="file" class="form-control" name="driver_image" id="driver_image" accept="image/*" required>
                </div>
            </div>
            <div class="form-group">
                <input type="checkbox" id="same_as_phone" />
                <label for="same_as_phone">WhatsApp Number is the same as Phone Number</label>
            </div>
            <div class="form-btn">
                <button type="submit" class="th-btn fw-btn" id="bookTaxiBtn">Submit Now <i class="fa-regular fa-arrow-right"></i></button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bookingForm').submit(function(event) {
            event.preventDefault();
            $('#bookTaxiBtn').prop('disabled', true);

            if (!validateForm()) {
                $('#bookTaxiBtn').prop('disabled', false);
                return false;
            }

            $.blockUI({ message: '<h4>Processing...</h4>' });

            var formData = new FormData(this);
            if ($('#same_as_phone').is(':checked')) {
                var phoneNumber = $('#number').val().trim();
                formData.append('whatsapp_number', phoneNumber);
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $.unblockUI();
                    $('#bookingForm')[0].reset();
                    Swal.fire({
                        title: 'Submitted!',
                        text: 'Your content was submitted successfully. Save this number 9446045678 for updates.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0fb390'
                    }).then(() => {
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    });
                    $('#bookTaxiBtn').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    $.unblockUI();
                    var errorMessages = xhr.responseJSON ? xhr.responseJSON.errors : {};

                    // Display error messages for each field
                    Object.keys(errorMessages).forEach(function(key) {
                        var field = $('[name="' + key + '"]');
                        field.addClass('is-invalid');
                        field.next('.invalid-feedback').remove(); // Remove existing error messages
                        field.after('<div class="invalid-feedback">' + errorMessages[key].join('<br>') + '</div>');
                    });

                    // Display a general error message if available
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        $('.form-messages').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    } else {
                        $('.form-messages').html('<div class="alert alert-danger">An error occurred</div>');
                    }

                    $('#bookTaxiBtn').prop('disabled', false);
                }
            });
        });

        function validateForm() {
            var valid = true;

            var fields = ['name', 'number', 'whatsapp_number', 'vehicle_type', 'seating_capacity', 'vehicle_number', 'parking_location', 'district'];

            fields.forEach(function(field) {
                var value = $('[name="' + field + '"]').val().trim();
                if (value === "") {
                    valid = false;
                    var fieldElement = $('[name="' + field + '"]');
                    fieldElement.addClass('is-invalid');
                    fieldElement.next('.invalid-feedback').remove(); // Remove existing error messages
                    fieldElement.after('<div class="invalid-feedback">This field is required</div>');
                } else {
                    $('[name="' + field + '"]').removeClass('is-invalid');
                    $('[name="' + field + '"]').next('.invalid-feedback').remove(); // Remove existing error messages
                }
            });

            if (!$('#same_as_phone').is(':checked')) {
                var whatsappNumber = $('[name="whatsapp_number"]').val().trim();
                if (whatsappNumber === "") {
                    valid = false;
                    var whatsappField = $('[name="whatsapp_number"]');
                    whatsappField.addClass('is-invalid');
                    whatsappField.next('.invalid-feedback').remove();
                    whatsappField.after('<div class="invalid-feedback">WhatsApp Number is required</div>');
                } else {
                    $('[name="whatsapp_number"]').removeClass('is-invalid');
                    $('[name="whatsapp_number"]').next('.invalid-feedback').remove();
                }
            }

            var vehiclePhoto = $('[name="vehicle_photo"]').prop('files').length;
            if (vehiclePhoto === 0) {
                valid = false;
                var vehiclePhotoField = $('[name="vehicle_photo"]');
                vehiclePhotoField.addClass('is-invalid');
                vehiclePhotoField.next('.invalid-feedback').remove(); // Remove existing error messages
                vehiclePhotoField.after('<div class="invalid-feedback">Vehicle photo is required</div>');
            } else {
                $('[name="vehicle_photo"]').removeClass('is-invalid');
                $('[name="vehicle_photo"]').next('.invalid-feedback').remove(); // Remove existing error messages
            }

            var driverImage = $('[name="driver_image"]').prop('files').length;
            if (driverImage === 0) {
                valid = false;
                var driverImageField = $('[name="driver_image"]');
                driverImageField.addClass('is-invalid');
                driverImageField.next('.invalid-feedback').remove(); // Remove existing error messages
                driverImageField.after('<div class="invalid-feedback">Driver image is required</div>');
            } else {
                $('[name="driver_image"]').removeClass('is-invalid');
                $('[name="driver_image"]').next('.invalid-feedback').remove();
            }

            return valid;
        }

        document.getElementById('same_as_phone').addEventListener('change', function() {
            var phoneNumber = document.getElementById('number').value;
            var whatsappNumber = document.getElementById('whatsapp');
            if (this.checked) {
                $("#whatsapp").val(phoneNumber);
                whatsappNumber.disabled = true;
            } else {
                whatsappNumber.disabled = false;
            }
        });
    });
</script>
 --}}

 @include('pages.web.includes.header')
<style>
    input[type="checkbox"] ~ label:before{
        border: 1px solid #673AB7;
    }
    input[type="checkbox"] ~ label {
        font-size: 11px;
    }
</style>
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
        <form action="{{ route('send-registration') }}" method="POST" class="booking-form-area" id="bookingForm" enctype="multipart/form-data">
            @csrf
            <div class="booking-title-area">
                <h4 class="booking-title">Registration</h4>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Your Name *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="number" id="number" placeholder="Phone Number *" required />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <input type="number" class="form-control" name="whatsapp_number" id="whatsapp" placeholder="WhatsApp Number *" required />
                    <div class="form-group mt-4 mb-0">
                        <input type="checkbox" id="same_as_phone" />
                        <label for="same_as_phone">WhatsApp Number is the same as Phone Number</label>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" placeholder="Type of Vehicle *" required />
                    @error('vehicle_type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="seating_capacity" id="seating_capacity" placeholder="Passenger Capacity *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="Vehicle Number *" required />
                </div>
                <div class="form-group col-sm-6">
                    <input type="text" class="form-control" name="parking_location" id="parking_location" placeholder="Parking Location *" required />
                </div>
                <div class="form-group col-sm-6">
                    <select class="form-control" name="district" id="district" required>
                        <option value="">-- District * --</option>
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
            </div>
            <br>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label>Vehicle photo from Front side (*)</label>
                    <input type="file" class="form-control" name="vehicle_photo" accept="image/*" required>
                </div>
                <div class="form-group col-sm-6">
                    <label>Driver Image</label>
                    <input type="file" class="form-control" name="driver_image" id="driver_image" accept="image/*">
                </div>
            </div>
            <div class="form-btn">
                <button type="submit" class="th-btn fw-btn" id="bookTaxiBtn">Submit Now <i class="fa-regular fa-arrow-right"></i></button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRsN/hYZyoD1t3R2TgC8X5dB2T2syuYrO6+rLdU5e" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bookingForm').submit(function(event) {
            event.preventDefault();
            $('#bookTaxiBtn').prop('disabled', true);

            if (!validateForm()) {
                $('#bookTaxiBtn').prop('disabled', false);
                return false;
            }

            $.blockUI({ message: '<h4>Processing...</h4>' });

            var formData = new FormData(this);
            if ($('#same_as_phone').is(':checked')) {
                var phoneNumber = $('#number').val().trim();
                formData.append('whatsapp_number', phoneNumber);
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $.unblockUI();
                    $('#bookingForm')[0].reset();
                    Swal.fire({
                        title: 'Submitted!',
                        text: 'Your content was submitted successfully. Save this number 9446045678 for updates.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0fb390'
                    }).then(() => {
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    });
                    $('#bookTaxiBtn').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    $.unblockUI();
                    var errorMessages = xhr.responseJSON ? xhr.responseJSON.errors : {};

                    // Display error messages for each field
                    Object.keys(errorMessages).forEach(function(key) {
                        var field = $('[name="' + key + '"]');
                        field.addClass('is-invalid');
                        field.next('.invalid-feedback').remove(); // Remove existing error messages
                        field.after('<div class="invalid-feedback">' + errorMessages[key].join('<br>') + '</div>');
                    });

                    // Display a general error message if available
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        $('.form-messages').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    } else {
                        $('.form-messages').html('<div class="alert alert-danger">An error occurred</div>');
                    }

                    $('#bookTaxiBtn').prop('disabled', false);
                }
            });
        });

        function validateForm() {
            var valid = true;

            var fields = ['name', 'number', 'whatsapp_number', 'vehicle_type', 'seating_capacity', 'vehicle_number', 'parking_location', 'district'];

            fields.forEach(function(field) {
                var value = $('[name="' + field + '"]').val().trim();
                if (value === "") {
                    valid = false;
                    var fieldElement = $('[name="' + field + '"]');
                    fieldElement.addClass('is-invalid');
                    fieldElement.next('.invalid-feedback').remove(); // Remove existing error messages
                    fieldElement.after('<div class="invalid-feedback">This field is required</div>');
                } else {
                    $('[name="' + field + '"]').removeClass('is-invalid');
                    $('[name="' + field + '"]').next('.invalid-feedback').remove(); // Remove existing error messages
                }
            });

            if (!$('#same_as_phone').is(':checked')) {
                var whatsappNumber = $('[name="whatsapp_number"]').val().trim();
                if (whatsappNumber === "") {
                    valid = false;
                    var whatsappField = $('[name="whatsapp_number"]');
                    whatsappField.addClass('is-invalid');
                    whatsappField.next('.invalid-feedback').remove();
                    whatsappField.after('<div class="invalid-feedback">WhatsApp Number is required</div>');
                } else {
                    $('[name="whatsapp_number"]').removeClass('is-invalid');
                    $('[name="whatsapp_number"]').next('.invalid-feedback').remove();
                }
            }

            var vehiclePhoto = $('[name="vehicle_photo"]').prop('files').length;
            if (vehiclePhoto === 0) {
                valid = false;
                var vehiclePhotoField = $('[name="vehicle_photo"]');
                vehiclePhotoField.addClass('is-invalid');
                vehiclePhotoField.next('.invalid-feedback').remove(); // Remove existing error messages
                vehiclePhotoField.after('<div class="invalid-feedback">Vehicle photo is required</div>');
            } else {
                $('[name="vehicle_photo"]').removeClass('is-invalid');
                $('[name="vehicle_photo"]').next('.invalid-feedback').remove(); // Remove existing error messages
            }

            return valid;
        }

        $('#same_as_phone').change(function() {
            var phoneNumber = $('#number').val();
            var whatsappNumber = $('#whatsapp');
            if (this.checked) {
                whatsappNumber.val(phoneNumber);
                whatsappNumber.prop('disabled', true);
            } else {
                whatsappNumber.prop('disabled', false);
            }
        });
    });
</script>

