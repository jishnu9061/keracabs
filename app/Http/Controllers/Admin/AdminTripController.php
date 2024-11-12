<?php

namespace App\Http\Controllers\Admin;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Stage;
use App\Models\StartTrip;
use App\Models\TripExpense;
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
        $trip = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'start_trips.created_at as start_time',
            'start_trips.updated_at as end_time',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket')
        )
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id') // Ensure this joins on the correct relationship
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'start_time',
            'end_time'
        );
        if ($request->filled('trip_name')) {
            $trip->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $trip->whereDate('start_time', '=', $request->start_date);
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

        // Main query
        $tripsQuery = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price'),
            DB::raw('SUM(trip_expenses.diesel + trip_expenses.driver + trip_expenses.cleaner + trip_expenses.conductor + trip_expenses.stand + trip_expenses.toll + trip_expenses.wash + trip_expenses.oil + trip_expenses.bank) as total_expenses')
        )
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->leftJoin('trip_expenses', 'start_trips.id', '=', 'trip_expenses.trip_id')
        ->join('stages', 'trips.stage_id', '=', 'stages.id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'date'
        );

        // Apply filters if available
        if ($request->filled('trip_name')) {
            $tripsQuery->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('date', '=', $request->start_date);
        }

        $trips = $tripsQuery->get();

        // Initialize variables
        $totalFullTicketPrice = 0;
        $totalHalfTicketPrice = 0;
        $totalStudentTicketPrice = 0;
        $totalLanguageTicketPrice = 0;
        $totalPhysicalTicketPrice = 0;
        $grandTotalTickets = 0;

        $totalExpenses = [];
        $netTotalPerTrip = [];

        foreach ($trips as $trip) {
            $totalFullTicketPrice += $trip->total_full_ticket_price;
            $totalHalfTicketPrice += $trip->total_half_ticket_price;
            $totalStudentTicketPrice += $trip->total_student_ticket_price;
            $totalLanguageTicketPrice += $trip->total_language_ticket_price;
            $totalPhysicalTicketPrice += $trip->total_physical_ticket_price;
            $grandTotalTickets += $trip->total_ticket;

            // Calculate total expenses for each trip
            $totalExpenseForCurrentTrip = $trip->total_expenses ?? 0;
            $totalExpenses[$trip->trip_id] = $totalExpenseForCurrentTrip;

            // Calculate net total for each trip
            $netTotalPerTrip[$trip->trip_id] = $trip->grand_total_ticket_price - $totalExpenseForCurrentTrip;
        }

        // Calculate total grand ticket price and amount
        $grandTotalTicketPrice = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLanguageTicketPrice + $totalPhysicalTicketPrice;
        $ticketCount = $dashboardService->getTicketCount();
        $totalAmount = $grandTotalTicketPrice;

        // Parameters to pass to the view
        $para = [
            'trips' => $trips,
            'sumOfFullTicket' => $totalFullTicketPrice,
            'sumOfHalfTicket' => $totalHalfTicketPrice,
            'sumOfStudentTicket' => $totalStudentTicketPrice,
            'sumOfLanguageTicket' => $totalLanguageTicketPrice,
            'sumOfPhysicalTicket' => $totalPhysicalTicketPrice,
            'sumOfTotalTicket' => $grandTotalTickets,
            'totalAmount' => $totalAmount,
            'totalExpense' => $totalExpenses,
            'netTotalPerTrip' => $netTotalPerTrip,
        ];

        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }


    public function fareReport(DashboardService $dashboardService, Request $request)
    {
        $path = $this->getView('admin.trip.fare');

        // Query to fetch trip data with stage data
        $tripsQuery = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
        )
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );

        if ($request->filled('trip_name')) {
            $tripsQuery->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('date', '=', $request->start_date);
        }

        $trips = $tripsQuery->get();

        // Initialize totals
        $totalFullTicketPrice = 0;
        $totalHalfTicketPrice = 0;
        $totalStudentTicketPrice = 0;
        $totalLanguageTicketPrice = 0;
        $totalPhysicalTicketPrice = 0;

        foreach ($trips as $trip) {
            // Instead of calculating price difference, directly use the ticket price totals
            $totalFullTicketPrice += $trip->full_ticket * $trip->total_full_ticket_price;
            $totalHalfTicketPrice += $trip->half_ticket * $trip->total_half_ticket_price;
            $totalStudentTicketPrice += $trip->student_ticket * $trip->total_student_ticket_price;
            $totalLanguageTicketPrice += $trip->total_language_ticket_price;
            $totalPhysicalTicketPrice += $trip->physical_ticket * $trip->total_physical_ticket_price;
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




    // public function stageReport(Request $request)
    // {
    //     $path = $this->getView('admin.trip.stage');
    //     $trip = Trip::select(
    //         'trips.id',
    //         'trips.stage_id',
    //         'trips.trip_name',
    //         'trips.start_date',
    //         'trips.start_time',
    //         'trips.end_date',
    //         'trips.end_time',
    //         'trips.full_ticket',
    //         'trips.half_ticket',
    //         'trips.student_ticket',
    //         'trips.language_ticket',
    //         'trips.physical_ticket',
    //         DB::raw('(trips.full_ticket + trips.half_ticket + trips.student_ticket + trips.language_ticket + trips.physical_ticket) as total_ticket'),
    //         'trips.total_amount',
    //         's.route_id',
    //         'r.route_from',
    //         'r.route_to',
    //         'trips.created_at',
    //     )
    //         ->join(Stage::getTableName() . ' as s', 's.id', '=', 'trips.stage_id')
    //         ->join(Route::getTableName() . ' as r', 's.route_id', '=', 'r.id');
    //     if ($request->filled('trip_name')) {
    //         $trip->where(function ($query) use ($request) {
    //             $query->where('r.route_from', 'like', '%' . $request->trip_name . '%')
    //                 ->orWhere('r.route_to', 'like', '%' . $request->trip_name . '%');
    //         });
    //     }

    //     if ($request->filled('start_date')) {
    //         $trip->whereDate('trips.created_at', '=', $request->start_date);
    //     }
    //     $trips = $trip->get();
    //     $para = ['trips' => $trips];
    //     $title = 'Print';
    //     return $this->renderView($path, $para, $title);
    // }

    public function stageReport(Request $request)
    {
        $path = $this->getView('admin.trip.stage');

        // Initialize the start trips query with selected fields
        $trip = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
)
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );

        // Filter based on trip name if provided
        if ($request->filled('trip_name')) {
            $trip->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        // Filter based on start date if provided
        if ($request->filled('start_date')) {
            $trip->whereDate('date', '=', $request->start_date);
        }

        // Execute the query to get trips
        $trips = $trip->get();

        // Initialize totals for ticket counts and amounts
        $totalFullTicket = 0;
        $totalHalfTicket = 0;
        $totalStudentTicket = 0;
        $totalLanguageTicket = 0;
        $totalPhysicalTicket = 0;
        $totalAmount = 0;

        // Process each trip to decode stage_data and extract start and end stage names
        foreach ($trips as $trip) {
            $stageData = json_decode($trip->stage_data, true);

            // Debugging to check stage data
            if (!is_array($stageData)) {
                $trip->start_stage_name = 'Unknown';
                $trip->end_stage_name = 'Unknown';
                continue;
            }

            // Ensure start_id and stop_id exist and are valid
            $startStage = $stageData[$trip->start_id] ?? null;
            $endStage = $stageData[$trip->stop_id] ?? null;

            // Assign stage names
            $trip->start_stage_name = $startStage['stage_name'] ?? 'Unknown';
            $trip->end_stage_name = $endStage['stage_name'] ?? 'Unknown';
            $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

            // Unset fields that are not needed in the response
            unset($trip->stage_data);

            // Accumulate ticket counts and amounts
            $totalFullTicket += $trip->full_ticket;
            $totalHalfTicket += $trip->half_ticket;
            $totalStudentTicket += $trip->student_ticket;
            $totalLanguageTicket += $trip->language_ticket;
            $totalPhysicalTicket += $trip->physical_ticket;
            $totalAmount += $trip->total_amount;
        }

        // Prepare data for view
        $para = [
            'trips' => $trips,
            'totalFullTicket' => $totalFullTicket,
            'totalHalfTicket' => $totalHalfTicket,
            'totalStudentTicket' => $totalStudentTicket,
            'totalLanguageTicket' => $totalLanguageTicket,
            'totalPhysicalTicket' => $totalPhysicalTicket,
            'totalAmount' => $totalAmount,
        ];
        // dd($para);
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }

    public function inspectorReport(Request $request)
    {
        $path = $this->getView('admin.trip.inspector');

        $tripsQuery = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
)
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );

        // Apply filters if provided
        if ($request->filled('trip_name')) {
            $tripsQuery->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('date', '=', $request->start_date);
        }

        // Execute the query to get trips
        $trips = $tripsQuery->get();
        // dd($trips);

        // Process the trips as required...
        foreach ($trips as $trip) {
            // Example processing
            $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');
            unset($trip->created_at); // Unset any unnecessary fields if needed
        }

        $para = ['trips' => $trips];
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }


    public function printTrip(Request $request)
    {
        $path = $this->getView('admin.trip.print.trip-report');
        $trip = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket')
        )
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id') // Ensure this joins on the correct relationship
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time'
        )->get();
        $para = ['trips' => $trip];
        $title = 'Route Stops';
        return $this->renderView($path, $para, $title);
    }

    public function printCollection(DashboardService $dashboardService,Request $request)
    {
        $path = $this->getView('admin.trip.print.collection');
        $tripsQuery = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'), // Fixed typo here
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price'),
            DB::raw('SUM(trip_expenses.diesel + trip_expenses.driver + trip_expenses.cleaner + trip_expenses.conductor + trip_expenses.stand + trip_expenses.toll + trip_expenses.wash + trip_expenses.oil + trip_expenses.bank) as total_expenses')
)
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->leftJoin('trip_expenses', 'start_trips.id', '=', 'trip_expenses.trip_id')
        ->join('stages', 'trips.stage_id', '=', 'stages.id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'date'
        );

        if ($request->filled('trip_name')) {
            $tripsQuery->where('start_trips.trip_name', 'like', '%' . $request->trip_name . '%');
        }

        if ($request->filled('start_date')) {
            $tripsQuery->whereDate('start_trips.created_at', '=', $request->start_date);
        }

        $trips = $tripsQuery->get();

        $totalFullTicketPrice = 0;
        $totalHalfTicketPrice = 0;
        $totalStudentTicketPrice = 0;
        $totalLanguageTicketPrice = 0;
        $totalPhysicalTicketPrice = 0;
        $grandTotalTickets = 0;

        $totalExpenses = [];

        foreach ($trips as $trip) {
            $totalFullTicketPrice += $trip->total_full_ticket_price;
            $totalHalfTicketPrice += $trip->total_half_ticket_price;
            $totalStudentTicketPrice += $trip->total_student_ticket_price;
            $totalLanguageTicketPrice += $trip->total_language_ticket_price;
            $totalPhysicalTicketPrice += $trip->total_physical_ticket_price;
            $grandTotalTicketPrice = $trip->grand_total_ticket_price;
            $grandTotalTickets += $trip->total_ticket;
            $grandTotalExpense = $trip->total_expenses ?? 0;

            $totalExpenses[$trip->trip_id] = $totalExpense->total_expenses ?? 0;
        }

        $grandTotalTicketPrice = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLanguageTicketPrice + $totalPhysicalTicketPrice;
        $ticketCount = $dashboardService->getTicketCount();
        $totalAmount = $grandTotalTicketPrice;

        $netTotalPerTrip = [];
        foreach ($trips as $trip) {
            $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
            ->selectRaw('SUM(diesel + driver + cleaner + conductor + stand + toll + wash + oil + bank) as total_expense')
            ->first();
            $totalExpenseForCurrentTrip = $totalExpense->total_expense ?? 0;
            $netTotalPerTrip[$trip->trip_id] = $grandTotalTicketPrice - ($totalExpenses[$trip->trip_id] ?? 0);
        }

        $para = [
            'trips' => $trips,
            'sumOfFullTicket' => $totalFullTicketPrice,
            'sumOfHalfTicket' => $totalHalfTicketPrice,
            'sumOfStudentTicket' => $totalStudentTicketPrice,
            'sumOfLanguageTicket' => $totalLanguageTicketPrice,
            'sumOfPhysicalTicket' => $totalPhysicalTicketPrice,
            'sumOfTotalTicket' => $grandTotalTickets,
            'totalAmount' => $totalAmount,
            'totalExpense' => $totalExpenses,
            'netTotalPerTrip' => $netTotalPerTrip,
            'totalExpenseForCurrentTrip'=>$totalExpenseForCurrentTrip
        ];

        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }

    public function printFare(Request $request,DashboardService $dashboardService)
    {
        $path = $this->getView('admin.trip.print.fare');
        $tripsQuery = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
        )
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );

        $trips = $tripsQuery->get();

        // Initialize totals
        $totalFullTicketPrice = 0;
        $totalHalfTicketPrice = 0;
        $totalStudentTicketPrice = 0;
        $totalLanguageTicketPrice = 0;
        $totalPhysicalTicketPrice = 0;

        foreach ($trips as $trip) {
            // Instead of calculating price difference, directly use the ticket price totals
            $totalFullTicketPrice += $trip->full_ticket * $trip->total_full_ticket_price;
            $totalHalfTicketPrice += $trip->half_ticket * $trip->total_half_ticket_price;
            $totalStudentTicketPrice += $trip->student_ticket * $trip->total_student_ticket_price;
            $totalLanguageTicketPrice += $trip->total_language_ticket_price;
            $totalPhysicalTicketPrice += $trip->physical_ticket * $trip->total_physical_ticket_price;
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

    public function printStage(Request $request)
    {
        $path = $this->getView('admin.trip.print.stage');
        $trip = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
)
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );

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

        foreach ($trips as $trip) {
            $stageData = json_decode($trip->stage_data, true);

            if (!is_array($stageData)) {
                $trip->start_stage_name = 'Unknown';
                $trip->end_stage_name = 'Unknown';
                continue;
            }

            $startStage = $stageData[$trip->start_id] ?? null;
            $endStage = $stageData[$trip->stop_id] ?? null;

            $trip->start_stage_name = $startStage['stage_name'] ?? 'Unknown';
            $trip->end_stage_name = $endStage['stage_name'] ?? 'Unknown';
            $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

            unset($trip->stage_data);
        }
        $para = ['trips' => $trips];
        $title = 'Print';
        return $this->renderView($path, $para, $title);
    }

    public function printInspector(Request $request)
    {
        $path = $this->getView('admin.trip.print.inspector');
        $trip = StartTrip::select(
            'start_trips.id as start_trip_id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'start_trips.created_at as date',
            DB::raw('SUM(trips.full_ticket) as full_ticket'),
            DB::raw('SUM(trips.full_ticket_price) as total_full_ticket_price'),
            DB::raw('SUM(trips.half_ticket) as half_ticket'),
            DB::raw('SUM(trips.half_ticket_price) as total_half_ticket_price'),
            DB::raw('SUM(trips.student_ticket) as student_ticket'),
            DB::raw('SUM(trips.student_ticket_price) as total_student_ticket_price'),
            DB::raw('SUM(trips.language_ticket) as language_ticket'),
            DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
            DB::raw('SUM(trips.physical_ticket) as physical_ticket'),
            DB::raw('SUM(trips.physical_ticket_price) as total_physical_ticket_price'),
            DB::raw('(SUM(trips.full_ticket) + SUM(trips.half_ticket) + SUM(trips.student_ticket) + SUM(trips.language_ticket) + SUM(trips.physical_ticket)) as total_ticket'),
            DB::raw('(SUM(trips.full_ticket_price * trips.full_ticket) + SUM(trips.half_ticket_price * trips.half_ticket) + SUM(trips.student_ticket_price * trips.student_ticket) + SUM(trips.lagguage_ticket_price) + SUM(trips.physical_ticket_price * trips.physical_ticket)) as grand_total_ticket_price')
)
        ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
        ->join('routes', 'routes.id', '=', 'start_trips.route_id')
        ->groupBy(
            'start_trips.id',
            'trips.trip_id',
            'start_trips.trip_name',
            'trips.start_date',
            'trips.start_time',
            'trips.end_date',
            'trips.end_time',
            'routes.route_from',
            'routes.route_to',
            'date'
        );
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
