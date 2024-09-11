@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Trip Report</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('trip.index') }}" method="GET" id="tripForm">
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
                                            id="tripFormReset">Reset
                                        </button>
                                        {{-- <a href="{{ route('trip.print') }}" class="btn btn-primary waves-effect waves-light w-100">Print</a> --}}
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </form>
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
                                    <th>Start Date</th>
                                    <th>Start time</th>
                                    <th>End Date</th>
                                    <th>End Time</th>
                                    <th>Trip name</th>
                                    <th>Full Ticket</th>
                                    <th>half Ticket</th>
                                    <th>Student Ticket</th>
                                    <th>Lagguage Ticket</th>
                                    <th>Physical Ticket</th>
                                    <th>Total Ticket</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($trips as $index => $trip)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td>{{ \Carbon\Carbon::parse($trip->start_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trip->start_time)->format('h:i a') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trip->end_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trip->end_time)->format('h:i a') }}</td>
                                        <td>{{ $trip->trip_name }}</td>
                                        <td>{{ $trip->full_ticket }}</td>
                                        <td>{{ $trip->half_ticket }}</td>
                                        <td>{{ $trip->student_ticket }}</td>
                                        <td>{{ $trip->language_ticket }}</td>
                                        <td>{{ $trip->physical_ticket }}</td>
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
        $('#tripFormReset').on('click', function(e) {
            e.preventDefault();
            var form = $('#tripForm');
            form.find('input[type=text]').val('');
            form.find('input[type=date]').val('');
            form.find('select').val([]).trigger('change');
            Swal.fire('Reset!', 'The form has been reset.', 'success');
        });
    });
</script>
