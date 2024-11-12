<?php

namespace App\Http\Controllers\Api;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Http\Constants\AdminConstants;
use Illuminate\Support\Facades\Validator;

class PrintReportController extends ApiBaseController
{
    public function printTripReport(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'device_id' => 'required|string|exists:devices,id',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Invalid data', $validator->errors()->toArray());
    }

    // Fetch trips along with stage data
    $trips = Trip::select(
        'trips.id',
        'trips.trip_name',
        'trips.start_date',
        'trips.start_time',
        'trips.end_date',
        'trips.end_time',
        'trips.full_ticket',
        'trips.half_ticket',
        'trips.student_ticket',
        'trips.language_ticket',
        'trips.physical_ticket',
        'trips.created_at',
        'trips.total_amount',
        'trips.total_expense',
        'trips.net_total',
        'stages.stage_data',
        'trips.stage_id',
        'trips.stop_id',
        'trips.start_id',
        'trips.route_status',
        'start_trips.id as trip_id'
    )
    ->join('start_trips', 'start_trips.id', '=', 'trips.trip_id')
    ->join('stages', 'trips.stage_id', '=', 'stages.id')
    ->where('trips.device_id', $request->device_id)
    ->get();

    // Initialize ticket price totals
    $totalFullTicketPrice = 0;
    $totalHalfTicketPrice = 0;
    $totalStudentTicketPrice = 0;
    $totalLanguageTicketPrice = 0;
    $totalPhysicalTicketPrice = 0;

    $tripsWithFareDetails = [];

    // Process each trip to calculate fare differences and extract stage names
    foreach ($trips as $trip) {
        $stageData = json_decode($trip->stage_data, true);

        // Get the start and end stage names
        $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
        $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

        $startPrice = 0;
        $stopPrice = 0;

        // Get stop price
        if (isset($stageData[$trip->stop_id])) {
            $stage = $stageData[$trip->stop_id];
            $prices = $stage['prices'] ?? [];
            if (!empty($prices)) {
                $stopPrice = end($prices);
            }
        }

        // Get start price
        if (isset($stageData[$trip->start_id])) {
            $stage = $stageData[$trip->start_id];
            $prices = $stage['prices'] ?? [];
            if (!empty($prices)) {
                $startPrice = end($prices);
            }
        }

        // Calculate the price difference based on route status
        $priceDifference = ($trip->route_status == AdminConstants::ROUTE_STATUS_UP)
            ? ($stopPrice - $startPrice)
            : ($startPrice - $stopPrice);

        // Calculate individual ticket prices for the trip
        $tripFullTicketPrice = $trip->full_ticket * $priceDifference;
        $tripHalfTicketPrice = $trip->half_ticket * $priceDifference * 0.5;
        $tripStudentTicketPrice = $trip->student_ticket * $priceDifference * 0.3;
        $tripLanguageTicketPrice = $trip->language_ticket * $priceDifference * 0.3;
        $tripPhysicalTicketPrice = $trip->physical_ticket * $priceDifference * 0.3;

        // Update total prices
        $totalFullTicketPrice += $tripFullTicketPrice;
        $totalHalfTicketPrice += $tripHalfTicketPrice;
        $totalStudentTicketPrice += $tripStudentTicketPrice;
        $totalLanguageTicketPrice += $tripLanguageTicketPrice;
        $totalPhysicalTicketPrice += $tripPhysicalTicketPrice;

        // Add trip fare details including ticket prices
        $tripsWithFareDetails[] = [
            'id' => $trip->trip_id,
            'ticket_id' => $trip->id,
            'trip_name' => $trip->trip_name,
            'start_date' => $trip->start_date,
            'start_time' => $trip->start_time,
            'end_date' => $trip->end_date,
            'end_time' => $trip->end_time,
            'start_stage_name' => $trip->start_stage_name,
            'end_stage_name' => $trip->end_stage_name,
            'full_ticket_price' => $tripFullTicketPrice,
            'half_ticket_price' => $tripHalfTicketPrice,
            'student_ticket_price' => $tripStudentTicketPrice,
            'language_ticket_price' => $tripLanguageTicketPrice,
            'physical_ticket_price' => $tripPhysicalTicketPrice,
            'total_fare' => $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice,
            'total_tickets' => $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket,
            'total_ticket_price' => $trip->total_amount,
            'total_expense' => $trip->total_expense,
            'net_total' => $trip->net_total,
            'formatted_created_at' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
        ];
    }

    // Calculate total fare sum
    $sumOfTotalFare = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLanguageTicketPrice + $totalPhysicalTicketPrice;

    // Prepare response data
    $data = [
        'trips' => $tripsWithFareDetails,
        'sumOfFullTicketPrice' => $totalFullTicketPrice,
        'sumOfHalfTicketPrice' => $totalHalfTicketPrice,
        'sumOfStudentTicketPrice' => $totalStudentTicketPrice,
        'sumOfLanguageTicketPrice' => $totalLanguageTicketPrice,
        'sumOfPhysicalTicketPrice' => $totalPhysicalTicketPrice,
        'sumOfTotalFare' => $sumOfTotalFare,
    ];

    // Generate PDF using the 'trip_report' view
    $pdf = PDF::loadView('reports.trip-report', $data);
    return $pdf->download('trip_report.pdf');
}
}
