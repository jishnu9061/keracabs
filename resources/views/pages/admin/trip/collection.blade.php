@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Collection Report</h4>



            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>

                        <form action="{{ route('trip.collection') }}" method="GET" id="collectionForm">
                            <div>
                                <div class="row align-items-center">
                                    <div class="col-lg-3">
                                        <div class="mb-4">
                                            <label class="form-label"> Date</label>
                                            <input type="text" class="form-control" name="date" id="datepicker-basic">
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
                                                id="collectionFormReset">Reset
                                            </button>
                                            <a href="{{ route('trip.collection-print') }}" class="btn btn-primary waves-effect waves-light w-100">Print</a>
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
                                    <th>Full Ticket</th>
                                    <th>half Ticket</th>
                                    <th>Student Ticket</th>
                                    <th>Lagguage Ticket</th>
                                    <th>Physical Ticket</th>
                                    <th>Toal Ticket</th>
                                    <th>Toal Amount</th>
                                    <th>Total Expense</th>
                                    <th>Net Amount</th>
                                </tr>
                            </thead>
                            @if(count($trips) > 0)
                                <tbody>
                                    @foreach ($trips as $trip)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $trip->date }}</td>
                                        <td>{{ $trip->trip_name }}</td>
                                        <td>{{ $trip->full_ticket }}</td>
                                        <td>{{ $trip->half_ticket }}</td>
                                        <td>{{ $trip->student_ticket }}</td>
                                        <td>{{ $trip->language_ticket }}</td>
                                        <td>{{ $trip->physical_ticket }}</td>
                                        <td>{{ $trip->total_ticket }}</td>
                                        <td>{{ $trip->grand_total_ticket_price }}</td>
                                        <td>{{ $totalExpense[$trip->trip_id] ?? 0 }}</td>
                                        <td>{{ $netTotalPerTrip[$trip->trip_id] ?? 0 }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">
                                            <h5>Total Amount</h5>
                                        </td>
                                        <td>{{ $sumOfFullTicket }}</td>
                                        <td>{{ $sumOfHalfTicket }}</td>
                                        <td>{{ $sumOfStudentTicket }}</td>
                                        <td>{{ $sumOfLanguageTicket }}</td>
                                        <td>{{ $sumOfPhysicalTicket }}</td>
                                        <td>{{ $sumOfTotalTicket }}</td>
                                        <td>{{ $totalAmount }}</td>
                                        <td>{{ array_sum($totalExpense) }}</td>
                                        <td>{{ $totalAmount }}</td>
                                    </tr>
                                </tbody>
                            @else
                            No Details
                            @endif
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
        $('#collectionFormReset').on('click', function(e) {
            e.preventDefault();
            var form = $('#collectionForm');
            form.find('input[type=text]').val('');
            form.find('input[type=date]').val('');
            form.find('select').val([]).trigger('change');
            Swal.fire('Reset!', 'The form has been reset.', 'success');
        });
    });
</script>
