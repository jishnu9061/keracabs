@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Inspector Report</h4>



        </div>
    </div>
</div>
<!-- end page title -->


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div>

                    <form action="{{ route('trip.inspector') }}" method="GET" id="inspectorForm">
                        <div>
                            <div class="row align-items-center">
                                <div class="col-lg-3">
                                    <div class="mb-4">
                                        <label class="form-label"> Date</label>
                                        <input type="text" class="form-control" name="name" id="datepicker-basic">
                                    </div>
                                </div>
                                {{-- <div class="col-lg-3 col-md-6">
                                    <div class="mb-4">
                                        <label for="choices-single-default" class="form-label">Device
                                        </label>
                                        <select class="form-control" data-trigger="" name="choices-single-default"
                                            id="choices-single-default">
                                            <option value=""></option>
                                            <option value="Choice 1">Device 1</option>
                                            <option value="Choice 2">Device 2</option>
                                            <option value="Choice 3">Device 3</option>
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="mb-4">
                                        <label for="trip-name" class="form-label">Trip</label>
                                        <input type="text" class="form-control" id="trip-name" name="trip_name"
                                            placeholder="Search Trip">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">


                                    <div class="text-right mt-1 d-flex">
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light w-100 me-2">Submit
                                        </button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light w-100 me-2"
                                            id="inspectorFormReset">Reset
                                        </button>
                                        <a href="{{ route('trip.inspector-print') }}" class="btn btn-primary waves-effect waves-light w-100">Print</a>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </form>
                    <!-- end row -->
                </div>
                <!-- Single select input Example -->





            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body">
                <div class="table-responsive mb-0" data-pattern="priority-columns">
                    <table id="datatable-buttons" class="table table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>Sl.No</th>
                                <th>Date</th>
                                <th>Trip Name</th>
                                <th>Stage Name</th>
                                <th>Total passengers</th>
                                <th>Droped Passenger</th>
                            </tr>
                        </thead>


                        <tbody>
                            @foreach ($trips as $trip)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trip->date }}</td>
                                <td>{{ $trip->trip_name }}</td>
                                <td>{{ $trip->route_from }}-{{ $trip->route_to }}</td>
                                <td>{{ $trip->total_ticket }}</td>
                                <td>{{ $trip->total_ticket }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end cardaa -->
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#inspectorFormReset').on('click', function(e) {
            e.preventDefault();
            var form = $('#inspectorForm');
            form.find('input[type=text]').val('');
            form.find('input[type=date]').val('');
            form.find('select').val([]).trigger('change');
            Swal.fire('Reset!', 'The form has been reset.', 'success');
        });
    });
</script>

