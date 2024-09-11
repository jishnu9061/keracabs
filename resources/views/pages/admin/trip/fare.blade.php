@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Fare Wise Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <form action="{{ route('trip.fare') }}" method="GET" id="fareForm">
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
                                                id="fareFormReset">Reset
                                            </button>
                                            {{-- <a href="{{ route('trip.print') }}" class="btn btn-primary waves-effect waves-light w-100">Print</a> --}}
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable-buttons" class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl. No</th>
                                    <th>Date</th>
                                    <th>Trip</th>
                                    <th>Full Ticket</th>
                                    <th>Half Ticket</th>
                                    <th>Student Ticket </th>
                                    <th>Lagguage Ticket</th>
                                    <th>Physical Ticket</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalFullTicketPrice = 0;
                                    $totalHalfTicketPrice = 0;
                                    $totalStudentTicketPrice = 0;
                                    $totalLanguageTicketPrice = 0;
                                    $totalPhysicalTicketPrice = 0;
                                    $netTotalAmount = 0;
                                @endphp

                                @foreach ($trips as $trip)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $trip->created_at }}</td>
                                    <td>{{ $trip->trip_name }}</td>
                                    <td>{{ $trip->full_ticket }}</td>
                                    <td>{{ $trip->half_ticket }}</td>
                                    <td>{{ $trip->student_ticket }}</td>
                                    <td>{{ $trip->language_ticket }}</td>
                                    <td>{{ $trip->physical_ticket }}</td>
                                </tr>
                                @endforeach

                                <tr>
                                    <td colspan="3"><h4>Total Amount</h4></td>
                                    <td>₹{{ number_format($sumOfFullTicketPrice, 2) }}</td>
                                    <td>₹{{ number_format($sumOfHalfTicketPrice, 2) }}</td>
                                    <td>₹{{ number_format($sumOfStudentTicketPrice, 2) }}</td>
                                    <td>₹{{ number_format($sumOfLanguageTicketPrice, 2) }}</td>
                                    <td>₹{{ number_format($sumOfPhysicalTicketPrice, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><h5>Net Amount</h5></td>
                                    <td colspan="5">₹{{ number_format($sumOfTotalFare, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
<script>
    $(document).ready(function() {
        $('#fareFormReset').on('click', function(e) {
            e.preventDefault();
            var form = $('#fareForm');
            form.find('input[type=text]').val('');
            form.find('input[type=date]').val('');
            form.find('select').val([]).trigger('change');
            Swal.fire('Reset!', 'The form has been reset.', 'success');
        });
    });
</script>
