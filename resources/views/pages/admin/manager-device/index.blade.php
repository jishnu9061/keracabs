@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Manager name - Devices</h4>
                <div class="page-title-right">
                    <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="bx bx-plus"></i>
                        Add Device</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered   nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>User Name</th>
                                    <th>Password </th>
                                    <th>Logo</th>
                                    <th>Additional</th>
                                    <th>Assign Route</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devices as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td><a href="javascript:;">{{ $user->user_name }}</a></td>
                                        <td>{{ $user->password }}</td>
                                        <td>
                                            <a href="{{ Storage::url('device/' . $user->logo) }}" class="image-popup">
                                                <img src="{{ Storage::url('device/' . $user->logo) }}" class="img-fluid"
                                                    alt="work-thumbnail" width="70">
                                            </a>
                                        </td>
                                        <td>
                                            <h5>{{ $user->header_one }}</h5>
                                        </td>
                                        <td>
                                            <a class="btn btn-outline-secondary btn-sm edit route-assign-btn" title="Route Assign"
                                                data-bs-toggle="modal" data-bs-target="#routeModal"
                                                data-user-id="{{ $user->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('manager-device.edit',$user->id) }}" class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a data-href="{{ route('manager-device.destroy', $user->id) }}" class="btn btn-outline-secondary btn-sm delete-btn" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                            <a href="#" data-href="{{ route('manager-device.reset', $user->id) }}" class="btn btn-outline-secondary btn-md reset-devices">
                                                Reset Devices
                                            </a>
                                        </td> <!-- Action -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="routeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignRouteForm" method="POST" action="{{ route('manager-device.assign') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="route-select" class="form-label font-size-16 text-muted">Select Route</label>
                            <select class="form-control" name="route_id" id="route-select">
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_from }} - {{ $route->route_to }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="entity_id" id="entityId"> <!-- Hidden input to store the user ID -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitRoute">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-center" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addDeviceForm" method="POST" action="{{ route('manager-device.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="manager_id" value="{{ $manager->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Devices</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row align-items-center">
                            <!-- Name Field -->
                            <div class="col-lg-6 col-md-12">
                                <div class="mb-4">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control" type="text" name="name" value="{{ old('name') }}"
                                        id="name">
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="col-lg-6 col-md-12">
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control" type="text" name="password"
                                        value="{{ old('password') }}" id="password">
                                    <div class="invalid-feedback" id="error-password"></div>
                                </div>
                            </div>

                            <!-- Logo Field -->
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input class="form-control" type="file" name="logo"
                                        value="{{ old('logo') }}" id="logo">
                                    <div class="invalid-feedback" id="error-logo"></div>
                                </div>
                            </div>

                            <!-- Optional Fields Section -->
                            <div class="col-lg-12 col-md-12">
                                <h4 class="mb-2">Optional</h4>
                                <hr>
                            </div>

                            <!-- Header1 Field -->
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="header_one" class="form-label">Header1</label>
                                    <input class="form-control" type="text" name="header_one"
                                        value="{{ old('header_one') }}" id="header_one">
                                    <div class="invalid-feedback" id="error-header_one"></div>
                                </div>
                            </div>

                            <!-- Header2 Field -->
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="header_two" class="form-label">Header2</label>
                                    <input class="form-control" type="text" name="header_two"
                                        value="{{ old('header_two') }}" id="header_two">
                                    <div class="invalid-feedback" id="error-header_two"></div>
                                </div>
                            </div>

                            <!-- Footer Field -->
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="footer" class="form-label">Footer</label>
                                    <input class="form-control" type="text" name="footer"
                                        value="{{ old('footer') }}" id="footer">
                                    <div class="invalid-feedback" id="error-footer"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
            </form>
        </div>
    </div>
    </div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRsN/hYZyoD1t3R2TgC8X5dB2T2syuYrO6+rLdU5e" crossorigin="anonymous">
</script>
<script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).on('click', '.route-assign-btn', function() {
        var userId = $(this).data('user-id');
        $('#entityId').val(userId);
    });

    $(document).ready(function() {
        $('#submitRoute').on('click', function() {
            $('#assignRouteForm').submit();
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.reset-devices').on('click', function(e) {
            e.preventDefault();
            var url = $(this).data('href');

            if (confirm('Are you sure you want to reset the devices for this user?')) {
                window.location.href = url;
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
    $('#addDeviceForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('manager-device.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle success response (e.g., close modal, show a success message)
                $('#exampleModal').modal('hide');
                alert("Device added successfully!");
                location.reload(); // Optional: Reload the page to see the updated list
            },
            error: function(xhr) {
                // Handle error response and display validation errors
                var errors = xhr.responseJSON.errors;
                $('#error-name').text(errors.name);
                $('#error-password').text(errors.password);
                $('#error-logo').text(errors.logo);
            }
        });
    });
});

    </script>
