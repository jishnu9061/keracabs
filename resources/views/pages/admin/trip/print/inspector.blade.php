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
                                    <th>Stage Name</th>
                                    <th>Total Passengers</th>
                                    <th>Dropped Passengers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trips as $trip)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trip->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $trip->trip_name }}</td>
                                        <td>{{ $trip->route_from }} - {{ $trip->route_to }}</td>
                                        <td>{{ $trip->total_ticket }}</td>
                                        <td>{{ $trip->total_ticket ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Automatically trigger print dialog after a short delay
        setTimeout(function() {
            window.print();
        }, 2000); // Increase delay to ensure content is fully rendered

        // Optional: Reset form functionality (if applicable)
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


@section('styles')
<style>
    @media print {
        .no-print {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Ensure headers remain visible on new pages */
        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        /* Handle page breaks inside the table */
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Increase font size and spacing for print readability */
        body {
            font-size: 12px;
            line-height: 1.2;
        }

        /* Ensure uniform spacing across the page */
        h4 {
            margin: 0;
            padding: 10px 0;
        }
    }
</style>
@endsection
