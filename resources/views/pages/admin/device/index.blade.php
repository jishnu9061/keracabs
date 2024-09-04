@extends('layouts.app')
@section('title', 'Dashboard | Greenveel')
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
                            @foreach($devices as $device)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td><a href="javascript:;">{{ $device->name }}</a></td>
                                    <td><a href="javascript:;">{{ $device->user_name }}</a></td>
                                    <td>{{ $device->password }}</td>
                                    <td>
                                        @if($device->logo)
                                            <a href="{{ asset('storage/device/'.$device->logo) }}" class="image-popup">
                                                <img src="{{ asset('storage/device/'.$device->logo) }}" class="img-fluid" alt="work-thumbnail" width="70">
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <h5>{{ $device->header_one }}</h5>
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-secondary btn-sm edit" title="Route Assign" data-bs-toggle="modal" data-bs-target="#routeModal{{ $device->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-secondary btn-sm edit delete-btn" title="Delete" data-href="{{ route('device.destroy', $device->id) }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <a class="btn btn-outline-secondary btn-md edit">
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

<div class="mt-4">
    <h5 class="font-size-14 mb-3">Multiple select input</h5>

    <div class="row">



    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

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
                                    <select class="form-control" data-trigger="" name="choices-multiple-default"
                                        id="choices-multiple-default" placeholder="This is a placeholder"
                                        multiple="">
                                        <option value="Choice 1">Choice 1</option>
                                        <option value="Choice 2">Choice 2</option>
                                        <option value="Choice 3">Choice 3</option>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
