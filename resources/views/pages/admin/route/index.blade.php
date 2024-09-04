@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Route List - Device Name</h4>
                <div class="page-title-right">
                    <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoute"><i class="bx bx-plus"></i> Add
                        Route</a>
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
                                    <th>Route</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($routes as $route)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="stages.html">{{ $route->route_from }} - {{ $route->route_to }}</a></td>
                                        <td> <a href="{{ route('route.edit',$route->id) }}" class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a class="btn btn-outline-secondary btn-sm delete-route"
                                                data-id="{{ $route->id }}" title="Delete">
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
    <div class="modal fade bs-example-modal-center" id="addRoute" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="route-form">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="route_from" class="form-label">From</label>
                                    <input class="form-control" type="text" name="route_from" id="route_from"
                                        placeholder="Enter starting route">
                                    <div id="error-route_from" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="route_to" class="form-label">To</label>
                                    <input class="form-control" type="text" name="route_to" id="route_to"
                                        placeholder="Enter destination route">
                                    <div id="error-route_to" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="mb-4">
                                    <label for="min_charge" class="form-label">Min. Charge</label>
                                    <input class="form-control" type="text" name="min_charge" id="min_charge"
                                        placeholder="Enter minimum charge">
                                    <div id="error-min_charge" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-check mb-4">
                                    <input type="checkbox" class="form-check-input" name="charge_type" id="fair_charge"
                                        value="{{ AdminConstants::ROUTE_TYPE_FAIR }}">
                                    <label class="form-check-label" for="fair_charge">Fair Charge</label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-check mb-4">
                                    <input type="checkbox" class="form-check-input" name="charge_type" id="student_charge"
                                        value="{{ AdminConstants::ROUTE_TYPE_STUDENT }}">
                                    <label class="form-check-label" for="student_charge">Student Charge</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit-route-form">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('input[type="checkbox"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[type="checkbox"]').not(this).prop('checked', false);
            }
        });

        $('#submit-route-form').click(function() {
            $('.text-danger').text('');
            var formData = $('#route-form').serialize();
            $.ajax({
                url: '{{ route('route.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    location.reload();
                    $('#addRoute').modal('hide');
                    Swal.fire('Added!','The route added successfully.', 'success');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                $('#error-' + field).text(errors[field][0]);
                            }
                        }
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        $('.delete-route').click(function() {
            var routeId = $(this).data('id');
            if (confirm('Are you sure you want to delete this route?')) {
                $.ajax({
                    url: '{{ route('route.destroy', ':id') }}'.replace(':id', routeId),
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr.success('Route deleted successfully!');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(
                            'An error occurred while deleting the route. Please try again.');
                    }
                });
            }
        });
    });
</script>
