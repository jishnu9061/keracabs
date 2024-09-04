@extends('layouts.app')
@section('title', 'Dashboard | Greenveel')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Manager List</h4>
                <div class="page-title-right">
                    <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="bx bx-plus"></i>
                        Add</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Contact</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($managers as $manager)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="{{ route('manager-device.index',$manager->id) }}">{{ $manager->name }}</a></td>
                                        <td>{{ $manager->user_name }}</td>
                                        <td>{{ $manager->password }}</td>
                                        <td>{{ $manager->contact }}</td>
                                        <td>
                                            <a href="{{ route('manager.edit', $manager->id) }}"
                                                class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a class="btn btn-outline-secondary btn-sm delete-btn"
                                                data-href="{{ route('manager.destroy', $manager->id) }}" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-center" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <form id="manager-form">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="name" name="name" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-4">
                                        <label for="username" class="form-label">Username</label>
                                        <input class="form-control" type="text" id="username" name="username" placeholder="Enter username">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input class="form-control" type="password" id="password" name="password" placeholder="Enter password">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="mb-4">
                                        <label for="number" class="form-label">Number</label>
                                        <input class="form-control" type="text" id="number" name="number" placeholder="Enter number">
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
                        $('#manager-form').on('submit', function(e) {
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
                            url: '{{ route('manager.store') }}',
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
                                    Swal.fire('Error!', 'Something went wrong.', 'error');
                                }
                            }
                        });
                    },
                    deleteItem: function(row, url) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You want to delete this manager!",
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
                                                'The manager has been deleted.', 'success');
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
