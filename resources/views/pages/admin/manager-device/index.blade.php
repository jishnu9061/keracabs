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
                                            <a class="btn btn-outline-secondary btn-sm edit" title="Route Assign"
                                                data-bs-toggle="modal" data-bs-target="#routeModal">
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
                                            <a class="btn btn-outline-secondary btn-md edit">
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
    <div class="modal fade bs-example-modal-center" id="routeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <form novalidate='novalidate'>
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-3">
                                        <label for="choices-multiple-default"
                                            class="form-label font-size-16 text-muted">Select Route</label>
                                            <select class="form-control" data-trigger="" name="route" id="choices-multiple-default">
                                                @foreach($routes as $route)
                                                    <option value="{{ $route->id }}">{{ $route->route_from }} - {{ $route->route_to }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-center" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addDeviceForm">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var App = {
            initialize: function() {
                // Handle form submission
                $('#addDeviceForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    App.submitForm(formData);
                });

                // Handle delete button click
                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    var row = $(this).closest('tr');
                    var url = $(this).data('href');
                    App.deleteItem(row, url);
                });
            },
            submitForm: function(formData) {
                $.ajax({
                    url: '{{ route('manager-device.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            $('#exampleModal').modal('hide');
                            location.reload();
                        } else {
                            $('.form-error').remove();
                            $.each(data.errors, function(key, value) {
                                $('#' + key).after(
                                    '<div class="text-danger form-error">' +
                                    value + '</div>');
                            });
                        }
                    },
                    error: function(data) {
                        if (data.status === 422) {
                            $('.form-error').remove();
                            var errors = data.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).after(
                                    '<div class="text-danger form-error">' +
                                    value[0] + '</div>');
                            });
                        } else {
                            // Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    }
                });
            },
            deleteItem: function(row, url) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this device!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    confirmButtonColor: '#0fb390'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(data) {
                                if (data.success) {
                                    row.remove();
                                    Swal.fire('Deleted!',
                                        'The manager has been deleted.',
                                        'success');
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            },
                            error: function(data) {
                                console.log(data);
                                Swal.fire('Error!', 'Something went wrong.',
                                    'error');
                            }
                        });
                    }
                });
            }
        };

        App.initialize();
    });
</script>
