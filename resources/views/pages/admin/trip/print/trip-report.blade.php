@extends('layouts.app')
@section('title', 'Dashboard | KL Mart')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between no-print">
                <h4 class="mb-sm-0 font-size-18">Trip Report</h4>
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
                                    <th>Sl.No</th>
                                    <th>Start Date</th>
                                    <th>Start time</th>
                                    <th>End Date</th>
                                    <th>End Time</th>
                                    <th>Trip name</th>
                                    <th>Full Ticket</th>
                                    <th>Half Ticket</th>
                                    <th>Student Ticket</th>
                                    <th>Luggage Ticket</th>
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
        </div>
    </div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Automatically trigger print on page load
        window.print();

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

<style>
    /* Hides elements when printing */
    @media print {
        .no-print {
            display: none;
        }

        /* Adjust table styling for print */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }
    }
</style>
