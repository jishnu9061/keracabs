<!DOCTYPE html>
<html>
<head>
    <title>Trip Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Trip Report</h2>

    <table>
        <thead>
            <tr>
                <th>Trip Name</th>
                <th>Start Stage</th>
                <th>End Stage</th>
                <th>Total Tickets</th>
                <th>Total Ticket Price</th>
                <th>Total Expense</th>
                <th>Net Total</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trips as $trip)
            <tr>
                <td>{{ $trip['trip_name'] }}</td>
                <td>{{ $trip['start_stage_name'] }}</td>
                <td>{{ $trip['end_stage_name'] }}</td>
                <td>{{ $trip['total_tickets'] }}</td>
                <td>{{ number_format($trip['total_ticket_price'], 2) }}</td>
                <td>{{ number_format($trip['total_expense'], 2) }}</td>
                <td>{{ number_format($trip['net_total'], 2) }}</td>
                <td>{{ $trip['formatted_created_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Fares Summary:</h3>
    <ul>
        <li>Full Ticket Price: {{ number_format($sumOfFullTicketPrice, 2) }}</li>
        <li>Half Ticket Price: {{ number_format($sumOfHalfTicketPrice, 2) }}</li>
        <li>Student Ticket Price: {{ number_format($sumOfStudentTicketPrice, 2) }}</li>
        <li>Language Ticket Price: {{ number_format($sumOfLanguageTicketPrice, 2) }}</li>
        <li>Physical Ticket Price: {{ number_format($sumOfPhysicalTicketPrice, 2) }}</li>
        <li>Total Fare: {{ number_format($sumOfTotalFare, 2) }}</li>
    </ul>
</body>
</html>
