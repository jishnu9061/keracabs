@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <!-- Start Page Title -->
    <div class="row no-print">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Collection Report</h4>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Date</th>
                                    <th>Trip Name</th>
                                    <th>Full Ticket</th>
                                    <th>Half Ticket</th>
                                    <th>Student Ticket</th>
                                    <th>Luggage Ticket</th>
                                    <th>Physical Ticket</th>
                                    <th>Total Ticket</th>
                                    <th>Total Amount</th>
                                    <th>Total Expense</th>
                                    <th>Net Amount</th>
                                </tr>
                            </thead>
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
                                        <td>{{ 	$trip->grand_total_ticket_price }}</td>
                                        <td>{{ $totalExpenseForCurrentTrip }}</td>
                                        <td>{{ 	$netTotalPerTrip[$trip->trip_id] }}</td>
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
                                    <td>{{ $totalExpense[$trip->trip_id] }}</td>
                                    <td>{{ $netTotalPerTrip[$trip->trip_id] }}</td>
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>
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
        // Delay print to ensure content is fully rendered
        setTimeout(function() {
            window.print();
        }, 500); // Adjust delay as needed
    });
</script>

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
            overflow-x: auto; /* Handle wide tables */
        }

        /* Ensure table headers repeat on each printed page */
        thead {
            display: table-header-group;
        }

        /* Adjust font size and padding to fit content */
        th, td {
            border: 1px solid black;
            padding: 3px; /* Reduce padding */
            font-size: 9px; /* Reduce font size */
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Style table header for differentiation */
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Reduce overall font size for body content */
        body {
            font-size: 10px;
            line-height: 1.2;
        }

        /* Remove margin for printed pages to use space efficiently */
        @page {
            margin: 5mm;
        }

        /* Adjust width for better content fit */
        body {
            zoom: 80%; /* Scale down the content */
        }

        /* Ensure uniform spacing for titles */
        h4 {
            margin: 0;
            padding: 10px 0;
        }

        /* Scale down large tables to fit the page */
        .table-responsive {
            max-width: 92%;
        }
    }
</style>
