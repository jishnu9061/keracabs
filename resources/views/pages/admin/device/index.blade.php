@extends('layouts.app')
@section('title', 'Dashboard | Greenveel')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">View Devices</h4>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered   nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Manager</th>
                                    <th>User Name</th>
                                    <th>Password </th>
                                    <th>Logo</th>
                                    <th>Additional</th>
                                    <th>Assign Route</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devices as $device)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td><a href="{{ route('manager-device.index',$device->manager_id) }}">{{ $device->name }}</a></td>
                                        <td><a href="{{ route('manager-device.list',$device->id) }}">{{ $device->user_name }}</a></td>
                                        <td>{{ $device->password }}</td>
                                        <td>
                                            @if ($device->logo)
                                                <a href="{{ asset('storage/device/' . $device->logo) }}" class="image-popup">
                                                    <img src="{{ asset('storage/device/' . $device->logo) }}"
                                                        class="img-fluid" alt="work-thumbnail" width="70">
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <h5>{{ $device->header_one }}</h5>
                                        </td>
                                        <td>
                                            <a class="btn btn-outline-secondary btn-sm edit route-assign-btn" title="Route Assign"
                                                data-bs-toggle="modal" data-bs-target="#routeModal"
                                                data-user-id="{{ $device->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-outline-secondary btn-sm edit delete-btn" title="Delete"
                                                data-href="{{ route('device.destroy', $device->id) }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                            <a href="#" data-href="{{ route('manager-device.reset', $device->id) }}" class="btn btn-outline-secondary btn-md reset-devices">
                                                Reset Devices
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

    <!-- End Page-content -->

    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

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
                            <select class="form-control" data-trigger="" name="route_id[]" id="choices-multiple-default" placeholder="This is a placeholder" multiple="">
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_from }} - {{ $route->route_to }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="entity_id" id="entityId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitRoute">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $.noConflict();
        $(document).on('click', '.route-assign-btn', function() {
        var userId = $(this).data('user-id');
        $('#entityId').val(userId); // Set the hidden input value
        });

        $('#submitRoute').on('click', function() {
            $('#assignRouteForm').submit();
        });
    });
</script>
<script>
    $(document).ready(function() {
        var App = {
            initialize: function() {
                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    var row = $(this).closest('tr');
                    var url = $(this).data('href');
                    App.deleteItem(row, url);
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#route-select').select2({
        placeholder: "Select a route",
        allowClear: true,
        width: '100%'
    });
});
</script>
