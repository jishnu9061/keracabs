<?php

namespace App\Http\Controllers\Admin;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Stage;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Constants\AdminConstants;

class AdminTripController extends Controller
{
    public function index(Request $request)
    {
        $path = $this->getView('admin.trip.index');
        $trip = Trip::select(
            'id',
            'trip_name',
            'start_date',
            'start_time',
            'end_date',
            'end_time',
            'full_ticket',
            'half_ticket',
            'student_ticket',
            'language_ticket',
            'physical_ticket',
            DB::raw('(full_ticket + half_ticket + student_ticket + language_ticket + physical_ticket) as total_ticket')
        );
        if ($request->filled('trip_name')) {
            $trip->where('trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $trip->whereDate('start_date', '=', $request->start_date);
        }

        if ($request->filled('start_time')) {
            $trip->whereTime('start_time', '=', $request->start_time);
        }
        $trips = $trip->get();
        $para = ['trips' => $trips];
        $title = 'Route Stops';
        return $this->renderView($path, $para, $title);
    }

    public function printScreen()
    {
        $path = $this->getView('admin.trip.print');
        $trips = Trip::select(
            'id',
            'trip_name',
            'start_date',
            'start_time',
            'end_date',
            'end_time',
            'full_ticket',
            'half_ticket',
            'student_ticket',
            'language_ticket',
            'physical_ticket',
            DB::raw('(full_ticket + half_ticket + student_ticket + language_ticket + physical_ticket) as total_ticket')
        )->get();
        $para = ['trips' => $trips];
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }

    public function collectionReport(DashboardService $dashboardService, Request $request)
    {
        $path = $this->getView('admin.trip.collection');
        $tripsQuery = Trip::select(
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
            'trips.total_expense',
            'trips.net_total',
            DB::raw('(full_ticket + half_ticket + student_ticket + language_ticket + physical_ticket) as total_ticket'),
            'stages.stage_data',
            'trips.stage_id',
            'trips.stop_id',
            'trips.start_id' // Assuming you have this column
        )
            ->join('stages', 'trips.stage_id', '=', 'stages.id');

        if ($request->filled('trip_name')) {
            $tripsQuery->where('trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('start_date', '=', $request->start_date);
        }

        $trips = $tripsQuery->get();

        // Initialize totals
        $totalFullTicket = 0;
        $totalHalfTicket = 0;
        $totalStudentTicket = 0;
        $totalLanguageTicket = 0;
        $totalPhysicalTicket = 0;

        foreach ($trips as $trip) {
            $stageData = $trip->stage_data;
            $decodedStageData = json_decode($stageData, true);
            $stopId = $trip->stop_id;
            $startId = $trip->start_id;

            $startPrice = 0;
            $stopPrice = 0;

            // Get the last price for stop_id
            if (isset($decodedStageData[$stopId])) {
                $stage = $decodedStageData[$stopId];
                $prices = $stage['prices'];
                if (!empty($prices)) {
                    $stopPrice = end($prices); // Last price
                }
            }

            // Get the first price for start_id
            if (isset($decodedStageData[$startId])) {
                $stage = $decodedStageData[$startId];
                $prices = $stage['prices'];
                if (!empty($prices)) {
                    $startPrice = end($prices); // First price
                }
            }

            // Calculate the price difference between start and stop stages
            $priceDifference = $stopPrice - $startPrice;

            // Update totals based on dynamic price difference
            $totalFullTicket += $trip->full_ticket ;
            $totalHalfTicket += $trip->half_ticket ;
            $totalStudentTicket += $trip->student_ticket;
            $totalLanguageTicket += $trip->language_ticket;
            $totalPhysicalTicket += $trip->physical_ticket;
        }

        // Calculate sums
        $sumOfTotalTicket = $totalFullTicket + $totalHalfTicket + $totalStudentTicket + $totalLanguageTicket + $totalPhysicalTicket;

        // Fetch totals from ticketCount if needed
        $ticketCount = $dashboardService->getTicketCount();
        $totalAmount = $ticketCount->total_amount_sum;
        $totalExpense = $ticketCount->total_expense_sum;
        $netTotal = $ticketCount->net_total_sum;

        // Prepare data for view
        $para = [
            'trips' => $trips,
            'sumOfFullTicket' => $totalFullTicket,
            'sumOfHalfTicket' => $totalHalfTicket,
            'sumOfStudentTicket' => $totalStudentTicket,
            'sumOfLanguageTicket' => $totalLanguageTicket,
            'sumOfPhysicalTicket' => $totalPhysicalTicket,
            'sumOfTotalTicket' => $sumOfTotalTicket,
            'totalAmount' => $totalAmount,
            'totalExpense' => $totalExpense,
            'netTotal' => $netTotal
        ];

        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }


    public function fareReport(DashboardService $dashboardService, Request $request)
    {
        $path = $this->getView('admin.trip.fare');

        // Query to fetch trip data with stage data
        $tripsQuery = Trip::select(
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
            'stages.stage_data',
            'trips.stage_id',
            'trips.stop_id',
            'trips.start_id' // Add this if you have this column
        )
        ->join('stages', 'trips.stage_id', '=', 'stages.id');

        if ($request->filled('trip_name')) {
            $tripsQuery->where('trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('start_date', '=', $request->start_date);
        }

        $trips = $tripsQuery->get();

        // Initialize totals
        $totalFullTicketPrice = 0;
        $totalHalfTicketPrice = 0;
        $totalStudentTicketPrice = 0;
        $totalLanguageTicketPrice = 0;
        $totalPhysicalTicketPrice = 0;

        foreach ($trips as $trip) {
            $stageData = $trip->stage_data;
            $decodedStageData = json_decode($stageData, true);
            $stopId = $trip->stop_id;
            $startId = $trip->start_id; // Ensure start_id is available

            $startPrice = 0;
            $stopPrice = 0;

            // Get the last price for stop_id
            if (isset($decodedStageData[$stopId])) {
                $stage = $decodedStageData[$stopId];
                $prices = $stage['prices'] ?? [];
                if (!empty($prices)) {
                    $stopPrice = end($prices);
                }
            }

            // Get the first price for start_id
            if (isset($decodedStageData[$startId])) {
                $stage = $decodedStageData[$startId];
                $prices = $stage['prices'] ?? [];
                if (!empty($prices)) {
                    $startPrice = end($prices);
                }
            }

            // Calculate the price difference between start and stop stages
            $priceDifference = $stopPrice - $startPrice;

            // Update totals based on dynamic price difference
            $totalFullTicketPrice += $trip->full_ticket * $priceDifference;
            $totalHalfTicketPrice += $trip->half_ticket * $priceDifference * 0.5;
            $totalStudentTicketPrice += $trip->student_ticket * $priceDifference * 0.3;
            $totalLanguageTicketPrice += $trip->language_ticket * $priceDifference * 0.3;
            $totalPhysicalTicketPrice += $trip->physical_ticket * $priceDifference * 0.3;
        }

        // Calculate sums
        $sumOfTotalFare = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLanguageTicketPrice + $totalPhysicalTicketPrice;

        // Fetch totals from ticketCount if needed
        $ticketCount = $dashboardService->getTicketCount();
        $totalAmount = $ticketCount->total_amount_sum ?? 0;
        $totalExpense = $ticketCount->total_expense_sum ?? 0;
        $netTotal = $ticketCount->net_total_sum ?? 0;

        // Prepare data for view
        $para = [
            'trips' => $trips,
            'sumOfFullTicketPrice' => $totalFullTicketPrice,
            'sumOfHalfTicketPrice' => $totalHalfTicketPrice,
            'sumOfStudentTicketPrice' => $totalStudentTicketPrice,
            'sumOfLanguageTicketPrice' => $totalLanguageTicketPrice,
            'sumOfPhysicalTicketPrice' => $totalPhysicalTicketPrice,
            'sumOfTotalFare' => $sumOfTotalFare,
            'totalAmount' => $totalAmount,
            'totalExpense' => $totalExpense,
            'netTotal' => $netTotal
        ];

        $title = 'Fare Report';
        return $this->renderView($path, $para, $title);
    }




    public function stageReport(Request $request)
    {
        $path = $this->getView('admin.trip.stage');
        $trip = Trip::select(
            'trips.id',
            'trips.stage_id',
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
            DB::raw('(trips.full_ticket + trips.half_ticket + trips.student_ticket + trips.language_ticket + trips.physical_ticket) as total_ticket'),
            'trips.total_amount',
            's.route_id',
            'r.route_from',
            'r.route_to',
            'trips.created_at',
        )
            ->join(Stage::getTableName() . ' as s', 's.id', '=', 'trips.stage_id')
            ->join(Route::getTableName() . ' as r', 's.route_id', '=', 'r.id');
        if ($request->filled('trip_name')) {
            $trip->where(function ($query) use ($request) {
                $query->where('r.route_from', 'like', '%' . $request->trip_name . '%')
                    ->orWhere('r.route_to', 'like', '%' . $request->trip_name . '%');
            });
        }

        if ($request->filled('start_date')) {
            $trip->whereDate('trips.created_at', '=', $request->start_date);
        }
        $trips = $trip->get();
        $para = ['trips' => $trips];
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }

    public function inspectorReport(Request $request)
    {
        $path = $this->getView('admin.trip.inspector');
        $trip = Trip::select(
            'trips.id',
            'trips.stage_id',
            'trips.trip_name',
            'trips.physical_ticket',
            DB::raw('(trips.full_ticket + trips.half_ticket + trips.student_ticket + trips.language_ticket + trips.physical_ticket) as total_ticket'),
            'trips.total_amount',
            's.route_id',
            'r.route_from',
            'r.route_to',
            'trips.created_at',
        )
            ->join(Stage::getTableName() . ' as s', 's.id', '=', 'trips.stage_id')
            ->join(Route::getTableName() . ' as r', 's.route_id', '=', 'r.id');
        if ($request->filled('trip_name')) {
            $trip->where(function ($query) use ($request) {
                $query->where('r.route_from', 'like', '%' . $request->trip_name . '%')
                    ->orWhere('r.route_to', 'like', '%' . $request->trip_name . '%');
            });
        }

        if ($request->filled('start_date')) {
            $trip->whereDate('trips.created_at', '=', $request->start_date);
        }
        $trips = $trip->get();
        $para = ['trips' => $trips];
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }
}
