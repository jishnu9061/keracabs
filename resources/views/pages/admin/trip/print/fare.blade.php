@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Fare Wise Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl. No</th>
                                    <th>Date</th>
                                    <th>Trip</th>
                                    <th>Full Ticket</th>
                                    <th>Half Ticket</th>
                                    <th>Student Ticket</th>
                                    <th>Language Ticket</th>
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
                                    <td>{{ $trip->date }}</td>
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
            <!-- End Card -->
        </div>
        <!-- End Col -->
    </div>
    <!-- End Row -->
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        setTimeout(function() {
            window.print();
        }, 1000);
    });
</script>

@section('styles')
<style>
    /* Hide elements not needed for printing */
    @media print {
        .no-print {
            display: none;
        }

        /* Ensure the table takes full width and handles overflow */
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        /* Ensure headers remain visible on new pages when printed */
        thead {
            display: table-header-group;
        }

        /* Handle page breaks inside the table */
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Border and padding adjustments for cleaner print layout */
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
            word-wrap: break-word; /* Handle long words */
            overflow-wrap: break-word; /* Handle long words */
        }

        /* Style table header for clear differentiation */
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Increase font size and spacing for print readability */
        body {
            font-size: 12px;
            line-height: 1.2;
        }

        /* Ensure uniform spacing across the page */
        h4, h5 {
            margin: 0;
            padding: 10px 0;
        }
    }
</style>
@endsection
