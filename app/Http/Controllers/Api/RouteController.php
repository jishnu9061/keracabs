<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Device;
use App\Models\StartDay;
use App\Models\StartTrip;
use App\Models\QrCodeModel;
use App\Models\TripExpense;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceRouteAssignment;
use Illuminate\Support\Facades\Crypt;
use App\Http\Constants\AdminConstants;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiBaseController;

class RouteController extends ApiBaseController
{
    public function getDeviceRoute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        } else {
            $routes = DB::table('device_route_assignments as da')
                ->join('routes as r', 'da.route_id', '=', 'r.id')
                ->where('da.device_id', $request->device_id)
                ->select('r.id', 'r.route_from', 'r.route_to', 'r.created_at', 'r.updated_at')
                ->get();
            $liveTickets = Trip::join('devices', 'trips.device_id', '=', 'devices.id')
                ->join('stages', 'trips.stage_id', '=', 'stages.id')
                ->where('trips.device_id', $request->device_id)
                ->orderBy('trips.created_at', 'desc')
                ->select('trips.id', 'trips.trip_name', 'trips.created_at', 'devices.header_one', 'devices.header_one', 'devices.header_two', 'devices.footer', 'stages.stage_data', 'trips.start_id', 'trips.stop_id', 'trips.full_ticket', 'trips.half_ticket', 'trips.student_ticket', 'trips.physical_ticket', 'trips.language_ticket', 'trips.full_ticket_price','trips.half_ticket_price','trips.student_ticket_price','trips.physical_ticket_price','trips.lagguage_ticket_price')
                ->get();
            return $this->sendResponse($routes, 'Route List');
        }
    }

    // public function getStages(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'route_id' => 'required|string|exists:routes,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $stage = Stage::where('route_id', $request->route_id)->first();

    //     if (!$stage) {
    //         return $this->sendError('Stage not found');
    //     }

    //     $stageData = json_decode($stage->stage_data, true);

    //     $formattedStages = [];

    //     foreach ($stageData as $key => $data) {
    //         $formattedStages[] = [
    //             'id' => (int) $key,
    //             'stage_name' => $data['stage_name'],
    //             'price' => (int) end($data['prices'])
    //         ];
    //     }

    //     return $this->sendResponse($formattedStages, 'Stage List');
    // }
    public function getStages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|string|exists:routes,id',
            'status' => 'required|in:' . implode(',', [AdminConstants::ROUTE_STATUS_UP, AdminConstants::ROUTE_STATUS_DOWN])
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Determine the order direction based on status
        $orderDirection = $request->status == AdminConstants::ROUTE_STATUS_UP ? 'asc' : 'desc';

        // Retrieve the stage based on route_id and status
        $stage = Stage::where('route_id', $request->route_id)
            ->orderBy('id', $orderDirection) // Assuming ordering by 'id'
            ->first();

        if (!$stage) {
            return $this->sendError('Stage not found');
        }

        // Decode stage_data from JSON
        $stageData = json_decode($stage->stage_data, true);

        // Check if stageData is validf
        if (!is_array($stageData)) {
            return $this->sendError('Invalid stage data format');
        }

        // Convert associative array to a list of stages with IDs as integer keys
        $stages = [];
        foreach ($stageData as $key => $data) {
            $stages[(int)$key] = [
                'stage_name' => $data['stage_name'] ?? 'N/A',
                'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
            ];
        }

        // Sort stages based on the ID
        ksort($stages); // Sorts the array by keys in ascending order

        // Reverse the stages if descending order is needed
        if ($orderDirection === 'desc') {
            $stages = array_reverse($stages, true);
        }

        // Calculate prices for descending order
        $maxPrice = $orderDirection === 'desc' ? (count($stages) ? reset($stages)['price'] : 0) : 0;
        $formattedStages = [];

        foreach ($stages as $id => $data) {
            if ($orderDirection === 'asc') {
                $formattedStages[] = [
                    'id' => $id,
                    'stage_name' => $data['stage_name'],
                    'price' => $data['price']
                ];
            } else {
                // In descending order, calculate price difference from the maximum price
                $formattedStages[] = [
                    'id' => $id,
                    'stage_name' => $data['stage_name'],
                    'price' => $maxPrice - $data['price']
                ];
            }
        }

        return $this->sendResponse($formattedStages, 'Stage List');
    }

    // public function getAllFares(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'route_id' => 'required|string|exists:routes,id',
    //         'status' => 'required|in:' . implode(',', [AdminConstants::ROUTE_STATUS_UP, AdminConstants::ROUTE_STATUS_DOWN])
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $route = Route::find($request->route_id);
    //     $studentRoute = Route::where('route_from', $route->route_from)
    //                          ->where('route_to', $route->route_to)
    //                          ->where('type', 2)
    //                          ->first();

    //     // Determine the order direction based on status
    //     $orderDirection = $request->status == AdminConstants::ROUTE_STATUS_UP ? 'asc' : 'desc';

    //     // Retrieve the stages for both regular route and student route
    //     $studentStage = Stage::where('route_id', $studentRoute->id)
    //                          ->orderBy('id', $orderDirection)
    //                          ->first();

    //     $stage = Stage::where('route_id', $request->route_id)
    //                   ->orderBy('id', $orderDirection)
    //                   ->first();

    //     if (!$stage) {
    //         return $this->sendError('Stage not found');
    //     }

    //     // Decode stage_data for both stages
    //     $stageData = json_decode($stage->stage_data, true);
    //     $studentStageData = json_decode($studentStage->stage_data, true);

    //     if (!is_array($stageData) || !is_array($studentStageData)) {
    //         return $this->sendError('Invalid stage data format');
    //     }

    //     // Prepare arrays for regular stages and student stages
    //     $regularStages = [];
    //     $studentStages = [];

    //     // Process the stages separately for regular and student routes
    //     foreach ($stageData as $key => $data) {
    //         $regularStages[(int)$key] = [
    //             'stage_name' => $data['stage_name'] ?? 'N/A',
    //             'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
    //         ];
    //     }

    //     foreach ($studentStageData as $key => $data) {
    //         $studentStages[(int)$key] = [
    //             'stage_name' => $data['stage_name'] ?? 'N/A',
    //             'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
    //         ];
    //     }

    //     // Sort stages based on the ID
    //     ksort($regularStages); // Sort by keys in ascending order
    //     ksort($studentStages); // Sort student stages by keys as well

    //     // Reverse the stages if descending order is needed
    //     if ($orderDirection === 'desc') {
    //         $regularStages = array_reverse($regularStages, true);
    //         $studentStages = array_reverse($studentStages, true);
    //     }

    //     // Calculate descending prices if needed
    //     $maxRegularPrice = $orderDirection === 'desc' ? (count($regularStages) ? reset($regularStages)['price'] : 0) : 0;
    //     $maxStudentPrice = $orderDirection === 'desc' ? (count($studentStages) ? reset($studentStages)['price'] : 0) : 0;

    //     // Format regular stages
    //     $formattedRegularStages = [];
    //     foreach ($regularStages as $id => $data) {
    //         if ($orderDirection === 'asc') {
    //             $formattedRegularStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $data['stage_name'],
    //                 'price' => $data['price']
    //             ];
    //         } else {
    //             $formattedRegularStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $data['stage_name'],
    //                 'price' => $maxRegularPrice - $data['price']
    //             ];
    //         }
    //     }

    //     // Format student stages
    //     $formattedStudentStages = [];
    //     foreach ($studentStages as $id => $data) {
    //         if ($orderDirection === 'asc') {
    //             $formattedStudentStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $data['stage_name'],
    //                 'price' => $data['price']
    //             ];
    //         } else {
    //             $formattedStudentStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $data['stage_name'],
    //                 'price' => $maxStudentPrice - $data['price']
    //             ];
    //         }
    //     }

    //     // Return both regular and student stages separately
    //     return $this->sendResponse([
    //         'regular_stages' => $formattedRegularStages,
    //         'student_stages' => $formattedStudentStages
    //     ], 'Stage List with Separate Regular and Student Prices');
    // }
    // public function getAllFares(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'route_id' => 'required|string|exists:routes,id',
    //         'status' => 'required|in:' . implode(',', [AdminConstants::ROUTE_STATUS_UP, AdminConstants::ROUTE_STATUS_DOWN])
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $route = Route::find($request->route_id);
    //     $studentRoute = Route::where('route_from', $route->route_from)
    //                          ->where('route_to', $route->route_to)
    //                          ->where('type', 2)
    //                          ->first();
    //     // Determine the order direction based on status
    //     $orderDirection = $request->status == AdminConstants::ROUTE_STATUS_UP ? 'asc' : 'desc';

    //     // Retrieve the stages for both regular route and student route
    //     $studentStage = Stage::where('route_id', $studentRoute->id)
    //                          ->orderBy('id', $orderDirection)
    //                          ->first();

    //     $stage = Stage::where('route_id', $request->route_id)
    //                   ->orderBy('id', $orderDirection)
    //                   ->first();

    //     if (!$stage) {
    //         return $this->sendError('Stage not found');
    //     }

    //     // Decode stage_data for both stages
    //     $stageData = json_decode($stage->stage_data, true);
    //     $studentStageData = json_decode($studentStage->stage_data, true);

    //     if (!is_array($stageData) || !is_array($studentStageData)) {
    //         return $this->sendError('Invalid stage data format');
    //     }

    //     // Prepare arrays for regular stages and student stages
    //     $regularStages = [];
    //     $studentStages = [];

    //     // Process the stages separately for regular and student routes
    //     foreach ($stageData as $key => $data) {
    //         $regularStages[(int)$key] = [
    //             'stage_name' => $data['stage_name'] ?? 'N/A',
    //             'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
    //         ];
    //     }

    //     foreach ($studentStageData as $key => $data) {
    //         $studentStages[(int)$key] = [
    //             'stage_name' => $data['stage_name'] ?? 'N/A',
    //             'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
    //         ];
    //     }

    //     // Sort stages based on the ID
    //     ksort($regularStages); // Sort by keys in ascending order
    //     ksort($studentStages); // Sort student stages by keys as well

    //     // Reverse the stages if descending order is needed
    //     if ($orderDirection === 'desc') {
    //         $regularStages = array_reverse($regularStages, true);
    //         $studentStages = array_reverse($studentStages, true);
    //     }

    //     // Combine both regular and student prices
    //     $combinedStages = [];
    //     foreach ($regularStages as $id => $regularStage) {
    //         $studentPrice = $studentStages[$id]['price'] ?? 0;

    //         if ($orderDirection === 'asc') {
    //             $combinedStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $regularStage['stage_name'],
    //                 'regular_price' => $regularStage['price'],
    //                 'student_price' => $studentPrice
    //             ];
    //         } else {
    //             $maxRegularPrice = count($regularStages) ? reset($regularStages)['price'] : 0;
    //             $maxStudentPrice = count($studentStages) ? reset($studentStages)['price'] : 0;

    //             $combinedStages[] = [
    //                 'id' => $id,
    //                 'stage_name' => $regularStage['stage_name'],
    //                 'regular_price' => $maxRegularPrice - $regularStage['price'],
    //                 'student_price' => $maxStudentPrice - $studentPrice
    //             ];
    //         }
    //     }

    //     $status = $studentStage ? true : false;

    //     // Return both regular and student stages in a combined format
    //     return $this->sendResponse([
    //         'student_price_status' => $status,
    //         'stages' => $combinedStages
    //     ], 'Stage List with Regular and Student Prices');
    // }
    public function getAllFares(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|string|exists:routes,id',
            'status' => 'required|in:' . implode(',', [AdminConstants::ROUTE_STATUS_UP, AdminConstants::ROUTE_STATUS_DOWN])
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $route = Route::find($request->route_id);
        $studentRoute = Route::where('route_from', $route->route_from)
            ->where('route_to', $route->route_to)
            ->where('type', 2)
            ->first();

        // Determine the order direction based on status
        $orderDirection = $request->status == AdminConstants::ROUTE_STATUS_UP ? 'asc' : 'desc';

        // Retrieve the stages for both routes
        $studentStage = $studentRoute ? Stage::where('route_id', $studentRoute->id)
            ->orderBy('id', $orderDirection)
            ->first() : null;

        $stage = Stage::where('route_id', $request->route_id)
            ->orderBy('id', $orderDirection)
            ->first();

        if (!$stage) {
            return $this->sendError('Stage not found');
        }

        // Decode stage_data for both stages
        $stageData = json_decode($stage->stage_data, true);
        $studentStageData = $studentStage ? json_decode($studentStage->stage_data, true) : [];

        if (!is_array($stageData) || !is_array($studentStageData)) {
            return $this->sendError('Invalid stage data format');
        }

        // Prepare arrays for regular stages and student stages
        $regularStages = [];
        $studentStages = [];

        // Process the stages for regular routes
        foreach ($stageData as $key => $data) {
            $regularStages[(int)$key] = [
                'stage_name' => $data['stage_name'] ?? 'N/A',
                // 'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
                'price' => !empty($data['prices']) && is_array($data['prices']) ? (int) $data['prices'][0] : 0
            ];
        }

        // Process the stages for student routes
        foreach ($studentStageData as $key => $data) {
            $studentStages[(int)$key] = [
                'stage_name' => $data['stage_name'] ?? 'N/A',
                // 'price' => !empty($data['prices']) ? (int) end($data['prices']) : 0
                'price' => !empty($data['prices']) && is_array($data['prices']) ? (int) $data['prices'][0] : 0
            ];
        }

        // Sort stages by ID
        ksort($regularStages);
        ksort($studentStages);

        // Reverse the stages if descending order is needed
        if ($orderDirection === 'desc') {
            $regularStages = array_reverse($regularStages, true);
            $studentStages = array_reverse($studentStages, true);
        }

        // Combine both regular and student prices
        $combinedStages = [];
        foreach ($regularStages as $id => $regularStage) {
            $studentPrice = isset($studentStages[$id]) ? $studentStages[$id]['price'] : 0; // Default to 0 if student stage doesn't exist

            if ($orderDirection === 'asc') {
                $combinedStages[] = [
                    'id' => $id,
                    'stage_name' => $regularStage['stage_name'],
                    'regular_price' => $regularStage['price'],
                    'student_price' => $studentPrice
                ];
            } else {
                $maxRegularPrice = count($regularStages) ? reset($regularStages)['price'] : 0;
                $maxStudentPrice = count($studentStages) ? reset($studentStages)['price'] : 0;

                $combinedStages[] = [
                    'id' => $id,
                    'stage_name' => $regularStage['stage_name'],
                    'regular_price' => $maxRegularPrice - $regularStage['price'],
                    'student_price' => $maxStudentPrice - $studentPrice
                ];
            }
        }

        $status = $studentStage ? true : false;

        // Return both regular and student stages in a combined format
        return $this->sendResponse([
            'student_price_status' => $status,
            'stages' => $combinedStages
        ], 'Stage List with Regular and Student Prices');
    }





    // public function bookTicket(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|string',
    //         'start_day_id' => 'required|string',
    //         'device_id' => 'required|string|exists:devices,id',
    //         'route_id' => 'required|string|exists:routes,id',
    //         'start_id' => 'required|string',
    //         'stop_id' => 'required|string',
    //         'route_status' => 'required|string',
    //         'full_ticket' => 'nullable|string',
    //         'half_ticket' => 'nullable|string',
    //         'student_ticket' => 'nullable|string',
    //         'lagguage_ticket' => 'nullable|string',
    //         'physical_ticket' => 'nullable|string',
    //         'full_ticket_price' => 'nullable|string',
    //         'half_ticket_price' => 'nullable|string',
    //         'student_ticket_price' => 'nullable|string',
    //         'lagguage_ticket_price' => 'nullable|string',
    //         'physical_ticket_price' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $latestTrip = Trip::orderBy('created_at', 'desc')->first();
    //     $nextNumber = $latestTrip ? (int) substr($latestTrip->trip_name, 4) + 1 : 1;
    //     $tripName = sprintf('TICKET%04d', $nextNumber);

    //     $stage = Stage::where('route_id', $request->route_id)->first();
    //     if (!$stage) {
    //         return $this->sendError('No stage data found for the provided route.');
    //     }

    //     $totalFullTicketPrice = $request->full_ticket * $request->full_ticket_price;
    //     $totalHalfTicketPrice = $request->half_ticket * $request->half_ticket_price;
    //     $totalStudentTicketPrice = $request->student_ticket * $request->student_ticket_price;
    //     $totalLuggageTicketPrice = $request->lagguage_ticket * $request->lagguage_ticket_price;
    //     $totalPhysicalTicketPrice = $request->physical_ticket * $request->physical_ticket_price;

    //     $totalAmount = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLuggageTicketPrice + $totalPhysicalTicketPrice;
    //     $totalExpense = 0;
    //     $netTotal = $totalAmount - $totalExpense;

    //     $trip = Trip::create([
    //         'trip_id' =>  $request->trip_id,
    //         'start_day_id' => $request->start_day_id,
    //         'device_id' => $request->device_id,
    //         'stage_id' => $stage->id,
    //         'trip_name' => $tripName,
    //         'start_id' => $request->start_id,
    //         'stop_id' => $request->stop_id,
    //         'start_date' => $request->start_date,
    //         'start_time' => $request->start_time,
    //         'end_date' => $request->end_date,
    //         'end_time' => $request->end_time,
    //         'full_ticket' => $request->full_ticket ?? 0,
    //         'half_ticket' => $request->half_ticket ?? 0,
    //         'student_ticket' => $request->student_ticket ?? 0,
    //         'language_ticket' => $request->lagguage_ticket ?? 0,
    //         'physical_ticket' => $request->physical_ticket ?? 0,
    //         'total_amount' => $totalAmount,
    //         'total_expense' => $totalExpense,
    //         'net_total' => $netTotal,
    //         'route_status' => $request->route_status,
    //         'full_ticket_price' => $request->full_ticket_price,
    //         'half_ticket_price' => $request->half_ticket_price,
    //         'student_ticket_price' => $request->student_ticket_price,
    //         'lagguage_ticket_price' => $request->lagguage_ticket_price,
    //         'physical_ticket_price' => $request->physical_ticket_price,
    //     ]);
    //     return $this->sendResponse($trip, 'Ticket booked successfully');
    // }
    public function bookTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tickets' => 'required|array',
            'tickets.*.trip_id' => 'required|string',
            'tickets.*.start_day_id' => 'required|string',
            'tickets.*.device_id' => 'required|string|exists:devices,id',
            'tickets.*.route_id' => 'required|string|exists:routes,id',
            'tickets.*.start_id' => 'required|string',
            'tickets.*.stop_id' => 'required|string',
            'tickets.*.route_status' => 'required|string',
            'tickets.*.full_ticket' => 'nullable|string',
            'tickets.*.half_ticket' => 'nullable|string',
            'tickets.*.student_ticket' => 'nullable|string',
            'tickets.*.lagguage_ticket' => 'nullable|string',
            'tickets.*.physical_ticket' => 'nullable|string',
            'tickets.*.full_ticket_price' => 'nullable|string',
            'tickets.*.half_ticket_price' => 'nullable|string',
            'tickets.*.student_ticket_price' => 'nullable|string',
            'tickets.*.lagguage_ticket_price' => 'nullable|string',
            'tickets.*.physical_ticket_price' => 'nullable|string',
            'tickets.*.date' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $createdTrips = [];

        foreach ($request->tickets as $ticketData) {
            $latestTrip = Trip::orderBy('created_at', 'desc')->first();
            $nextNumber = $latestTrip ? (int) substr($latestTrip->trip_name, 4) + 1 : 1;
            $tripName = sprintf('TICKET%04d', $nextNumber);

            $stage = Stage::where('route_id', $ticketData['route_id'])->first();
            if (!$stage) {
                return $this->sendError('No stage data found for the provided route.');
            }

            $totalFullTicketPrice = $ticketData['full_ticket'] * $ticketData['full_ticket_price'];
            $totalHalfTicketPrice = $ticketData['half_ticket'] * $ticketData['half_ticket_price'];
            $totalStudentTicketPrice = $ticketData['student_ticket'] * $ticketData['student_ticket_price'];
            $totalLuggageTicketPrice = $ticketData['lagguage_ticket'] * $ticketData['lagguage_ticket_price'];
            $totalPhysicalTicketPrice = $ticketData['physical_ticket'] * $ticketData['physical_ticket_price'];

            $totalAmount = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLuggageTicketPrice + $totalPhysicalTicketPrice;
            $totalExpense = 0;
            $netTotal = $totalAmount - $totalExpense;

            $trip = Trip::create([
                'trip_id' =>  $ticketData['trip_id'],
                'start_day_id' => $ticketData['start_day_id'],
                'device_id' => $ticketData['device_id'],
                'stage_id' => $stage->id,
                'trip_name' => $tripName,
                'start_id' => $ticketData['start_id'],
                'stop_id' => $ticketData['stop_id'],
                'full_ticket' => $ticketData['full_ticket'] ?? 0,
                'half_ticket' => $ticketData['half_ticket'] ?? 0,
                'student_ticket' => $ticketData['student_ticket'] ?? 0,
                'language_ticket' => $ticketData['lagguage_ticket'] ?? 0,
                'physical_ticket' => $ticketData['physical_ticket'] ?? 0,
                'total_amount' => $totalAmount,
                'total_expense' => $totalExpense,
                'net_total' => $netTotal,
                'route_status' => $ticketData['route_status'],
                'full_ticket_price' => $ticketData['full_ticket_price'],
                'half_ticket_price' => $ticketData['half_ticket_price'],
                'student_ticket_price' => $ticketData['student_ticket_price'],
                'lagguage_ticket_price' => $ticketData['lagguage_ticket_price'],
                'physical_ticket_price' => $ticketData['physical_ticket_price'],
                'created_at' => $ticketData['created_at'] ?? Carbon::now()
            ]);

            $createdTrips[] = $trip;
        }

        return $this->sendResponse($createdTrips, 'Tickets booked successfully');
    }
    public function getPreviousBooking(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Fetch trips along with route and stage data
        $trips = Trip::select(
            'trips.*',
            'routes.route_from',
            'routes.route_to',
            'stages.stage_data' // Only select stage data for decoding
        )
            ->join('stages', 'trips.stage_id', '=', 'stages.id')
            ->join('routes', 'stages.route_id', '=', 'routes.id')
            ->where('trips.device_id', $request->device_id)
            ->orderBy('trips.created_at', 'desc')
            ->get();

        // Process each trip to decode stage_data and extract start and end stage names
        foreach ($trips as $trip) {
            $stageData = json_decode($trip->stage_data, true);

            // Get the start and end stage names based on start_id and stop_id
            $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
            $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;
            $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

            // Unset the stage_data field to avoid returning it
            unset($trip->stage_data, $trip->start_date, $trip->start_time, $trip->end_date, $trip->end_time);
        }

        // Return the trips with only the start and end stage names
        return $this->sendResponse($trips, 'Trips with stage data retrieved successfully.');
    }


    // public function getTripReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Initialize total quantities for each ticket type and total amount, expenses
    //     $totalFullTickets = 0;
    //     $totalHalfTickets = 0;
    //     $totalStudentTickets = 0;
    //     $totalLuggageTickets = 0;
    //     $totalPhysicalTickets = 0;

    //     $totalAmount = 0;
    //     $totalExpense = 0;
    //     $netTotal = 0;

    //     $trips = Trip::select(
    //         'trips.*',
    //         'routes.route_from',
    //         'routes.route_to',
    //         'stages.stage_data'
    //     )
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //         ->join('routes', 'stages.route_id', '=', 'routes.id')
    //         ->where('trips.device_id', $request->device_id)
    //         ->orderBy('trips.created_at', 'desc')
    //         ->get();

    //     foreach ($trips as $trip) {
    //         $stageData = json_decode($trip->stage_data, true);

    //         // Set stage names
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //         // Format the created_at date
    //         $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

    //         // Calculate total tickets for each trip
    //         $trip->total_tickets = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //         // Sum the total quantities for each ticket type
    //         $totalFullTickets += $trip->full_ticket;
    //         $totalHalfTickets += $trip->half_ticket;
    //         $totalStudentTickets += $trip->student_ticket;
    //         $totalLuggageTickets += $trip->language_ticket;
    //         $totalPhysicalTickets += $trip->physical_ticket;

    //         // Sum total amount and expenses
    //         $totalAmount += $trip->total_amount;
    //         $totalExpense += $trip->total_expense;

    //         // Calculate net total (amount - expense)
    //         $netTotal += ($trip->total_amount - $trip->total_expense);

    //         // Remove stage data from the response
    //         unset($trip->stage_data);
    //     }

    //     // Prepare total quantities, amounts, and net total for the response
    //     $totals = [
    //         'total_full_tickets' => $totalFullTickets,
    //         'total_half_tickets' => $totalHalfTickets,
    //         'total_student_tickets' => $totalStudentTickets,
    //         'total_luggage_tickets' => $totalLuggageTickets,
    //         'total_physical_tickets' => $totalPhysicalTickets,
    //         'total_amount' => $totalAmount,
    //         'total_expense' => $totalExpense,
    //         'net_total' => $netTotal
    //     ];

    //     return $this->sendResponse([
    //         'trips' => $trips,
    //         'totals' => $totals
    //     ], 'Trips with stage data, total tickets, amounts, and expenses retrieved successfully.');
    // }
    // public function getTripReport(Request $request)
    // {
    //     // Validate the incoming request
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Query to fetch trip data along with stage names
    //     $trips = Trip::select(
    //         'trips.id',
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
    //         'trips.created_at',
    //         'stages.stage_data',
    //         'start_stage.name as start_stage_name',
    //         'stop_stage.name as stop_stage_name',
    //         'trips.stage_id',
    //         'trips.stop_id',
    //         'trips.start_id'
    //     )
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')  // Joining stages for stage data
    //         // Join to get start stage name
    //         ->join('stages as start_stage', 'trips.start_id', '=', 'start_stage.id')
    //         // Join to get stop stage name
    //         ->join('stages as stop_stage', 'trips.stop_id', '=', 'stop_stage.id')
    //         ->where('trips.device_id', $request->device_id)
    //         ->get();

    //     // Initialize an array to hold fare details for each trip
    //     $tripFareDetails = [];

    //     foreach ($trips as $trip) {
    //         $stageData = $trip->stage_data;
    //         $decodedStageData = json_decode($stageData, true);
    //         $stopId = $trip->stop_id;
    //         $startId = $trip->start_id;

    //         $startPrice = 0;
    //         $stopPrice = 0;

    //         // Fetch stop price
    //         if (isset($decodedStageData[$stopId])) {
    //             $stage = $decodedStageData[$stopId];
    //             $prices = $stage['prices'] ?? [];
    //             if (!empty($prices)) {
    //                 $stopPrice = end($prices);  // Get last price for stop
    //             }
    //         }

    //         // Fetch start price
    //         if (isset($decodedStageData[$startId])) {
    //             $stage = $decodedStageData[$startId];
    //             $prices = $stage['prices'] ?? [];
    //             if (!empty($prices)) {
    //                 $startPrice = end($prices);  // Get last price for start
    //             }
    //         }

    //         if($trip->route_status == AdminConstants::ROUTE_STATUS_UP) {
    //                 $priceDifference = $stopPrice - $startPrice;
    //             } else if($trip->route_status == AdminConstants::ROUTE_STATUS_DOWN){
    //                 $priceDifference = $startPrice - $stopPrice;
    //             }

    //         // Calculate fare for each ticket type based on the price difference
    //         $fullTicketFare = $trip->full_ticket * $priceDifference;
    //         $halfTicketFare = $trip->half_ticket * $priceDifference * 0.5;
    //         $studentTicketFare = $trip->student_ticket * $priceDifference * 0.3;
    //         $languageTicketFare = $trip->language_ticket * $priceDifference * 0.3;
    //         $physicalTicketFare = $trip->physical_ticket * $priceDifference * 0.3;

    //         // Total fare for this trip
    //         $totalFareForTrip = $fullTicketFare + $halfTicketFare + $studentTicketFare + $languageTicketFare + $physicalTicketFare;

    //         // Append the fare details for this trip
    //         $tripFareDetails[] = [
    //             'trip_id' => $trip->id,
    //             'trip_name' => $trip->trip_name,
    //             'start_stage_name' => $trip->start_stage_name,
    //             'stop_stage_name' => $trip->stop_stage_name,
    //             'full_ticket_fare' => $fullTicketFare,
    //             'half_ticket_fare' => $halfTicketFare,
    //             'student_ticket_fare' => $studentTicketFare,
    //             'language_ticket_fare' => $languageTicketFare,
    //             'physical_ticket_fare' => $physicalTicketFare,
    //             'total_fare' => $totalFareForTrip,
    //         ];
    //     }

    //     // Prepare the response data
    //     $data = [
    //         'trips' => $tripFareDetails
    //     ];

    //     return $this->sendResponse($data, 'Trip report generated successfully.');
    // }

    public function getTripReport(Request $request)
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
            'start_trips.id as trip_id',
            'start_trips.created_at as trip_start',
            'start_trips.updated_at as trip_end'
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
                'start_date' => \Carbon\Carbon::parse($trip->trip_start)->format('d M Y'),
                'start_time' =>  Carbon::parse($trip->trip_start)->format('H:i:s'),
                'end_date' => \Carbon\Carbon::parse($trip->trip_end)->format('d M Y'),
                'end_time' => Carbon::parse($trip->trip_end)->format('H:i:s'),
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
                'formatted_created_at' => \Carbon\Carbon::parse($trip->trip_start)->format('d M Y'),
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

        return $this->sendResponse($data, 'Fare report generated successfully.');
    }



    // public function stageWiseReport(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Fetch trips along with route and stage data
    //     $trips = Trip::select(
    //             'trips.*',
    //             // 'routes.route_from',
    //             // 'routes.route_to',
    //             'stages.stage_data'
    //         )
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //         ->join('routes', 'stages.route_id', '=', 'routes.id')
    //         ->where('trips.device_id', $request->device_id)
    //         ->orderBy('trips.created_at', 'desc')
    //         ->get();

    //     // Process each trip to decode stage_data and extract start and end stage names
    //     foreach ($trips as $trip) {
    //         $stageData = json_decode($trip->stage_data, true);

    //         // Get the start and end stage names based on start_id and stop_id
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;
    //         $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

    //         $trip->total_tickets = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //         // Unset the stage_data field to avoid returning it
    //         unset($trip->stage_data,$trip->start_date,$trip->start_time,$trip->end_date,$trip->end_time);
    //     }

    //     // Return the trips with only the start and end stage names
    //     return $this->sendResponse($trips, 'Trips with stage data retrieved successfully.');
    // }
    // public function stageWiseReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         'id',
    //         'created_at',
    //         'updated_at',
    //         'trip_name'
    //     )
    //     ->where('device_id', $request->device_id)
    //     ->get();

    //     $stageReport = [];
    //     $totalPassengers = 0;
    //     $totalAmount = 0;

    //     foreach ($trips as $trip) {
    //         $bookings = Trip::where('trip_id', $trip->id)
    //             ->select(
    //                 'id',
    //                 'start_id',
    //                 'stop_id',
    //                 'full_ticket',
    //                 'half_ticket',
    //                 'physical_ticket',
    //                 'language_ticket',
    //                 'stage_id',
    //                 DB::raw('SUM(full_ticket + half_ticket + physical_ticket + language_ticket) as total_tickets'),
    //                 DB::raw('SUM(total_amount) as total_amount')
    //             )
    //             ->groupBy('id', 'start_id', 'stop_id', 'full_ticket', 'half_ticket', 'physical_ticket', 'language_ticket', 'stage_id')
    //             ->get();

    //         foreach ($bookings as $booking) {
    //             $stage = Stage::find($booking->stage_id);
    //             if ($stage) {
    //                 $stageData = json_decode($stage->stage_data, true);
    //                 if (json_last_error() === JSON_ERROR_NONE) {
    //                     $booking->start_stage_name = $stageData[$booking->start_id]['stage_name'] ?? null;
    //                     $booking->end_stage_name = $stageData[$booking->stop_id]['stage_name'] ?? null;
    //                 } else {
    //                     $booking->start_stage_name = null;
    //                     $booking->end_stage_name = null;
    //                 }
    //             } else {
    //                 $booking->start_stage_name = null;
    //                 $booking->end_stage_name = null;
    //             }
    //         }

    //         $tripTotalPassengers = $bookings->sum('total_tickets');
    //         $tripTotalAmount = $bookings->sum('total_amount');

    //         $totalPassengers += $tripTotalPassengers;
    //         $totalAmount += $tripTotalAmount;

    //         $stageReport[] = [
    //             'id' => $trip->id,
    //             'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //             'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
    //             'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
    //             'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
    //             'trips' => $bookings,
    //         ];
    //     }

    //     return $this->sendResponse([
    //         'stage_report' => $stageReport,
    //         'total_amount' => $totalAmount
    //     ], 'Trips with stage data retrieved successfully.');
    // }
    public function stageWiseReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $trips = StartTrip::select('id', 'created_at', 'updated_at', 'trip_name')
            ->where('device_id', $request->device_id)
            ->get();

        $stageReport = [];
        $totalPassengers = 0;
        $totalAmount = 0;
        $overallTotalTickets = 0;

        foreach ($trips as $trip) {
            $bookings = Trip::where('trip_id', $trip->id)
                ->select(
                    'id',
                    'start_id',
                    'stop_id',
                    'full_ticket',
                    'half_ticket',
                    'physical_ticket',
                    'language_ticket',
                    'stage_id',
                    DB::raw('SUM(full_ticket + half_ticket + physical_ticket + language_ticket) as total_tickets'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('id', 'start_id', 'stop_id', 'full_ticket', 'half_ticket', 'physical_ticket', 'language_ticket', 'stage_id')
                ->get();

            $stageTickets = [];
            $tripTotalPassengers = 0;
            $tripTotalAmount = 0;

            foreach ($bookings as $booking) {
                $stage = Stage::find($booking->stage_id);
                if ($stage) {
                    $stageData = json_decode($stage->stage_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($stageData as $stageId => $stageInfo) {
                            $existingStage = array_filter($stageTickets, function ($st) use ($stageId) {
                                return $st['id'] == (string)$stageId;
                            });

                            if (empty($existingStage)) {
                                $stageTickets[] = [
                                    'id' => (string)$stageId,
                                    'stage_name' => $stageInfo['stage_name'],
                                    'total_tickets' => 0
                                ];
                            }

                            foreach ($stageTickets as &$stageTicket) {
                                if ($stageTicket['id'] == (string)$stageId) {
                                    if ($booking->start_id == $stageId) {
                                        $stageTicket['total_tickets'] += $booking->total_tickets;
                                    }
                                }
                            }
                        }

                        $booking->start_stage_name = $stageData[$booking->start_id]['stage_name'] ?? null;
                        $booking->end_stage_name = $stageData[$booking->stop_id]['stage_name'] ?? null;
                    } else {
                        $booking->start_stage_name = null;
                        $booking->end_stage_name = null;
                    }
                } else {
                    $booking->start_stage_name = null;
                    $booking->end_stage_name = null;
                }
            }

            $tripTotalPassengers = $bookings->sum('total_tickets');
            $tripTotalAmount = $bookings->sum('total_amount');

            $totalPassengers += $tripTotalPassengers;
            $totalAmount += $tripTotalAmount;
            $overallTotalTickets += $tripTotalPassengers;

            $stageReport[] = [
                'id' => $trip->id,
                'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
                'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
                'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
                'total_tickets' => $tripTotalPassengers,
                'total_amount' => $tripTotalAmount,
                'stage_tickets' => $stageTickets
            ];
        }

        return $this->sendResponse([
            'stage_report' => $stageReport
        ], 'Trips with stage data retrieved successfully.');
    }

    // public function inspectorReport(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Fetch trips along with route and stage data
    //     $trips = Trip::select(
    //             'trips.*',
    //             // 'routes.route_from',
    //             // 'routes.route_to',
    //             'stages.stage_data'
    //         )
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //         ->join('routes', 'stages.route_id', '=', 'routes.id')
    //         ->where('trips.device_id', $request->device_id)
    //         ->orderBy('trips.created_at', 'desc')
    //         ->get();

    //     // Process each trip to decode stage_data and extract start and end stage names
    //     foreach ($trips as $trip) {
    //         $stageData = json_decode($trip->stage_data, true);

    //         // Get the start and end stage names based on start_id and stop_id
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;
    //         $trip->formatted_created_at = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

    //         $trip->total_passenger = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;
    //         $trip->dropped_passenger = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //         $trip->total_tickets = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //         // Unset the stage_data field to avoid returning it
    //         unset($trip->stage_data,$trip->start_date,$trip->start_time,$trip->end_date,$trip->end_time);
    //     }

    //     // Return the trips with only the start and end stage names
    //     return $this->sendResponse($trips, 'Trips with stage data retrieved successfully.');
    // }
    // public function inspectorReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         'start_trips.id',
    //         'start_trips.created_at',
    //         'start_trips.updated_at',
    //         'start_trips.trip_name'
    //     )
    //     ->where('start_trips.device_id', $request->device_id)
    //     ->get();

    //     $inspectorReport = [];
    //     $totalPassengers = 0;

    //     foreach ($trips as $trip) {
    //         $bookings = Trip::where('trip_id', $trip->id)
    //         ->select(
    //             'id',
    //             'start_id',
    //             'stop_id',
    //             'full_ticket',
    //             'half_ticket',
    //             'physical_ticket',
    //             'language_ticket',
    //             'stage_id',
    //             DB::raw('SUM(full_ticket + half_ticket + physical_ticket + language_ticket) as total_passengers')
    //         )
    //         ->groupBy('id', 'start_id', 'stop_id', 'full_ticket', 'half_ticket', 'physical_ticket', 'language_ticket', 'stage_id')
    //         ->get();
    //         foreach ($bookings as $booking) {
    //             $stageDataString = Stage::where('id', $booking->stage_id)->first()->stage_data;

    //             $stageData = json_decode($stageDataString, true);

    //             if (json_last_error() !== JSON_ERROR_NONE) {
    //                 $booking->start_stage_name = null;
    //                 $booking->end_stage_name = null;
    //                 continue;
    //             }

    //             $booking->start_stage_name = $stageData[$booking->start_id]['stage_name'] ?? null;
    //             $booking->end_stage_name = $stageData[$booking->stop_id]['stage_name'] ?? null;
    //         }

    //         $tripTotalPassengers = $bookings->sum('total_passengers');

    //         $totalPassengers += $tripTotalPassengers;

    //         $inspectorReport[] = [
    //             'id' => $trip->id,
    //             'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //             'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
    //             'start_time' =>  \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
    //             'end_time' =>  \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
    //             'trips' => $bookings,
    //         ];
    //     }

    //     return $this->sendResponse([
    //         'inspector_report' => $inspectorReport,
    //         'total_passengers_in' => $totalPassengers,
    //         'total_passengers_out' => $totalPassengers,
    //     ], 'Trips with stage data retrieved successfully.');
    // }
    public function inspectorReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $trips = StartTrip::select('id', 'created_at', 'updated_at', 'trip_name')
            ->where('device_id', $request->device_id)
            ->get();

        $stageReport = [];
        $totalPassengers = 0;
        $totalAmount = 0;
        $overallTotalTickets = 0;

        foreach ($trips as $trip) {
            $bookings = Trip::where('trip_id', $trip->id)
                ->select(
                    'id',
                    'start_id',
                    'stop_id',
                    'full_ticket',
                    'half_ticket',
                    'physical_ticket',
                    'language_ticket',
                    'stage_id',
                    DB::raw('SUM(full_ticket + half_ticket + physical_ticket + language_ticket) as total_tickets'),
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('id', 'start_id', 'stop_id', 'full_ticket', 'half_ticket', 'physical_ticket', 'language_ticket', 'stage_id')
                ->get();

            $stageTickets = [];
            $tripTotalPassengers = 0;
            $tripTotalAmount = 0;

            foreach ($bookings as $booking) {
                $stage = Stage::find($booking->stage_id);
                if ($stage) {
                    $stageData = json_decode($stage->stage_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($stageData as $stageId => $stageInfo) {
                            $existingStage = array_filter($stageTickets, function ($st) use ($stageId) {
                                return $st['id'] == (string)$stageId;
                            });

                            if (empty($existingStage)) {
                                $stageTickets[] = [
                                    'id' => (string)$stageId,
                                    'stage_name' => $stageInfo['stage_name'],
                                    'total_passengers' => 0,
                                    'full_ticket' => 0,
                                    'half_ticket' => 0,
                                    'student_ticket' => 0,
                                    'physical_ticket' => 0,
                                    'language_ticket' => 0
                                ];
                            }

                            foreach ($stageTickets as &$stageTicket) {
                                if ($stageTicket['id'] == (string)$stageId) {
                                    if ($booking->start_id == $stageId) {
                                        $stageTicket['total_passengers'] += $booking->total_tickets;
                                        $stageTicket['full_ticket'] += $booking->full_ticket;
                                        $stageTicket['half_ticket'] += $booking->half_ticket;
                                        $stageTicket['student_ticket'] += $booking->student_ticket;
                                        $stageTicket['physical_ticket'] += $booking->physical_ticket;
                                        $stageTicket['language_ticket'] += $booking->language_ticket;
                                    }
                                }
                            }
                        }

                        $booking->start_stage_name = $stageData[$booking->start_id]['stage_name'] ?? null;
                        $booking->end_stage_name = $stageData[$booking->stop_id]['stage_name'] ?? null;
                    } else {
                        $booking->start_stage_name = null;
                        $booking->end_stage_name = null;
                    }
                } else {
                    $booking->start_stage_name = null;
                    $booking->end_stage_name = null;
                }
            }

            $tripTotalPassengers = $bookings->sum('total_tickets');
            $tripTotalAmount = $bookings->sum('total_amount');

            $totalPassengers += $tripTotalPassengers;
            $totalAmount += $tripTotalAmount;
            $overallTotalTickets += $tripTotalPassengers;

            $stageReport[] = [
                'id' => $trip->id,
                'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
                'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
                'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
                'total_passengers_in' => $tripTotalPassengers,
                'total_passengers_out' => $tripTotalPassengers,
                'total_tickets' => $tripTotalPassengers,
                'total_amount' => $tripTotalAmount,
                'stage_tickets' => $stageTickets
            ];
        }

        return $this->sendResponse([
            'stage_report' => $stageReport
        ], 'Trips with stage data retrieved successfully.');
    }




    // public function startTrip(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|exists:trips,id',
    //         'device_id' => 'required|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trip = Trip::where('id', $request->trip_id)
    //                 ->where('device_id', $request->device_id)
    //                 ->first();

    //     if (!$trip) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Trip does not belong to this device.',
    //         ], 400);
    //     }


    //     if ($trip->status != AdminConstants::STATUS_BOOKED) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This trip has already been started or completed.',
    //         ], 400);
    //     }

    //     $trip->start_date = Carbon::now()->toDateString();
    //     $trip->start_time = Carbon::now()->toTimeString();
    //     $trip->status = AdminConstants::STATUS_STARTED;
    //     $trip->save();

    //     return $this->sendResponse($trip, 'Trip started successfully.');
    // }
    public function startTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'device_id' => 'required|exists:devices,id',
            // 'start_id' => 'required|integer',
            // 'stop_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Create a new start trip record
        $startTrip = new StartTrip([
            'route_id' => $request->route_id,
            'device_id' => $request->device_id,
            // 'start_id' => $request->start_id,
            // 'stop_id' => $request->stop_id,
            'start_trip_date' => Carbon::now(),
            'status' => AdminConstants::STATUS_ACTIVE,
        ]);

        // Generate a unique trip name
        $tripName = $this->generateUniqueTripName();
        $startTrip->trip_name = $tripName; // Assign the generated trip name
        $startTrip->save();

        $responseData = [
            'id' => (int) $startTrip->id,
            'trip_name' => (string) $startTrip->trip_name,
            'route_id' => (string) $startTrip->route_id,
            'device_id' => (string) $startTrip->device_id,
            'start_trip_date' => (string) $startTrip->start_trip_date->toDateTimeString(),
            'status' => (int) $startTrip->status,
            'created_at' => (string) $startTrip->created_at,
            'updated_at' => (string) $startTrip->updated_at,
        ];
        // dd($responseData);

        return $this->sendResponse($responseData, 'Trip started successfully.');
    }

    private function generateUniqueTripName()
    {
        // Lock the table to prevent concurrent access issues
        \DB::beginTransaction();
        try {
            $latestTrip = StartTrip::orderBy('created_at', 'desc')->first();
            $nextNumber = $latestTrip ? (int) substr($latestTrip->trip_name, 4) + 1 : 1;
            $tripName = sprintf('TRIP%04d', $nextNumber);

            // Check if the trip name already exists
            while (StartTrip::where('trip_name', $tripName)->exists()) {
                $nextNumber++;
                $tripName = sprintf('TRIP%04d', $nextNumber);
            }

            \DB::commit();
            return $tripName;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e; // Re-throw the exception for handling
        }
    }

    public function endTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:start_trips,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $startTrip = StartTrip::find($request->trip_id);

        if (!$startTrip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip not found.',
            ], 404);
        }

        if ($startTrip->status != AdminConstants::STATUS_ACTIVE) {
            return response()->json([
                'status' => false,
                'message' => 'This trip has already been ended.',
            ], 400);
        }

        $startTrip->status = AdminConstants::STATUS_INACTIVE;
        $startTrip->end_trip_date = Carbon::now();
        $startTrip->save();

        $response = [
            'id' => (int) $startTrip->id,
            'start_trip_date' => $startTrip->start_trip_date ? (string) Carbon::parse($startTrip->start_trip_date)->format('Y-m-d') : '',
            'end_trip_date' => $startTrip->end_trip_date ? (string) Carbon::parse($startTrip->end_trip_date)->format('Y-m-d') : '',
            'device_id' => (string) $startTrip->device_id,
            'route_id' => (string) $startTrip->route_id,
            'trip_name' => (string) $startTrip->trip_name,
            'status' => (int) $startTrip->status,
            'cleaner_collection' => $startTrip->cleaner_collection ?? null,
            'created_at' => (string) $startTrip->created_at,
            'updated_at' => (string) $startTrip->updated_at,
        ];

        return $this->sendResponse($response, 'Trip ended successfully.');
    }



    // public function endTrip(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|exists:trips,id',
    //         'device_id' => 'required|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }


    //     $trip = Trip::where('id', $request->trip_id)
    //                 ->where('device_id', $request->device_id)
    //                 ->first();

    //     if (!$trip) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Trip does not belong to this device.',
    //         ], 400);
    //     }

    //     if ($trip->status != AdminConstants::STATUS_STARTED) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This trip has not started yet or has already been completed.',
    //         ], 400);
    //     }

    //     $trip->end_date = Carbon::now()->toDateString();
    //     $trip->end_time = Carbon::now()->toTimeString();
    //     $trip->status = AdminConstants::STATUS_ENDED;
    //     $trip->save();

    //     return $this->sendResponse($trip, 'Trip ended successfully.');
    // }

    // public function fareReport(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Fetch trips along with stage data
    //     $trips = Trip::select(
    //         'trips.id',
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
    //         'trips.created_at',
    //         'stages.stage_data',
    //         'trips.stage_id',
    //         'trips.stop_id',
    //         'trips.start_id',
    //         'trips.route_status'
    //     )
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->get();

    //     // Initialize ticket price totals
    //     $totalFullTicketPrice = 0;
    //     $totalHalfTicketPrice = 0;
    //     $totalStudentTicketPrice = 0;
    //     $totalLanguageTicketPrice = 0;
    //     $totalPhysicalTicketPrice = 0;

    //     $tripsWithFareDetails = [];

    //     // Process each trip to calculate fare differences and extract stage names
    //     foreach ($trips as $trip) {
    //         $stageData = json_decode($trip->stage_data, true);

    //         // Get the start and end stage names
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //         $startPrice = 0;
    //         $stopPrice = 0;

    //         // Get stop price
    //         if (isset($stageData[$trip->stop_id])) {
    //             $stage = $stageData[$trip->stop_id];
    //             $prices = $stage['prices'] ?? [];
    //             if (!empty($prices)) {
    //                 $stopPrice = end($prices);
    //             }
    //         }

    //         // Get start price
    //         if (isset($stageData[$trip->start_id])) {
    //             $stage = $stageData[$trip->start_id];
    //             $prices = $stage['prices'] ?? [];
    //             if (!empty($prices)) {
    //                 $startPrice = end($prices);
    //             }
    //         }

    //         // Calculate the price difference based on route status
    //         $priceDifference = ($trip->route_status == AdminConstants::ROUTE_STATUS_UP)
    //             ? ($stopPrice - $startPrice)
    //             : ($startPrice - $stopPrice);

    //         // Calculate individual ticket prices for the trip
    //         $tripFullTicketPrice = $trip->full_ticket * $priceDifference;
    //         $tripHalfTicketPrice = $trip->half_ticket * $priceDifference * 0.5;
    //         $tripStudentTicketPrice = $trip->student_ticket * $priceDifference * 0.3;
    //         $tripLanguageTicketPrice = $trip->language_ticket * $priceDifference * 0.3;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $priceDifference * 0.3;

    //         // Update total prices
    //         $totalFullTicketPrice += $tripFullTicketPrice;
    //         $totalHalfTicketPrice += $tripHalfTicketPrice;
    //         $totalStudentTicketPrice += $tripStudentTicketPrice;
    //         $totalLanguageTicketPrice += $tripLanguageTicketPrice;
    //         $totalPhysicalTicketPrice += $tripPhysicalTicketPrice;

    //         // Add trip fare details including ticket prices
    //         $tripsWithFareDetails[] = [
    //             'id' => $trip->id,
    //             'trip_name' => $trip->trip_name,
    //             'start_date' => $trip->start_date,
    //             'start_time' => $trip->start_time,
    //             'end_date' => $trip->end_date,
    //             'end_time' => $trip->end_time,
    //             'start_stage_name' => $trip->start_stage_name,
    //             'end_stage_name' => $trip->end_stage_name,
    //             'full_ticket_price' => $tripFullTicketPrice,
    //             'half_ticket_price' => $tripHalfTicketPrice,
    //             'student_ticket_price' => $tripStudentTicketPrice,
    //             'language_ticket_price' => $tripLanguageTicketPrice,
    //             'physical_ticket_price' => $tripPhysicalTicketPrice,
    //             'total_fare' => $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice,
    //             'total_tickets' => $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket,
    //             'formatted_created_at' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //         ];
    //     }

    //     // Calculate total fare sum
    //     $sumOfTotalFare = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLanguageTicketPrice + $totalPhysicalTicketPrice;

    //     // Prepare response data
    //     $data = [
    //         'trips' => $tripsWithFareDetails,
    //         'sumOfFullTicketPrice' => $totalFullTicketPrice,
    //         'sumOfHalfTicketPrice' => $totalHalfTicketPrice,
    //         'sumOfStudentTicketPrice' => $totalStudentTicketPrice,
    //         'sumOfLanguageTicketPrice' => $totalLanguageTicketPrice,
    //         'sumOfPhysicalTicketPrice' => $totalPhysicalTicketPrice,
    //         'sumOfTotalFare' => $sumOfTotalFare,
    //     ];

    //     return $this->sendResponse($data, 'Fare report generated successfully.');
    // }
    // public function fareReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         'id',
    //         'created_at',
    //         'updated_at',
    //         'trip_name'
    //     )
    //     ->where('device_id', $request->device_id)
    //     ->get();

    //     $fareReport = [];
    //     $totalPassengers = 0;
    //     $totalAmount = 0;

    //     foreach ($trips as $trip) {
    //         $bookings = Trip::where('trip_id', $trip->id)
    //             ->select(
    //                 'id',
    //                 'start_id',
    //                 'stop_id',
    //                 'full_ticket',
    //                 'half_ticket',
    //                 'physical_ticket',
    //                 'language_ticket',
    //                 'full_ticket_price',
    //                 'half_ticket_price',
    //                 'physical_ticket_price',
    //                 'lagguage_ticket_price',
    //                 'student_ticket',
    //                 'student_ticket_price',
    //                 'stage_id',
    //                 DB::raw('SUM(full_ticket * full_ticket_price) as total_full_ticket_price'),
    //                 DB::raw('SUM(half_ticket * half_ticket_price) as total_half_ticket_price'),
    //                 DB::raw('SUM(physical_ticket * physical_ticket_price) as total_physical_ticket_price'),
    //                 DB::raw('SUM(student_ticket * student_ticket_price) as total_student_ticket_price'),
    //                 DB::raw('SUM(full_ticket + half_ticket + physical_ticket + language_ticket + student_ticket) as total_tickets'),
    //                 DB::raw('SUM(COALESCE(full_ticket * full_ticket_price, 0) +
    //                 COALESCE(half_ticket * half_ticket_price, 0) +
    //                 COALESCE(physical_ticket * physical_ticket_price, 0) +
    //                 COALESCE(lagguage_ticket_price, 0)) as total_amount')
    //             )
    //             ->groupBy('id', 'start_id', 'stop_id', 'full_ticket', 'half_ticket', 'physical_ticket', 'language_ticket', 'stage_id','full_ticket_price','half_ticket_price','physical_ticket_price','lagguage_ticket_price','student_ticket','student_ticket_price')
    //             ->get();

    //         $tripTotalPassengers = $bookings->sum('total_tickets');
    //         $tripTotalAmount = $bookings->sum('total_amount');

    //         $totalPassengers += $tripTotalPassengers;
    //         $totalAmount += $tripTotalAmount;

    //         $fareReport[] = [
    //             'id' => $trip->id,
    //             'trip_name' => $trip->trip_name,
    //             'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //             'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
    //             'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
    //             'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
    //             'trips' => $bookings,
    //         ];
    //     }

    //     return $this->sendResponse([
    //         'fare_report' => $fareReport,
    //         'total_amount' => $totalAmount,
    //     ], 'Trips with stage data retrieved successfully.');
    // }
    public function fareReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $trips = StartTrip::select('id', 'created_at', 'updated_at', 'trip_name')
            ->where('device_id', $request->device_id)
            ->get();

        $stageReport = [];

        foreach ($trips as $trip) {
            $bookings = Trip::where('trip_id', $trip->id)
                ->select(
                    'id',
                    'start_id',
                    'stage_id',
                    'stop_id',
                    'full_ticket_price',
                    'half_ticket_price',
                    'physical_ticket_price',
                    'lagguage_ticket_price',
                    'student_ticket_price',
                    'full_ticket',
                    'half_ticket',
                    'student_ticket',
                    'language_ticket',
                    'physical_ticket',
                    DB::raw('SUM(total_amount) as total_amount')
                )
                ->groupBy('id', 'start_id', 'stop_id', 'full_ticket_price', 'half_ticket_price', 'physical_ticket_price', 'lagguage_ticket_price', 'student_ticket_price', 'stage_id', 'full_ticket', 'half_ticket', 'student_ticket', 'language_ticket', 'physical_ticket')
                ->get();
            $stageTickets = [];
            $tripTotalAmount = 0;

            foreach ($bookings as $booking) {
                $stage = Stage::find($booking->stage_id);
                if ($stage) {
                    $stageData = json_decode($stage->stage_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($stageData as $stageId => $stageInfo) {
                            // Check if the stage already exists in the stageTickets array
                            $existingStage = array_filter($stageTickets, function ($st) use ($stageId) {
                                return $st['id'] == (string)$stageId;
                            });

                            if (empty($existingStage)) {
                                $stageTickets[] = [
                                    'id' => (string)$stageId,
                                    'total_tickets' => 0,
                                    'end_price' => end($stageInfo['prices']),
                                    'total_price' => 0, // Initialize total price
                                ];
                            }

                            // Loop through stage tickets and calculate ticket totals
                            foreach ($stageTickets as &$stageTicket) {
                                $ticketPriceTypes = [
                                    $booking->full_ticket_price,
                                    $booking->half_ticket_price,
                                    $booking->physical_ticket_price,
                                    $booking->lagguage_ticket_price,
                                    $booking->student_ticket_price,
                                ];

                                foreach ($ticketPriceTypes as $priceValue) {
                                    if (abs($stageTicket['end_price'] - $priceValue) < 0.01) {
                                        $stageTicket['total_tickets'] += 1;
                                        $stageTicket['total_price'] = $stageTicket['total_tickets'] * $stageTicket['end_price']; // Calculate total price
                                        break; // Exit the loop once a match is found
                                    }
                                }
                            }
                            $totalStagePriceSum = array_sum(array_column($stageTickets, 'total_price'));
                            $totalStageTickets = array_sum(array_column($stageTickets, 'total_tickets'));
                        }
                    }
                }
            }

            $tripTotalAmount = $bookings->sum('total_amount');

            // Filter out trips with no stage tickets
            if (count($stageTickets) > 0) {
                $stageReport[] = [
                    'id' => $trip->id,
                    'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
                    'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
                    'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
                    'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
                    'stage_tickets' => $stageTickets,
                    'total_stage_price_sum' => $totalStagePriceSum,
                    'total_tickets' => $totalStageTickets
                ];
            }
        }

        return $this->sendResponse([
            'fare_report' => $stageReport,
        ], 'Trips with stage data retrieved successfully.');
    }
    // public function fareReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartDay::select('id', 'created_at', 'updated_at')
    //         ->where('device_id', $request->device_id)
    //         ->orderBy('created_at','desc')
    //         ->get();

    //     $stageReport = [];

    //     foreach ($trips as $trip) {
    //         $bookings = Trip::where('start_day_id', $trip->id)
    //             ->select(
    //                 'id',
    //                 'start_id',
    //                 'stage_id',
    //                 'stop_id',
    //                 'full_ticket_price',
    //                 'half_ticket_price',
    //                 'physical_ticket_price',
    //                 'lagguage_ticket_price',
    //                 'student_ticket_price',
    //                 'full_ticket',
    //                 'half_ticket',
    //                 'student_ticket',
    //                 'language_ticket',
    //                 'physical_ticket',
    //                 DB::raw('SUM(total_amount) as total_amount')
    //             )
    //             ->groupBy('id', 'start_id', 'stop_id', 'full_ticket_price', 'half_ticket_price', 'physical_ticket_price', 'lagguage_ticket_price', 'student_ticket_price','stage_id','full_ticket','half_ticket','student_ticket','language_ticket','physical_ticket')
    //             ->get();
    //         $stageTickets = [];
    //         $tripTotalAmount = 0;

    //         foreach ($bookings as $booking) {
    //             $stage = Stage::find($booking->stage_id);
    //             if ($stage) {
    //                 $stageData = json_decode($stage->stage_data, true);
    //                 if (json_last_error() === JSON_ERROR_NONE) {
    //                     foreach ($stageData as $stageId => $stageInfo) {
    //                         // Check if the stage already exists in the stageTickets array
    //                         $existingStage = array_filter($stageTickets, function ($st) use ($stageId) {
    //                             return $st['id'] == (string)$stageId;
    //                         });

    //                         if (empty($existingStage)) {
    //                             $stageTickets[] = [
    //                                 'id' => (string)$stageId,
    //                                 'total_tickets' => 0,
    //                                 'end_price' => end($stageInfo['prices']),
    //                                 'total_price' => 0, // Initialize total price
    //                             ];
    //                         }

    //                         // Loop through stage tickets and calculate ticket totals
    //                         foreach ($stageTickets as &$stageTicket) {
    //                             $ticketPriceTypes = [
    //                                 $booking->full_ticket_price,
    //                                 $booking->half_ticket_price,
    //                                 $booking->physical_ticket_price,
    //                                 $booking->lagguage_ticket_price,
    //                                 $booking->student_ticket_price,
    //                             ];

    //                             foreach ($ticketPriceTypes as $priceValue) {
    //                                 if (abs($stageTicket['end_price'] - $priceValue) < 0.01) {
    //                                     $stageTicket['total_tickets'] += 1;
    //                                     $stageTicket['total_price'] = $stageTicket['total_tickets'] * $stageTicket['end_price']; // Calculate total price
    //                                     break; // Exit the loop once a match is found
    //                                 }
    //                             }
    //                         }
    //                         $totalStagePriceSum = array_sum(array_column($stageTickets, 'total_price'));
    //                         $totalStageTickets = array_sum(array_column($stageTickets, 'total_tickets'));
    //                     }
    //                 }
    //             }
    //         }

    //         $tripTotalAmount = $bookings->sum('total_amount');

    //         // Filter out trips with no stage tickets
    //         if (count($stageTickets) > 0) {
    //             $stageReport[] = [
    //                 'id' => $trip->id,
    //                 'start_date' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //                 'end_date' => \Carbon\Carbon::parse($trip->updated_at)->format('d M Y'),
    //                 'start_time' => \Carbon\Carbon::parse($trip->created_at)->format('H:i'),
    //                 'end_time' => \Carbon\Carbon::parse($trip->updated_at)->format('H:i'),
    //                 'stage_tickets' => $stageTickets,
    //                 'total_stage_price_sum' => $totalStagePriceSum,
    //                 'total_tickets' => $totalStageTickets
    //             ];
    //         }
    //     }

    //     return $this->sendResponse([
    //         'fare_report' => $stageReport,
    //     ], 'Trips with stage data retrieved successfully.');
    // }


    public function startDay(Request $request)
    {
        $startDay = StartDay::create([
            'start_time' => now(),
        ]);

        return $this->sendResponse($startDay, 'Successfully started the day');
    }

    public function endDay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_day_id' => 'required|integer|exists:start_days,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $startDay = StartDay::find($request->start_day_id);

        if (!$startDay) {
            return response()->json(['message' => 'Start day not found'], 404);
        }

        $startDay->end_time = now();
        $startDay->save();

        return $this->sendResponse($startDay, 'Successfully ended the day');
    }

    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         DB::raw('COUNT(trips.id) as total_trips'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket_price',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket_price',
    //         'trips.half_ticket',
    //         'trips.student_ticket',
    //         'trips.language_ticket',
    //         'trips.physical_ticket',
    //         'stages.stage_data',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         DB::raw('MIN(start_days.id) as start_day_id'),
    //         DB::raw('MIN(stages.id) as stage_id')
    //     )
    //         ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //         ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //         ->where('trips.device_id', $request->device_id)
    //         ->groupBy(
    //             DB::raw('DATE(start_days.start_time)'),
    //             'start_trip_id',
    //             'trip_id',
    //             'trips.start_id',
    //             'trips.stop_id',
    //             'trips.full_ticket',
    //             'trips.half_ticket',
    //             'trips.student_ticket',
    //             'trips.language_ticket',
    //             'trips.physical_ticket',
    //             'stages.stage_data',
    //             'trips.route_status',
    //             'trips.id',
    //             'trips.full_ticket_price',
    //             'trips.half_ticket_price',
    //             'trips.student_ticket_price',
    //             'trips.lagguage_ticket_price',
    //             'trips.physical_ticket_price',
    //             DB::raw('DATE(start_days.end_time)'),
    //             'start_days.start_time',
    //             'start_days.end_time',
    //             'start_trips.trip_name'
    //         )
    //         ->get();

    //     // Initialize counters for daily totals
    //     $dailyTotals = [];
    //     $tripAggregation = [];

    //     foreach ($trips as $trip) {
    //         // Ensure start_time and end_time are Carbon instances
    //         $trip->start_time = \Carbon\Carbon::parse($trip->start_time);
    //         $trip->end_time = \Carbon\Carbon::parse($trip->end_time);

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice = $trip->language_ticket * $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         $date = $trip->start_time->format('d M Y');
    //         $endDate = $trip->end_time->format('d M Y');
    //         $startTime = $trip->start_time->format('H:i');
    //         $endTime = $trip->end_time->format('H:i');

    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => []
    //             ];
    //         }

    //         // Aggregate daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

    //         $dailyTotals[$date]['total_tickets'] += $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //         // Update total prices and expenses
    //         $dailyTotals[$date]['total_price'] += $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;
    //         $dailyTotals[$date]['total_expense'] += 0; // Assuming expense equals total price if expense data is not available
    //         $dailyTotals[$date]['net_total'] += $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 per trip entry

    //         // Aggregate trip-wise data by trip_id
    //         if (!isset($tripAggregation[$trip->trip_id])) {
    //             $tripAggregation[$trip->trip_id] = [
    //                 'trip_name' => $trip->trip_name,
    //                 'trip_total_tickets' => 0,
    //                 'trip_total_price' => 0
    //             ];
    //         }

    //         // Sum up the total tickets and prices for the same trip_id
    //         $tripAggregation[$trip->trip_id]['trip_total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    //         $tripAggregation[$trip->trip_id]['trip_total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
    //     }

    //     // Format the data
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => $date,
    //             'end_date' => $endDate,
    //             'start_time' => $startTime,
    //             'end_time' => $endTime,
    //             'full_ticket_price' => $data['full_ticket_price'],
    //             'half_ticket_price' => $data['half_ticket_price'],
    //             'student_ticket_price' => $data['student_ticket_price'],
    //             'language_ticket_price' => $data['language_ticket_price'],
    //             'physical_ticket_price' => $data['physical_ticket_price'],
    //             'total_full_ticket_count' => $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => $data['total_physical_ticket_count'],
    //             'total_tickets' => $data['total_tickets'],
    //             'total_trips' => $data['total_trips'],
    //             'total_price' => $data['total_price'],
    //             'total_expense' => $data['total_expense'],
    //             'net_total' => $data['net_total'],
    //             'trips' => array_values($tripAggregation)
    //         ];
    //     }

    //     return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }
    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //         // 'trips.trip_name as ticket_name'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->orderBy('start_days.start_time','desc')
    //     // ->whereDate('start_trips.created_at', '2024-10-23')
    //     ->get();
    //     // dd($trips);
    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //         $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
    //     ->select('total_expense')
    //     ->first();

    //     // dd($totalExpense);
    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice =  $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

    //         $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;
    //         $dailyTotals[$date]['total_expense'] += $totalExpense->total_expense ?? 0;

    //         $dailyTotals[$date]['start_date'] = date('d M Y', strtotime($trip->start_time));
    //         $dailyTotals[$date]['end_date'] = date('d M Y', strtotime($trip->end_time));
    //         $dailyTotals[$date]['start_time'] = date('H:i', strtotime($trip->start_time));
    //         $dailyTotals[$date]['end_time'] = date('H:i', strtotime($trip->end_time));

    //       // Assuming $trip->start_time and $trip->end_time are strings
    // $date = date('d M Y', strtotime($trip->start_time));
    // $endDate = date('d M Y', strtotime($trip->end_time));
    // $startTime = date('H:i', strtotime($trip->start_time));
    // $endTime = date('H:i', strtotime($trip->end_time));


    //       // Total tickets and prices
    // $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    // $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
    // // $dailyTotals[$date]['total_expense'] += $totalExpense ? $totalExpense->total_expense : 0;

    // // Initialize total for net total calculation
    // $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    // // Add cleaner_collection only once if it hasn't been added
    // if (!isset($dailyTotals[$date]['hasAddedCleanerCollection'])) {
    //     $total += $trip->cleaner_collection;
    //     $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    // }

    // if (!isset($dailyTotals[$date]['hasSubtractedTotalExpense'])) {
    //     $total -= $totalExpense ? $totalExpense->amount : 0;
    //     $dailyTotals[$date]['hasSubtractedTotalExpense'] = true;
    // }


    // // Update the net total
    // $dailyTotals[$date]['net_total'] += $total;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => (string) $trip->start_trip_id,
    //             // 'ticket_name' =>  $trip->ticket_name,
    //             'trip_name' => (string) $trip->trip_name,
    //             'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => (int) $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //     if ($existingTripKey !== false) {
    //         // If it exists, update the total tickets and price
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //     } else {
    //         // If it doesn't exist, add the trip data
    //         $dailyTotals[$date]['trips'][] = $tripData;
    //     }
    //     }

    //     // dd($dailyTotals);

    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => (string) $data['start_date'],
    //             'end_date' => (string) $data['end_date'],
    //             'start_time' => (string) $data['start_time'],
    //             'end_time' => (string) $data['end_time'],
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) $data['cleaner_collection'],
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int)   $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //     return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }
    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->orderBy('start_days.start_time', 'desc')
    //     ->whereDate('start_trips.created_at', '2024-10-23')
    //     ->get();

    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //         $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
    //             ->select('total_expense')
    //             ->first();

    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //                 'hasSubtractedTotalExpense' => false, // Track if expense has been subtracted
    //                 'hasAddedCleanerCollection' => false, // Track if cleaner collection has been added
    //                 'hasAddedTotalExpense' => false, // Track if total expense has been added
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice = $trip->language_ticket * $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

    //         // Total tickets and prices
    //         $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);

    //         // Calculate total price and initialize net total
    //         $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    //         // Add cleaner_collection only once if it hasn't been added
    //         if (!$dailyTotals[$date]['hasAddedCleanerCollection']) {
    //             $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;
    //             $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    //         }

    //         // Subtract total expense only once for the day
    //         if (!$dailyTotals[$date]['hasAddedTotalExpense']) {
    //             $dailyTotals[$date]['total_expense'] += $totalExpense->total_expense ?? 0;
    //             $dailyTotals[$date]['hasAddedTotalExpense'] = true;
    //         }

    //         // Calculate net total
    //         $dailyTotals[$date]['net_total'] += ($total + $dailyTotals[$date]['cleaner_collection'] - $dailyTotals[$date]['total_expense']);

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => (string) $trip->start_trip_id,
    //             'trip_name' => (string) $trip->trip_name,
    //             'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => (int) $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //         if ($existingTripKey !== false) {
    //             // If it exists, update the total tickets and price
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //         } else {
    //             // If it doesn't exist, add the trip data
    //             $dailyTotals[$date]['trips'][] = $tripData;
    //         }
    //     }

    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => $date,
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) $data['cleaner_collection'],
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int) $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //     return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }
    public function previousCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $trips = StartTrip::select(
            DB::raw('DATE(start_days.start_time) as trip_date'),
            'start_trips.id as start_trip_id',
            'trips.id',
            'trips.trip_id as trip_id',
            'trips.start_id',
            'trips.stop_id',
            'trips.full_ticket',
            'trips.full_ticket_price',
            'trips.half_ticket',
            'trips.half_ticket_price',
            'trips.student_ticket',
            'trips.student_ticket_price',
            'trips.language_ticket',
            'trips.lagguage_ticket_price',
            'trips.physical_ticket',
            'trips.physical_ticket_price',
            'stages.stage_data',
            'start_trips.created_at',
            'start_days.start_time',
            'start_days.end_time',
            'start_days.created_at as start_date',
            'trips.route_status',
            'start_trips.trip_name',
            'start_trips.cleaner_collection'
        )
            ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
            ->join('stages', 'trips.stage_id', '=', 'stages.id')
            ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
            ->where('trips.device_id', $request->device_id)
            ->orderBy('start_days.start_time', 'desc')
            // ->whereDate('start_trips.created_at', '2024-10-21')
            ->get();

        // Initialize daily totals
        $dailyTotals = [];

        // Array to track which trip IDs have already had expenses added
        $addedExpenseTripIds = [];

        $addedCleanerCollectionTripIds = [];

        foreach ($trips as $trip) {
            $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
                ->select('total_expense')
                ->first();

            // Format trip start_time to date
            $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

            // Initialize daily totals for the date if not set
            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [
                    'full_ticket_price' => 0,
                    'half_ticket_price' => 0,
                    'student_ticket_price' => 0,
                    'language_ticket_price' => 0,
                    'physical_ticket_price' => 0,
                    'total_full_ticket_count' => 0,
                    'total_half_ticket_count' => 0,
                    'total_student_ticket_count' => 0,
                    'total_language_ticket_count' => 0,
                    'total_physical_ticket_count' => 0,
                    'cleaner_collection' => 0,
                    'total_tickets' => 0,
                    'total_trips' => 0,
                    'total_price' => 0,
                    'total_expense' => 0,
                    'net_total' => 0,
                    'trips' => [],
                    'hasAddedCleanerCollection' => false, // Track if expense has been subtracted
                ];
            }

            // Calculate ticket prices
            $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
            $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
            $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
            $tripLanguageTicketPrice = $trip->language_ticket * $trip->lagguage_ticket_price;
            $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

            // Aggregate the daily totals
            $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
            $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
            $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
            $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
            $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

            // Count tickets
            $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
            $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
            $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
            $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
            $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

            $dailyTotals[$date]['start_date'] = date('d M Y', strtotime($trip->start_time));
            $dailyTotals[$date]['end_date'] = date('d M Y', strtotime($trip->end_time));
            $dailyTotals[$date]['start_time'] = date('H:i', strtotime($trip->start_time));
            $dailyTotals[$date]['end_time'] = date('H:i', strtotime($trip->end_time));

            // $date = date('d M Y', strtotime($trip->start_time));
            // $endDate = date('d M Y', strtotime($trip->end_time));
            // $startTime = date('H:i', strtotime($trip->start_time));
            // $endTime = date('H:i', strtotime($trip->end_time));

            // Total tickets and prices

            $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);

            // Calculate total price and initialize net total
            $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

            if (!in_array($trip->start_trip_id, $addedCleanerCollectionTripIds)) {
                $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;
                $addedCleanerCollectionTripIds[] = $trip->start_trip_id; // Mark this trip ID as processed
            }


            // Add expense for this specific trip ID only once
            if (!in_array($trip->start_trip_id, $addedExpenseTripIds)) {
                $dailyTotals[$date]['total_expense'] += $totalExpense->total_expense ?? 0;
                $addedExpenseTripIds[] = $trip->start_trip_id; // Mark this trip ID as processed
            }
            $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
            // Calculate net total
            // $dailyTotals[$date]['net_total'] += ($total + $dailyTotals[$date]['cleaner_collection'] - $dailyTotals[$date]['total_expense']);
            $dailyTotals[$date]['net_total'] = $dailyTotals[$date]['total_price'] + $dailyTotals[$date]['cleaner_collection'] - $dailyTotals[$date]['total_expense'];

            // Increment total trips for the day
            $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

            // Collect trip data for the day
            $tripData = [
                'id' => (string) $trip->start_trip_id,
                'trip_name' => (string) $trip->trip_name,
                'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
                'trip_total_price' => (int) $total,
            ];

            $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

            if ($existingTripKey !== false) {
                // If it exists, update the total tickets and price
                $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
                $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
            } else {
                // If it doesn't exist, add the trip data
                $dailyTotals[$date]['trips'][] = $tripData;
            }
        }

        // Format the final response
        $formattedData = [];
        foreach ($dailyTotals as $date => $data) {
            $formattedData[] = [
                'date' => $date,
                'end_date' => (string) $data['end_date'],
                'start_time' => (string) $data['start_time'],
                'end_time' => (string) $data['end_time'],
                'full_ticket_price' => (int) $data['full_ticket_price'],
                'cleaner_collection' => (int) $data['cleaner_collection'],
                'half_ticket_price' => (int) $data['half_ticket_price'],
                'student_ticket_price' => (int) $data['student_ticket_price'],
                'language_ticket_price' => (int) $data['language_ticket_price'],
                'physical_ticket_price' => (int) $data['physical_ticket_price'],
                'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
                'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
                'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
                'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
                'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
                'total_tickets' => (int) $data['total_tickets'],
                'total_trips' => (int) $data['total_trips'],
                'total_price' => (int) $data['total_price'] + $data['cleaner_collection'],
                'total_expense' => (int) $data['total_expense'],
                'net_total' => (int) $data['net_total'],
                'trips' => $data['trips'],
            ];
        }

        return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    }


    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->orderBy('start_days.start_time','desc')
    //     ->whereDate('start_trips.created_at', '2024-10-23')
    //     ->get();

    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //         $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
    //             ->select('total_expense')
    //             ->first();

    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //                 'hasSubtractedTotalExpense' => false, // Track if expense has been subtracted
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice = $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

    //         $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;

    //         // Total tickets and prices
    //         $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    //         $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);

    //         // Initialize total for net total calculation
    //         $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    //         // Add cleaner_collection only once if it hasn't been added
    //         if (!isset($dailyTotals[$date]['hasAddedCleanerCollection'])) {
    //             $total += $trip->cleaner_collection;
    //             $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    //         }

    //         // Subtract total expense once for the day
    //         if (!$dailyTotals[$date]['hasSubtractedTotalExpense']) {
    //             $total -= $totalExpense ? $totalExpense->total_expense : 0;
    //             $dailyTotals[$date]['hasSubtractedTotalExpense'] = true;
    //         }

    //         // Update the net total
    //         $dailyTotals[$date]['net_total'] += $total;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => (string) $trip->start_trip_id,
    //             'trip_name' => (string) $trip->trip_name,
    //             'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => (int) $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //         if ($existingTripKey !== false) {
    //             // If it exists, update the total tickets and price
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //         } else {
    //             // If it doesn't exist, add the trip data
    //             $dailyTotals[$date]['trips'][] = $tripData;
    //         }
    //     }

    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => (string) $data['start_date'],
    //             'end_date' => (string) $data['end_date'],
    //             'start_time' => (string) $data['start_time'],
    //             'end_time' => (string) $data['end_time'],
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) $data['cleaner_collection'],
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int) $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //      return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }
    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->orderBy('start_days.start_time', 'desc')
    //     ->whereDate('start_trips.created_at', '2024-10-21')
    //     ->get();

    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //         $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
    //             ->select('total_expense')
    //             ->first();

    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => $totalExpense ? $totalExpense->total_expense : 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //                 'hasSubtractedTotalExpense' => false, // Track if expense has been subtracted
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice = $trip->language_ticket * $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

    //         $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;

    //         // Total tickets and prices
    //         $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    //         $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);

    //         // Initialize total for net total calculation
    //         $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    //         // Add cleaner_collection only once if it hasn't been added
    //         if (!isset($dailyTotals[$date]['hasAddedCleanerCollection'])) {
    //             $total += $trip->cleaner_collection;
    //             $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    //         }

    //         // Subtract total expense once for the day
    //         if (!$dailyTotals[$date]['hasSubtractedTotalExpense']) {
    //             $total -= $dailyTotals[$date]['total_expense'];
    //             $dailyTotals[$date]['hasSubtractedTotalExpense'] = true;
    //         }

    //         // Update the net total
    //         $dailyTotals[$date]['net_total'] += $total;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => (string) $trip->start_trip_id,
    //             'trip_name' => (string) $trip->trip_name,
    //             'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => (int) $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //         if ($existingTripKey !== false) {
    //             // If it exists, update the total tickets and price
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //             $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //         } else {
    //             // If it doesn't exist, add the trip data
    //             $dailyTotals[$date]['trips'][] = $tripData;
    //         }
    //     }

    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => $date,
    //             // 'end_date' => (string) $data['end_time'],
    //             // 'start_time' => (string) $data['start_time'],
    //             // 'end_time' => (string) $data['end_time'],
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) $data['cleaner_collection'],
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int) $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //     return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }


    // public function previousCollection(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //         // 'trips.trip_name as ticket_name'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $request->device_id)
    //     ->orderBy('start_days.start_time','desc')
    //     ->get();

    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //          $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
    //                         ->select('total_expense')
    //                         ->first();
    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice =  $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;
    //         $dailyTotals[$date]['cleaner_collection'] =+ $trip->cleaner_collection ?? 0;
    //         $dailyTotals[$date]['total_expense'] += $totalExpense ? $totalExpense->total_expense : 0;

    //       // Assuming $trip->start_time and $trip->end_time are strings
    // $date = date('d M Y', strtotime($trip->start_time));
    // $endDate = date('d M Y', strtotime($trip->end_time));
    // $startTime = date('H:i', strtotime($trip->start_time));
    // $endTime = date('H:i', strtotime($trip->end_time));


    //       // Total tickets and prices
    // $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    // $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
    // // $dailyTotals[$date]['total_expense'] += $totalExpense ? $totalExpense->total_expense : 0;
    // if (!isset($dailyTotals[$date]['total_expense'])) {
    //     $dailyTotals[$date]['total_expense'] = 0;
    // }


    //     // $dailyTotals[$date]['total_expense'] += $totalExpense ? $totalExpense->total_expense : 0;


    // // Initialize total for net total calculation
    // $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    // // Add cleaner_collection only once if it hasn't been added
    // if (!isset($dailyTotals[$date]['hasAddedCleanerCollection'])) {
    //     $total += $trip->cleaner_collection;
    //     $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    // }

    // if (!isset($dailyTotals[$date]['hasSubtractedTotalExpense'])) {
    //     $total -= $totalExpense ? $totalExpense->amount : 0;
    //     $dailyTotals[$date]['hasSubtractedTotalExpense'] = true;
    // }


    // // Update the net total
    // $dailyTotals[$date]['net_total'] += $total;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => $trip->start_trip_id,
    //             // 'ticket_name' =>  $trip->ticket_name,
    //             'trip_name' => $trip->trip_name,
    //             'trip_total_tickets' => ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //     if ($existingTripKey !== false) {
    //         // If it exists, update the total tickets and price
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //     } else {
    //         // If it doesn't exist, add the trip data
    //         $dailyTotals[$date]['trips'][] = $tripData;
    //     }
    //     }



    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => (string) $data['start_date'],
    //             'end_date' => (string) $data['end_date'],
    //             'start_time' => (string) $data['start_time'],
    //             'end_time' => (string) $data['end_time'],
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) $data['cleaner_collection'],
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int)   $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //     return $this->sendResponse($formattedData, 'Trips retrieved successfully.');
    // }



    private function getStagePrice($stageData, $stageId)
    {
        $price = 0;
        if (isset($stageData[$stageId])) {
            $prices = $stageData[$stageId]['prices'] ?? [];
            if (!empty($prices)) {
                $price = end($prices);
            }
        }
        return $price;
    }


    //  public function manageTrip(Request $request)
    //     {
    //         $validator = Validator::make($request->all(), [
    //             'device_id' => 'required|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->sendError('Invalid data', $validator->errors()->toArray());
    //         }

    //         $activeTrip = StartDay::where('device_id', $request->device_id)
    //             ->whereNull('end_time')
    //             ->first();

    //         if ($activeTrip) {
    //             $activeTrip->end_time = now();
    //             $activeTrip->is_active =  AdminConstants::STATUS_INACTIVE;
    //             $activeTrip->save();

    //             return $this->sendResponse($activeTrip, 'Successfully ended the trip');
    //         } else {
    //             $trip = StartDay::create([
    //                 'device_id' => $request->device_id,
    //                 'start_time' => now(),
    //                 'is_active' => AdminConstants::STATUS_ACTIVE,
    //             ]);

    //             return $this->sendResponse($trip, 'Successfully started a new trip');
    //         }
    //     }
    public function manageTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $activeTrip = StartDay::where('device_id', $request->device_id)
            ->whereNull('end_time')
            ->first();

        if ($activeTrip) {
            // Ending the active trip
            $activeTrip->end_time = now();
            $activeTrip->is_active = AdminConstants::STATUS_INACTIVE;
            $activeTrip->save();

            // Refresh to ensure updated values
            $activeTrip->refresh();

            $responseData = [
                'id' => (int) $activeTrip->id,
                'device_id' => (int) $activeTrip->device_id,
                'start_time' => $activeTrip->start_time ? (string) Carbon::parse($activeTrip->start_time)->toDateTimeString() : '',
                'end_time' => $activeTrip->end_time ? (string) Carbon::parse($activeTrip->end_time)->toDateTimeString() : '',
                'is_active' => (int) $activeTrip->is_active,
                'created_at' => $activeTrip->created_at,
                'updated_at' => $activeTrip->updated_at
            ];

            return $this->sendResponse($responseData, 'Successfully ended the day');
        } else {
            // Starting a new trip
            $trip = StartDay::create([
                'device_id' => $request->device_id,
                'start_time' => now(),
                'is_active' => AdminConstants::STATUS_ACTIVE,
            ]);

            // Ensure the newly created trip has all attributes set correctly
            $trip->refresh();

            $responseData = [
                'id' => (int) $trip->id,
                'device_id' => (int) $trip->device_id,
                'start_time' => $trip->start_time ? (string) Carbon::parse($trip->start_time)->toDateTimeString() : '',
                'end_time' => $trip->end_time ? (string) Carbon::parse($trip->end_time)->toDateTimeString() : '',
                'is_active' => (int) $trip->is_active,
                'created_at' => $trip->created_at,
                'updated_at' => $trip->updated_at
            ];

            return $this->sendResponse($responseData, 'Successfully started a new day');
        }
    }


    // public function getCollectionReport(Request $request)
    //     {
    //         $validator = Validator::make($request->all(), [
    //             'device_id' => 'required|string|exists:devices,id',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->sendError('Invalid data', $validator->errors()->toArray());
    //         }

    //         $collectionReport = Trip::select(
    //             'trips.id',
    //             'trips.start_id',
    //             'trips.stop_id',
    //             'start_trips.trip_name',
    //             'trips.created_at',
    //             'stages.stage_data',
    //             'start_days.start_time',
    //             'start_days.end_time'
    //         )
    //             ->join('start_trips', 'start_trips.id', '=', 'trips.trip_id')
    //             ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //             ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //             ->where('trips.device_id', $request->device_id)
    //             ->get();

    //         $tripsGroupedByDate = [];

    //         foreach ($collectionReport as $trip) {
    //             $stageData = json_decode($trip->stage_data, true);

    //             $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //             $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //             $formattedDate = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

    //             $tripsGroupedByDate[$formattedDate][] = [
    //                 'id' => $trip->id,
    //                 'trip_name' => $trip->trip_name,
    //                 'start_date' => $trip->start_time,
    //                 'end_date' => $trip->end_time,
    //                 'start_stage_name' => $trip->start_stage_name,
    //                 'end_stage_name' => $trip->end_stage_name,
    //                 'formatted_created_at' => $formattedDate,
    //             ];
    //         }

    //         $tripsWithDetails = array_values($tripsGroupedByDate);

    //         return $this->sendResponse($tripsWithDetails, 'Collection report generated successfully.');
    //     }

    public function getCollectionReport(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,id',
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Fetch the collection report with proper joins
        $collectionReport = StartTrip::select(
            'start_trips.id',
            'start_trips.trip_name',
            'start_trips.created_at',
            'start_days.start_time',
            'start_days.end_time',
            'start_trips.id as trip_id',
            'routes.route_from',
            'routes.route_to',
            'trips.stage_id'
        )
            ->leftJoin('trips', 'trips.trip_id', '=', 'start_trips.id')
            ->leftJoin('routes', 'routes.id', '=', 'start_trips.route_id')
            ->leftJoin('start_days', 'trips.start_day_id', '=', 'start_days.id')
            ->leftJoin('stages', 'trips.stage_id', '=', 'stages.id')
            ->where('start_trips.device_id', $request->device_id)
            ->orderBy('start_trips.created_at', 'desc')
            ->distinct('start_trips.id')
            ->get();

        // Group trips by the created date
        $tripsGroupedByDate = [];

        foreach ($collectionReport as $trip) {
            // Optionally decode stage data if necessary
            $stageData = json_decode($trip->stage_data, true);
            $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
            $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

            $formattedDate = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

            // Populate the grouped array
            $tripsGroupedByDate[$formattedDate][] = [
                'id' => (int) $trip->trip_id,
                'trip_name' => (string) $trip->trip_name,
                'start_date' => (string) $trip->start_time,
                'end_date' => (string) $trip->end_time,
                'start_stage_name' => (string) $trip->route_from,
                'end_stage_name' => (string) $trip->route_to,
                'formatted_created_at' => (string) $formattedDate,
            ];
        }

        // Re-index the array for output
        $tripsWithDetails = array_values($tripsGroupedByDate);

        // Return the response
        return $this->sendResponse($tripsWithDetails, 'Collection report generated successfully.');
    }
    // public function getCollectionReport(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $collectionReport = StartTrip::select(
    //             'start_trips.id',
    //             'start_trips.trip_name',
    //             'start_trips.created_at',
    //             'start_trips.updated_at',
    //             'start_trips.id as trip_id',
    //             'routes.route_from',
    //             'routes.route_to'
    //         )
    //         ->join('routes', 'routes.id', '=', 'start_trips.route_id')
    //         ->where('start_trips.device_id', $request->device_id)
    //         ->orderBy('start_trips.created_at','desc')
    //         ->get();

    //     $tripsGroupedByDate = [];

    //     foreach ($collectionReport as $trip) {
    //         $formattedDate = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');

    //         $tripsGroupedByDate [] = [
    //             'id' => $trip->id,
    //             'trip_name' => $trip->trip_name,
    //             'start_date' => $trip->created_at,
    //             'end_date' => $trip->updated_at,
    //             'start_stage_name' => $trip->route_from,
    //             'end_stage_name' => $trip->route_to,
    //             'formatted_created_at' => $formattedDate,
    //         ];
    //     }

    //     return $this->sendResponse($tripsGroupedByDate, 'Collection report generated successfully.');
    // }
    // public function getCollectionReport(Request $request)
    // {
    //     // Validate the incoming request
    //     $validator = Validator::make($request->all(), [
    //         'device_id' => 'required|string|exists:devices,id',
    //     ]);

    //     // Handle validation failure
    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Fetch the collection report with proper joins
    //     $collectionReport = StartTrip::select(
    //             'start_trips.id',
    //             'start_trips.trip_name',
    //             'start_trips.created_at',
    //             'start_trips.updated_at',
    //             'routes.route_from',
    //             'routes.route_to'
    //         )
    //         ->join('routes', 'routes.id', '=', 'start_trips.route_id')
    //         ->where('start_trips.device_id', $request->device_id)
    //         ->get();

    //     // Group trips by the created date
    //     $tripsGroupedByDate = [];

    //     foreach ($collectionReport as $trip) {
    //         $formattedDate = \Carbon\Carbon::parse($trip->created_at)->format('d M Y');
    //         $startTime = \Carbon\Carbon::parse($trip->created_at)->format('H:i');
    //         $endTime = \Carbon\Carbon::parse($trip->updated_at)->format('H:i');
    //         $endDate = \Carbon\Carbon::parse($trip->updated_at)->format('d M Y');

    //         // Initialize the array if it doesn't exist
    //         if (!isset($tripsGroupedByDate[$formattedDate])) {
    //             $tripsGroupedByDate[$formattedDate] = [
    //                 'date' => $formattedDate,
    //                 // 'end_date' => $endDate,
    //                 // 'start_time' => $startTime,
    //                 // 'end_time' => $endTime,
    //                 // 'total_trips' => 0, // Initialize total trips
    //                 'trips' => []
    //             ];
    //         }

    //         // Append the trip to the respective date
    //         $tripsGroupedByDate[$formattedDate]['trips'][] = [
    //             'id' => $trip->id,
    //             'trip_name' => $trip->trip_name,
    //             'start_date' =>$formattedDate,
    //             'end_date' => $endDate,
    //             'start_stage_name' => $trip->route_from,
    //             'end_stage_name' => $trip->route_to,

    //         ];

    //     }

    //     // Re-index the array for output
    //     $tripsWithDetails = array_values($tripsGroupedByDate);

    //     // Return the response
    //     return $this->sendResponse($tripsWithDetails, 'Collection report generated successfully.');
    // }





    // public function getTripDetails(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|string|exists:trips,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     $trip = Trip::select(
    //         'trips.id',
    //         'trips.trip_name',
    //         'trips.full_ticket',
    //         'trips.half_ticket',
    //         'trips.student_ticket',
    //         'trips.language_ticket',
    //         'trips.physical_ticket',
    //         'trips.stage_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.created_at',
    //         'trips.route_status',
    //         'stages.stage_data',
    //         'trips.total_amount',
    //         'trips.total_expense',
    //         'trips.net_total',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'trips.conductor_collection',
    //         'trips.cleaner_collection',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket_price',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket_price'
    //     )
    //         ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //         ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //         ->where('trips.id', $request->trip_id)
    //         ->first();

    //     if (!$trip) {
    //         return $this->sendError('Trip not found.');
    //     }

    //     $stageData = json_decode($trip->stage_data, true);

    //     $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //     $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //     $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //     $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //     $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //     $tripLanguageTicketPrice = $trip->lagguage_ticket_price;
    //     $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //     $totalTicketCount = $trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket;

    //     $netTotal = $trip->net_total + $trip->cleaner_collection;

    //     $tripDetails = [
    //         'id' => $trip->id,
    //         'start_date' => \Carbon\Carbon::parse($trip->start_date)->format('d M Y'),
    //         'end_date' => \Carbon\Carbon::parse($trip->end_date)->format('d M Y'),
    //         'start_time' => \Carbon\Carbon::parse($trip->start_date)->format('H:i'),
    //         'end_time' => \Carbon\Carbon::parse($trip->end_date)->format('H:i'),
    //         'trip_name' => $trip->trip_name,
    //         'start_stage_name' => $trip->start_stage_name,
    //         'end_stage_name' => $trip->end_stage_name,
    //         'full_ticket_count' => $trip->full_ticket,
    //         'half_ticket_count' => $trip->half_ticket,
    //         'student_ticket_count' => $trip->student_ticket,
    //         'language_ticket_count' => $trip->language_ticket,
    //         'physical_ticket_count' => $trip->physical_ticket,
    //         'full_ticket_price' => $tripFullTicketPrice,
    //         'half_ticket_price' => $tripHalfTicketPrice,
    //         'student_ticket_price' => $tripStudentTicketPrice,
    //         'language_ticket_price' => $tripLanguageTicketPrice,
    //         'physical_ticket_price' => $tripPhysicalTicketPrice,
    //         'total_ticket_price' => $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice,
    //         'total_tickets' => $totalTicketCount,
    //         'conductor_collection' => $trip->conductor_collection,
    //         'cleaner_collection' => $trip->cleaner_collection,
    //         'formatted_created_at' => \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //         'total_amount' => $trip->total_amount,
    //         'total_expense' => $trip->total_expense,
    //         'net_total' => $netTotal
    //     ];

    //     return $this->sendResponse($tripDetails, 'Trip details fetched successfully.');
    // }
    //     public function getTripDetails(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Invalid data', $validator->errors()->toArray());
    //     }

    //     // Fetch aggregated trip details with bookings
    //     $tripDetails = StartTrip::leftJoin('trips', 'start_trips.id', '=', 'trips.trip_id')
    //         ->select(
    //             'start_trips.id',
    //             'trips.trip_id',
    //             'start_trips.trip_name',
    //             'start_trips.created_at',
    //             'start_trips.updated_at',
    //             'start_trips.start_trip_date',
    //             'start_trips.end_trip_date',
    //             'start_trips.cleaner_collection',
    //             DB::raw('SUM(trips.full_ticket) as total_full_tickets'),
    //             DB::raw('SUM(trips.half_ticket) as total_half_tickets'),
    //             DB::raw('SUM(trips.student_ticket) as total_student_tickets'),
    //             DB::raw('SUM(trips.language_ticket) as total_language_tickets'),
    //             DB::raw('SUM(trips.physical_ticket) as total_physical_tickets'),
    //             DB::raw('SUM(trips.full_ticket * COALESCE(trips.full_ticket_price, 0)) as total_full_ticket_price'),
    //             DB::raw('SUM(trips.half_ticket * COALESCE(trips.half_ticket_price, 0)) as total_half_ticket_price'),
    //             DB::raw('SUM(trips.student_ticket * COALESCE(trips.student_ticket_price, 0)) as total_student_ticket_price'),
    //             DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
    //             DB::raw('SUM(trips.physical_ticket * COALESCE(trips.physical_ticket_price, 0)) as total_physical_ticket_price'),
    //             DB::raw('SUM(trips.total_amount) as total_amount'),
    //             DB::raw('SUM(trips.total_expense) as total_expense'),
    //             DB::raw('SUM(trips.net_total) as net_total')

    //         )
    //         ->where('trips.trip_id', $request->trip_id)
    //         ->groupBy('start_trips.id', 'start_trips.trip_name','start_trips.created_at','start_trips.updated_at','trips.trip_id','start_trips.cleaner_collection','start_trips.start_trip_date','start_trips.end_trip_date')
    //         ->first();
    //     if (!$tripDetails) {
    //         $tripDeetails = StartTrip::find($request->trip_id);

    //         return $this->sendResponse($responseDetails, 'Trip details fetched successfully.');
    //     }

    //     $totalTicketPrice = $tripDetails->total_full_ticket_price +
    //                         $tripDetails->total_half_ticket_price +
    //                         $tripDetails->total_student_ticket_price +
    //                         $tripDetails->total_language_ticket_price +
    //                         $tripDetails->total_physical_ticket_price;

    //     $totalTicketCount = $tripDetails->total_full_tickets +
    //                         $tripDetails->total_half_tickets +
    //                         $tripDetails->total_student_tickets +
    //                         $tripDetails->total_language_tickets +
    //                         $tripDetails->total_physical_tickets;

    //     $netTotal = $totalTicketPrice + ($tripDetails->cleaner_collection ?? 0);

    //     $totalExpense = TripExpense::where('trip_id', $request->trip_id)
    //     ->select('total_expense')
    //     ->first();

    //     $totalExpenseAmount = $totalExpense->total_expense ?? 0;

    //     $total = $netTotal - $totalExpenseAmount;

    //     $responseDetails = [
    //         'id' => $tripDetails->id,
    //         // 'start_date' => \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('d M Y'),
    //         // 'end_date' => \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('d M Y'),
    //         // 'start_time' => \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('H:i'),
    //         // 'end_time' => \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('H:i'),
    //         'start_date' => (string) $tripDetails->start_trip_date ? \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('d M Y') : '',
    //         'end_date' => (string) $tripDetails->end_trip_date ? \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('d M Y') : '',
    //         'start_time' => (string) $tripDetails->start_trip_date ? \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('H:i') : '',
    //         'end_time' => (string) $tripDetails->end_trip_date ? \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('H:i') : '',
    //         'trip_name' => (string) $tripDetails->trip_name,
    //         // 'start_stage_name' => $tripDetails->start_stage_name ?? '',
    //         // 'end_stage_name' => $tripDetails->end_stage_name ?? '',
    //         'full_ticket_count' => (string) $tripDetails->total_full_tickets,
    //         'half_ticket_count' => (string) $tripDetails->total_half_tickets,
    //         'student_ticket_count' => (string) $tripDetails->total_student_tickets,
    //         'language_ticket_count' => (string) $tripDetails->total_language_tickets,
    //         'physical_ticket_count' => (string) $tripDetails->total_physical_tickets,
    //         'total_full_ticket_price' => (string) $tripDetails->total_full_ticket_price,
    //         'total_half_ticket_price' => (string) $tripDetails->total_half_ticket_price,
    //         'total_student_ticket_price' => (string) $tripDetails->total_student_ticket_price,
    //         'total_language_ticket_price' => (string) $tripDetails->total_language_ticket_price,
    //         'total_physical_ticket_price' => (string) $tripDetails->total_physical_ticket_price,
    //         'total_ticket_price' => (int) $totalTicketPrice,
    //         'total_tickets' => (int) $totalTicketCount,
    //         'conductor_collection' => (int) $tripDetails->conductor_collection ?? 0,
    //         'cleaner_collection' => (int) $tripDetails->cleaner_collection ?? 0,
    //         'formatted_created_at' => (string) \Carbon\Carbon::parse($tripDetails->created_at)->format('d M Y'),
    //         'total_amount' => (int) $netTotal,
    //         'total_expense' => (int) $totalExpenseAmount,
    //         'net_total' => (int) $total
    //     ];

    //     return $this->sendResponse($responseDetails, 'Trip details fetched successfully.');
    // }

    public function getTripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Fetch aggregated trip details with bookings
        $tripDetails = StartTrip::leftJoin('trips', 'start_trips.id', '=', 'trips.trip_id')
            ->select(
                'start_trips.id',
                'trips.trip_id',
                'start_trips.trip_name',
                'start_trips.created_at',
                'start_trips.updated_at',
                'start_trips.start_trip_date',
                'start_trips.end_trip_date',
                'start_trips.cleaner_collection',
                DB::raw('SUM(trips.full_ticket) as total_full_tickets'),
                DB::raw('SUM(trips.half_ticket) as total_half_tickets'),
                DB::raw('SUM(trips.student_ticket) as total_student_tickets'),
                DB::raw('SUM(trips.language_ticket) as total_language_tickets'),
                DB::raw('SUM(trips.physical_ticket) as total_physical_tickets'),
                DB::raw('SUM(trips.full_ticket * COALESCE(trips.full_ticket_price, 0)) as total_full_ticket_price'),
                DB::raw('SUM(trips.half_ticket * COALESCE(trips.half_ticket_price, 0)) as total_half_ticket_price'),
                DB::raw('SUM(trips.student_ticket * COALESCE(trips.student_ticket_price, 0)) as total_student_ticket_price'),
                // DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
                DB::raw('SUM(trips.language_ticket * COALESCE(trips.lagguage_ticket_price, 0)) as total_language_ticket_price'),
                DB::raw('SUM(trips.physical_ticket * COALESCE(trips.physical_ticket_price, 0)) as total_physical_ticket_price'),
                DB::raw('SUM(trips.total_amount) as total_amount'),
                DB::raw('SUM(trips.total_expense) as total_expense'),
                DB::raw('SUM(trips.net_total) as net_total')

            )
            ->where('trips.trip_id', $request->trip_id)
            ->groupBy('start_trips.id', 'start_trips.trip_name', 'start_trips.created_at', 'start_trips.updated_at', 'trips.trip_id', 'start_trips.cleaner_collection', 'start_trips.start_trip_date', 'start_trips.end_trip_date')
            ->first();
        if (!$tripDetails) {
            $emptyTripDetails = StartTrip::find($request->trip_id);
            $totalExpense = TripExpense::where('trip_id', $request->trip_id)
                ->select('total_expense')
                ->first();
            $totalExpenseValue = $totalExpense ? (int) $totalExpense->total_expense : 0;
            $netTotal = 0 - $totalExpenseValue;
            $responseDetails = [
                'id' => $emptyTripDetails->id,
                'start_date' => (string) $emptyTripDetails->start_trip_date ? \Carbon\Carbon::parse($emptyTripDetails->start_trip_date)->format('d M Y') : '',
                'end_date' => (string) $emptyTripDetails->end_trip_date ? \Carbon\Carbon::parse($emptyTripDetails->end_trip_date)->format('d M Y') : '',
                'start_time' => (string) $emptyTripDetails->start_trip_date ? \Carbon\Carbon::parse($emptyTripDetails->start_trip_date)->format('H:i') : '',
                'end_time' => (string) $emptyTripDetails->end_trip_date ? \Carbon\Carbon::parse($emptyTripDetails->end_trip_date)->format('H:i') : '',
                'trip_name' => (string) $emptyTripDetails->trip_name ?? '',
                'full_ticket_count' => '0',
                'half_ticket_count' =>  '0',
                'student_ticket_count' => '0',
                'language_ticket_count' => '0',
                'physical_ticket_count' => '0',
                'total_full_ticket_price' => '0',
                'total_half_ticket_price' => '0',
                'total_student_ticket_price' => '0',
                'total_language_ticket_price' => '0',
                'total_physical_ticket_price' => '0',
                'total_ticket_price' => 0,
                'total_tickets' => 0,
                'conductor_collection' => 0,
                'cleaner_collection' => 0,
                'formatted_created_at' => (string) \Carbon\Carbon::parse($emptyTripDetails->created_at)->format('d M Y'),
                'total_amount' => 0,
                'total_expense' => (int) $totalExpenseValue,
                'net_total' =>  (int) $netTotal
            ];

            return $this->sendResponse($responseDetails, 'Trip details fetched successfully.');
        }

        $totalTicketPrice = $tripDetails->total_full_ticket_price +
            $tripDetails->total_half_ticket_price +
            $tripDetails->total_student_ticket_price +
            $tripDetails->total_language_ticket_price +
            $tripDetails->total_physical_ticket_price;

        $totalTicketCount = $tripDetails->total_full_tickets +
            $tripDetails->total_half_tickets +
            $tripDetails->total_student_tickets +
            $tripDetails->total_language_tickets +
            $tripDetails->total_physical_tickets;

        $netTotal = $totalTicketPrice + ($tripDetails->cleaner_collection ?? 0);

        $totalExpense = TripExpense::where('trip_id', $request->trip_id)
            ->select('total_expense')
            ->first();

        $totalExpenseAmount = $totalExpense->total_expense ?? 0;

        $total = $netTotal - $totalExpenseAmount;

        $responseDetails = [
            'id' => $tripDetails->id,
            // 'start_date' => \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('d M Y'),
            // 'end_date' => \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('d M Y'),
            // 'start_time' => \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('H:i'),
            // 'end_time' => \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('H:i'),
            'start_date' => (string) $tripDetails->start_trip_date ? \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('d M Y') : '',
            'end_date' => (string) $tripDetails->end_trip_date ? \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('d M Y') : '',
            'start_time' => (string) $tripDetails->start_trip_date ? \Carbon\Carbon::parse($tripDetails->start_trip_date)->format('H:i') : '',
            'end_time' => (string) $tripDetails->end_trip_date ? \Carbon\Carbon::parse($tripDetails->end_trip_date)->format('H:i') : '',
            'trip_name' => (string) $tripDetails->trip_name,
            // 'start_stage_name' => $tripDetails->start_stage_name ?? '',
            // 'end_stage_name' => $tripDetails->end_stage_name ?? '',
            'full_ticket_count' => (string) $tripDetails->total_full_tickets,
            'half_ticket_count' => (string) $tripDetails->total_half_tickets,
            'student_ticket_count' => (string) $tripDetails->total_student_tickets,
            'language_ticket_count' => (string) $tripDetails->total_language_tickets,
            'physical_ticket_count' => (string) $tripDetails->total_physical_tickets,
            'total_full_ticket_price' => (string) $tripDetails->total_full_ticket_price,
            'total_half_ticket_price' => (string) $tripDetails->total_half_ticket_price,
            'total_student_ticket_price' => (string) $tripDetails->total_student_ticket_price,
            'total_language_ticket_price' => (string) $tripDetails->total_language_ticket_price,
            'total_physical_ticket_price' => (string) $tripDetails->total_physical_ticket_price,
            'total_ticket_price' => (int) $totalTicketPrice,
            'total_tickets' => (int) $totalTicketCount,
            'conductor_collection' => (int) $tripDetails->conductor_collection ?? 0,
            'cleaner_collection' => (int) $tripDetails->cleaner_collection ?? 0,
            'formatted_created_at' => (string) \Carbon\Carbon::parse($tripDetails->created_at)->format('d M Y'),
            'total_amount' => (int) $netTotal,
            'total_expense' => (int) $totalExpenseAmount,
            'net_total' => (int) $total
        ];

        return $this->sendResponse($responseDetails, 'Trip details fetched successfully.');
    }

    public function submitCleanerAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|string',
            'cleaner_amount' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $trip = StartTrip::find($request->trip_id);
        if (!$trip) {
            return $this->sendError('Trip not found.');
        }

        $trip->cleaner_collection = $request->cleaner_amount;
        $trip->save();

        $responseData = [
            'id' => (int) $trip->id,
            'route_id' => (string) $trip->route_id,
            'device_id' => (string) $trip->device_id,
            'cleaner_collection' => (string) $trip->cleaner_collection,
            'trip_name' => (string) $trip->trip_name,
            'start_trip_date' => $trip->start_trip_date ? (string) Carbon::parse($trip->start_trip_date)->toDateTimeString() : '',
            'end_trip_date' => $trip->end_trip_date ? (string) Carbon::parse($trip->end_trip_date)->toDateTimeString() : '',
            'status' => (string) $trip->status,
            'created_at' => (string) $trip->created_at,
            'updated_at' => (string) $trip->updated_at
        ];

        return $this->sendResponse($responseData, 'Cleaner amount updated successfully.');
    }

    public function addExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:start_trips,id',
            'diesel' => 'nullable|numeric',
            'driver' => 'nullable|numeric',
            'cleaner' => 'nullable|numeric',
            'conductor' => 'nullable|numeric',
            'stand' => 'nullable|numeric',
            'toll' => 'nullable|numeric',
            'wash' => 'nullable|numeric',
            'oil' => 'nullable|numeric',
            'bank' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Calculate total expenses
        $totalExpense = collect($request->only(['diesel', 'driver', 'cleaner', 'stand', 'toll', 'wash', 'oil', 'bank', 'conductor']))->sum();

        // Create a new expense record
        $expense = TripExpense::create([
            'trip_id' => $request->trip_id,
            'diesel' => $request->diesel ?? 0,
            'driver' => $request->driver ?? 0,
            'cleaner' => $request->cleaner ?? 0,
            'conductor' => $request->conductor ?? 0,
            'stand' => $request->stand ?? 0,
            'toll' => $request->toll ?? 0,
            'wash' => $request->wash ?? 0,
            'oil' => $request->oil ?? 0,
            'bank' => $request->bank ?? 0,
            'total_expense' => $totalExpense,
        ]);

        //  $responseData = [
        //      'id' => (int) $expense->id,
        //     'trip_id' => (string) $expense->trip_id,
        //     'diesel' => (int) $expense->diesel,
        //     'driver' => (int) $expense->driver,
        //     'cleaner' => (int) $expense->cleaner,
        //     'conductor' => (int) $expense->conductor,
        //     'stand' => (int) $expense->stand,
        //     'toll' => (int) $expense->toll,
        //     'wash' => (int) $expense->wash,
        //     'oil' => (int) $expense->oil,
        //     'bank' => (int) $expense->bank,
        //     'total_expense' => (int) $expense->total_expense,
        //     'created_at' => (string) $expense->created_at->toDateTimeString(),
        //     'updated_at' => (string) $expense->updated_at->toDateTimeString(),
        // ];


        return $this->sendResponse($expense, 'Expense added successfully.');
    }

    // public function generateQrCode(Request $request)
    //     {
    //         $deviceId = $this->getLoggedUserId();
    //         $device = Device::find($deviceId);

    //         if (!$device) {
    //             return $this->sendError('Device missing.');
    //         }

    //         if ($device->user_name && $device->password) {
    //             $username = $device->user_name;
    //             $password = $device->password;

    //             $data = [
    //                 'user_name' => $username,
    //                 'passcode' => $password,
    //             ];

    //             $serializedData = serialize($data);
    //             $encryptedId = Crypt::encryptString($serializedData);

    //             // Generate the QR code
    //             $folderName = time();
    //             $fileName = 'qrcode_' . time() . '.png';
    //             $publicPath = public_path('storage/loginQR/' . $folderName);

    //             if (!file_exists($publicPath)) {
    //                 mkdir($publicPath, 0755, true);
    //             }

    //             // Save QR code to a file
    //             QrCode::format('png')->size(512)->generate($encryptedId, $publicPath . '/' . $fileName);

    //             $pathUrl = asset('storage/loginQR/' . $folderName . '/' . $fileName);

    //             $device->update([
    //                 'qr_code' => $pathUrl,
    //             ]);

    //             $data = [
    //                 'user_name' => $username,
    //                 'qrcode' => $pathUrl,
    //             ];

    //             return $this->sendResponse($data, 'QR code generated');
    //         }

    //         return $this->sendError('Please update customer user_name or password.');
    //     }

    // public function generateQrCode(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'gpay_id' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Validation Error', $validator->errors());
    //     }

    //     $upiId = $request->input('gpay_id');
    //     $currency = 'INR';

    //     // UPI URI without amount and note
    //     $upiUri = "upi://pay?pa=" . urlencode($upiId) . "&cu=$currency";

    //     \Log::info('Generated UPI URI: ' . $upiUri);

    //     $folderName = time();
    //     $fileName = 'qrcode_' . time() . '.png';
    //     $publicPath = public_path('storage/loginQR/' . $folderName);

    //     if (!file_exists($publicPath)) {
    //         mkdir($publicPath, 0755, true);
    //     }

    //     try {
    //         // Generate QR code locally using the Simple QR Code library
    //         $qrCode = QrCode::format('png')->size(512)->generate($upiUri);
    //         $filePath = $publicPath . '/' . $fileName;

    //         // Save QR code image
    //         file_put_contents($filePath, $qrCode);

    //         $pathUrl = asset('storage/loginQR/' . $folderName . '/' . $fileName);

    //         \Log::info('Saved QR Code at: ' . $pathUrl);

    //         return $this->sendResponse([
    //             'gpay_id' => $upiId,
    //             'qrcode' => $pathUrl,
    //             'currency' => $currency,
    //         ], 'QR code generated and saved');
    //     } catch (\Exception $e) {
    //         return $this->sendError('Error generating QR code: ' . $e->getMessage());
    //     }

    // }



    public function generateQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gpay_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $upiId = $request->input('gpay_id');
        $currency = 'INR';

        // UPI URI without amount and note
        $upiUri = "upi://pay?pa=" . urlencode($upiId) . "&cu=$currency";

        \Log::info('Generated UPI URI: ' . $upiUri);

        // Set the folder and file paths
        $folderName = time();
        $fileName = 'qrcode_' . time() . '.png';
        $publicPath = public_path('storage/loginQR/' . $folderName);

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        try {
            // Generate QR code with Chillerlan
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG, // Output as PNG
                'eccLevel'   => QRCode::ECC_L, // Error correction level
                'scale'      => 10, // Size of the QR code
            ]);

            $qrcode = new QRCode($options);

            // Generate the QR code and save it as a PNG file
            $filePath = $publicPath . '/' . $fileName;
            $qrcode->render($upiUri, $filePath); // Save QR code to file

            $pathUrl = asset('storage/loginQR/' . $folderName . '/' . $fileName);

            \Log::info('Saved QR Code at: ' . $pathUrl);

            return $this->sendResponse([
                'gpay_id' => $upiId,
                'qrcode'  => $pathUrl,
                'currency' => $currency,
            ], 'QR code generated and saved');
        } catch (\Exception $e) {
            return $this->sendError('Error generating QR code: ' . $e->getMessage());
        }
    }

    public function getDashboard(Request $request)
    {
        $userId = $this->getLoggedUserId();

        $device = Device::select('id', 'manager_id', 'route_id', 'user_name', 'password', 'logo', 'header_one', 'header_two', 'footer', 'created_at', 'updated_at', 'qr_code')
            ->where('id', $userId)
            ->first();

        if (!$device) {
            return $this->sendResponse(null, 'No device found for this user.', 404);
        }

        $stagesData = $this->getStagesForDashboard($device->id);
        // dd($stagesData);

        $formattedDevice = [
            'id' => $device->id,
            'manager_id' => $device->manager_id,
            'route_id' => $device->route_id,
            'user_name' => $device->user_name,
            'logo' => asset('storage/device/' . $device->logo),
            'header_one' => $device->header_one ?? '',
            'header_two' => $device->header_two ?? '',
            'footer' => $device->footer ?? '',
            'qr_code_path' => asset('storage/qr_code/' . $device->qr_code) ?? asset('public/images/blog.jpg'),
            'previous_collection' => $this->lastTendaysTripCollection($device->id),
            'trip_details' => $this->lastTenDaysCollectionReport($device->id),
            'routes' => $this->getRouteForDashboard($device->id),
            // 'stages' => $stagesData['data'],
            'created_at' => $device->created_at,
            'updated_at' => $device->updated_at,
        ];
        return $this->sendResponse($formattedDevice, 'Device retrieved successfully.');
    }

    public function multipleTicketBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tickets' => 'required|array',
            'tickets.*.trip_id' => 'required|string',
            'tickets.*.start_day_id' => 'required|string',
            'tickets.*.device_id' => 'required|string|exists:devices,id',
            'tickets.*.route_id' => 'required|string|exists:routes,id',
            'tickets.*.stage_id' => 'required|string|exists:stages,id',
            'tickets.*.start_id' => 'required|string',
            'tickets.*.stop_id' => 'required|string',
            'tickets.*.route_status' => 'required|string',
            'tickets.*.full_ticket' => 'nullable|integer|min:0',
            'tickets.*.half_ticket' => 'nullable|integer|min:0',
            'tickets.*.student_ticket' => 'nullable|integer|min:0',
            'tickets.*.lagguage_ticket' => 'nullable|integer|min:0',
            'tickets.*.physical_ticket' => 'nullable|integer|min:0',
            'tickets.*.full_ticket_price' => 'nullable|numeric|min:0',
            'tickets.*.half_ticket_price' => 'nullable|numeric|min:0',
            'tickets.*.student_ticket_price' => 'nullable|numeric|min:0',
            'tickets.*.lagguage_ticket_price' => 'nullable|numeric|min:0',
            'tickets.*.physical_ticket_price' => 'nullable|numeric|min:0',
            'tickets.*.date' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $createdTrips = [];

        foreach ($request->tickets as $ticketData) {
            // Generate unique trip names
            $latestTrip = Trip::orderBy('created_at', 'desc')->first();
            $nextNumber = $latestTrip ? (int) substr($latestTrip->trip_name, 4) + 1 : 1;
            $tripName = sprintf('TICKET%04d', $nextNumber);

            // Find the appropriate stage for the route
            $stage = Stage::find($ticketData['stage_id']);
            if (!$stage) {
                return $this->sendError('No stage data found for the provided stage ID.');
            }

            // Calculate ticket totals
            $totalFullTicketPrice = ($ticketData['full_ticket'] ?? 0) * ($ticketData['full_ticket_price'] ?? 0);
            $totalHalfTicketPrice = ($ticketData['half_ticket'] ?? 0) * ($ticketData['half_ticket_price'] ?? 0);
            $totalStudentTicketPrice = ($ticketData['student_ticket'] ?? 0) * ($ticketData['student_ticket_price'] ?? 0);
            $totalLuggageTicketPrice = ($ticketData['lagguage_ticket'] ?? 0) * ($ticketData['lagguage_ticket_price'] ?? 0);
            $totalPhysicalTicketPrice = ($ticketData['physical_ticket'] ?? 0) * ($ticketData['physical_ticket_price'] ?? 0);

            $totalAmount = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLuggageTicketPrice + $totalPhysicalTicketPrice;
            $totalExpense = 0; // Define or calculate the total expense if necessary
            $netTotal = $totalAmount - $totalExpense;

            // Create a new trip for each ticket
            $trip = Trip::create([
                'trip_id' =>  $ticketData['trip_id'],
                'start_day_id' => $ticketData['start_day_id'],
                'device_id' => $ticketData['device_id'],
                'stage_id' => $stage->id, // Use the found stage's ID
                'trip_name' => $tripName,
                'start_id' => $ticketData['start_id'],
                'stop_id' => $ticketData['stop_id'],
                'full_ticket' => $ticketData['full_ticket'] ?? 0,
                'half_ticket' => $ticketData['half_ticket'] ?? 0,
                'student_ticket' => $ticketData['student_ticket'] ?? 0,
                'language_ticket' => $ticketData['lagguage_ticket'] ?? 0,
                'physical_ticket' => $ticketData['physical_ticket'] ?? 0,
                'total_amount' => $totalAmount,
                'total_expense' => $totalExpense,
                'net_total' => $netTotal,
                'route_status' => $ticketData['route_status'],
                'full_ticket_price' => $ticketData['full_ticket_price'],
                'half_ticket_price' => $ticketData['half_ticket_price'],
                'student_ticket_price' => $ticketData['student_ticket_price'],
                'lagguage_ticket_price' => $ticketData['lagguage_ticket_price'],
                'physical_ticket_price' => $ticketData['physical_ticket_price'],
                'created_at' => $ticketData['date'] ?? Carbon::now() // Use provided date or current time
            ]);

            $createdTrips[] = $trip;
        }

        return $this->sendResponse($createdTrips, 'Tickets booked successfully');
    }

    // public function lastTendaysTripCollection($deviceId)
    // {
    //     $dateTenDaysAgo = \Carbon\Carbon::now()->subDays(10)->toDateString();
    //      $trips = StartTrip::select(
    //         DB::raw('DATE(start_days.start_time) as trip_date'),
    //         'start_trips.id as start_trip_id',
    //         'trips.id',
    //         'trips.trip_id as trip_id',
    //         'trips.start_id',
    //         'trips.stop_id',
    //         'trips.full_ticket',
    //         'trips.full_ticket_price',
    //         'trips.half_ticket',
    //         'trips.half_ticket_price',
    //         'trips.student_ticket',
    //         'trips.student_ticket_price',
    //         'trips.language_ticket',
    //         'trips.lagguage_ticket_price',
    //         'trips.physical_ticket',
    //         'trips.physical_ticket_price',
    //         'stages.stage_data',
    //         'start_trips.created_at',
    //         'start_days.start_time',
    //         'start_days.end_time',
    //         'start_days.created_at as start_date',
    //         'trips.route_status',
    //         'start_trips.trip_name',
    //         'start_trips.cleaner_collection'
    //         // 'trips.trip_name as ticket_name'
    //     )
    //     ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
    //     ->join('stages', 'trips.stage_id', '=', 'stages.id')
    //     ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
    //     ->where('trips.device_id', $deviceId)
    //     ->where('start_days.start_time', '>=', $dateTenDaysAgo)
    //     ->orderBy('start_days.start_time','desc')
    //     // ->limit(10)
    //     ->get();

    //     // Initialize daily totals
    //     $dailyTotals = [];

    //     foreach ($trips as $trip) {
    //         $totalExpense = TripExpense::where('trip_id', $trip->start_trip_id)
    //     ->select('total_expense')
    //     ->first();
    //         // Format trip start_time to date
    //         $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

    //         // Initialize daily totals for the date if not set
    //         if (!isset($dailyTotals[$date])) {
    //             $dailyTotals[$date] = [
    //                 'full_ticket_price' => 0,
    //                 'half_ticket_price' => 0,
    //                 'student_ticket_price' => 0,
    //                 'language_ticket_price' => 0,
    //                 'physical_ticket_price' => 0,
    //                 'total_full_ticket_count' => 0,
    //                 'total_half_ticket_count' => 0,
    //                 'total_student_ticket_count' => 0,
    //                 'total_language_ticket_count' => 0,
    //                 'total_physical_ticket_count' => 0,
    //                 'cleaner_collection' => 0,
    //                 'total_tickets' => 0,
    //                 'total_trips' => 0,
    //                 'total_price' => 0,
    //                 'total_expense' => 0,
    //                 'net_total' => 0,
    //                 'trips' => [],
    //             ];
    //         }

    //         // Calculate ticket prices
    //         $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
    //         $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
    //         $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
    //         $tripLanguageTicketPrice =  $trip->lagguage_ticket_price;
    //         $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

    //         // Aggregate the daily totals
    //         $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
    //         $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
    //         $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
    //         $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
    //         $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

    //         // Count tickets
    //         $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
    //         $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
    //         $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
    //         $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
    //         $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;
    //         $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection;
    //         $dailyTotals[$date]['start_date'] = date('d M Y', strtotime($trip->start_time));
    //         $dailyTotals[$date]['end_date'] = date('d M Y', strtotime($trip->end_time));
    //         $dailyTotals[$date]['start_time'] = date('H:i', strtotime($trip->start_time));
    //         $dailyTotals[$date]['end_time'] = date('H:i', strtotime($trip->end_time));

    //       // Assuming $trip->start_time and $trip->end_time are strings
    // $date = date('d M Y', strtotime($trip->start_time));
    // $endDate = date('d M Y', strtotime($trip->end_time));
    // $startTime = date('H:i', strtotime($trip->start_time));
    // $endTime = date('H:i', strtotime($trip->end_time));


    //       // Total tickets and prices
    // $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);
    // $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
    // $dailyTotals[$date]['total_expense'] += $totalExpense ? $totalExpense->total_expense : 0;

    // // Initialize total for net total calculation
    // $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

    // // Add cleaner_collection only once if it hasn't been added
    // if (!isset($dailyTotals[$date]['hasAddedCleanerCollection'])) {
    //     $total += $trip->cleaner_collection;
    //     $dailyTotals[$date]['hasAddedCleanerCollection'] = true;
    // }

    // if (!isset($dailyTotals[$date]['hasSubtractedTotalExpense'])) {
    //     $total -= $totalExpense ? $totalExpense->amount : 0;
    //     $dailyTotals[$date]['hasSubtractedTotalExpense'] = true;
    // }


    // // Update the net total
    // $dailyTotals[$date]['net_total'] += $total;

    //         // Increment total trips for the day
    //         $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

    //         // Collect trip data for the day
    //         $tripData = [
    //             'id' => $trip->start_trip_id,
    //             // 'ticket_name' =>  $trip->ticket_name,
    //             'trip_name' => (string) $trip->trip_name,
    //             'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
    //             'trip_total_price' => (int) $total,
    //         ];

    //         $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

    //     if ($existingTripKey !== false) {
    //         // If it exists, update the total tickets and price
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
    //         $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
    //     } else {
    //         // If it doesn't exist, add the trip data
    //         $dailyTotals[$date]['trips'][] = $tripData;
    //     }
    //     }

    //     // dd($dailyTotals);

    //     // Format the final response
    //     $formattedData = [];
    //     foreach ($dailyTotals as $date => $data) {
    //         $formattedData[] = [
    //             'date' => (string) $data['start_date'],
    //             'end_date' => (string) $data['end_date'],
    //             'start_time' => (string) $data['start_time'],
    //             'end_time' => (string) $data['end_time'],
    //             'full_ticket_price' => (int) $data['full_ticket_price'],
    //             'cleaner_collection' => (int) ($data['cleaner_collection'] ?? 0),
    //             'half_ticket_price' => (int) $data['half_ticket_price'],
    //             'student_ticket_price' => (int) $data['student_ticket_price'],
    //             'language_ticket_price' => (int) $data['language_ticket_price'],
    //             'physical_ticket_price' => (int) $data['physical_ticket_price'],
    //             'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
    //             'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
    //             'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
    //             'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
    //             'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
    //             'total_tickets' => (int) $data['total_tickets'],
    //             'total_trips' => (int) $data['total_trips'],
    //             'total_price' => (int) $data['total_price'],
    //             'total_expense' => (int) $data['total_expense'],
    //             'net_total' => (int) $data['net_total'],
    //             'trips' => $data['trips'],
    //         ];
    //     }

    //     $formattedData = array_slice($formattedData, 0, 10);
    //     return $formattedData;

    // }

    public function lastTenDaysTripCollection($deviceId)
    {
        $trips = StartTrip::select(
            DB::raw('DATE(start_days.start_time) as trip_date'),
            'start_trips.id as start_trip_id',
            'trips.id',
            'trips.trip_id as trip_id',
            'trips.start_id',
            'trips.stop_id',
            'trips.full_ticket',
            'trips.full_ticket_price',
            'trips.half_ticket',
            'trips.half_ticket_price',
            'trips.student_ticket',
            'trips.student_ticket_price',
            'trips.language_ticket',
            'trips.lagguage_ticket_price',
            'trips.physical_ticket',
            'trips.physical_ticket_price',
            'stages.stage_data',
            'start_trips.created_at',
            'start_days.start_time',
            'start_days.end_time',
            'start_days.created_at as start_date',
            'trips.route_status',
            'start_trips.trip_name',
            'start_trips.cleaner_collection'
        )
            ->join('trips', 'start_trips.id', '=', 'trips.trip_id')
            ->join('stages', 'trips.stage_id', '=', 'stages.id')
            ->join('start_days', 'trips.start_day_id', '=', 'start_days.id')
            ->where('trips.device_id', $deviceId)
            ->orderBy('start_days.start_time', 'desc')
            // ->whereDate('start_trips.created_at', '2024-10-21')
            ->get();

        // Initialize daily totals
        $dailyTotals = [];

        // Array to track which trip IDs have already had expenses added
        $addedExpenseTripIds = [];

        $addedCleanerCollectionTripIds = [];

        foreach ($trips as $trip) {
            $totalExpense = TripExpense::where('trip_id', $trip->trip_id)
                ->select('total_expense')
                ->first();

            // Format trip start_time to date
            $date = \Carbon\Carbon::parse($trip->start_time)->format('d M Y');

            // Initialize daily totals for the date if not set
            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [
                    'full_ticket_price' => 0,
                    'half_ticket_price' => 0,
                    'student_ticket_price' => 0,
                    'language_ticket_price' => 0,
                    'physical_ticket_price' => 0,
                    'total_full_ticket_count' => 0,
                    'total_half_ticket_count' => 0,
                    'total_student_ticket_count' => 0,
                    'total_language_ticket_count' => 0,
                    'total_physical_ticket_count' => 0,
                    'cleaner_collection' => 0,
                    'total_tickets' => 0,
                    'total_trips' => 0,
                    'total_price' => 0,
                    'total_expense' => 0,
                    'net_total' => 0,
                    'trips' => [],
                    'hasAddedCleanerCollection' => false, // Track if expense has been subtracted
                ];
            }

            // Calculate ticket prices
            $tripFullTicketPrice = $trip->full_ticket * $trip->full_ticket_price;
            $tripHalfTicketPrice = $trip->half_ticket * $trip->half_ticket_price;
            $tripStudentTicketPrice = $trip->student_ticket * $trip->student_ticket_price;
            $tripLanguageTicketPrice = $trip->language_ticket * $trip->lagguage_ticket_price;
            $tripPhysicalTicketPrice = $trip->physical_ticket * $trip->physical_ticket_price;

            // Aggregate the daily totals
            $dailyTotals[$date]['full_ticket_price'] += $tripFullTicketPrice;
            $dailyTotals[$date]['half_ticket_price'] += $tripHalfTicketPrice;
            $dailyTotals[$date]['student_ticket_price'] += $tripStudentTicketPrice;
            $dailyTotals[$date]['language_ticket_price'] += $tripLanguageTicketPrice;
            $dailyTotals[$date]['physical_ticket_price'] += $tripPhysicalTicketPrice;

            // Count tickets
            $dailyTotals[$date]['total_full_ticket_count'] += $trip->full_ticket;
            $dailyTotals[$date]['total_half_ticket_count'] += $trip->half_ticket;
            $dailyTotals[$date]['total_student_ticket_count'] += $trip->student_ticket;
            $dailyTotals[$date]['total_language_ticket_count'] += $trip->language_ticket;
            $dailyTotals[$date]['total_physical_ticket_count'] += $trip->physical_ticket;

            $dailyTotals[$date]['start_date'] = date('d M Y', strtotime($trip->start_time));
            $dailyTotals[$date]['end_date'] = date('d M Y', strtotime($trip->end_time));
            $dailyTotals[$date]['start_time'] = date('H:i', strtotime($trip->start_time));
            $dailyTotals[$date]['end_time'] = date('H:i', strtotime($trip->end_time));

            // $date = date('d M Y', strtotime($trip->start_time));
            // $endDate = date('d M Y', strtotime($trip->end_time));
            // $startTime = date('H:i', strtotime($trip->start_time));
            // $endTime = date('H:i', strtotime($trip->end_time));

            // Total tickets and prices

            $dailyTotals[$date]['total_tickets'] += ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket);

            // Calculate total price and initialize net total
            $total = $tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice;

            if (!in_array($trip->start_trip_id, $addedCleanerCollectionTripIds)) {
                $dailyTotals[$date]['cleaner_collection'] += $trip->cleaner_collection ?? 0;
                $addedCleanerCollectionTripIds[] = $trip->start_trip_id; // Mark this trip ID as processed
            }


            // Add expense for this specific trip ID only once
            if (!in_array($trip->start_trip_id, $addedExpenseTripIds)) {
                $dailyTotals[$date]['total_expense'] += $totalExpense->total_expense ?? 0;
                $addedExpenseTripIds[] = $trip->start_trip_id; // Mark this trip ID as processed
            }
            $dailyTotals[$date]['total_price'] += ($tripFullTicketPrice + $tripHalfTicketPrice + $tripStudentTicketPrice + $tripLanguageTicketPrice + $tripPhysicalTicketPrice);
            // Calculate net total
            // $dailyTotals[$date]['net_total'] += ($total + $dailyTotals[$date]['cleaner_collection'] - $dailyTotals[$date]['total_expense']);
            $dailyTotals[$date]['net_total'] = $dailyTotals[$date]['total_price'] + $dailyTotals[$date]['cleaner_collection'] - $dailyTotals[$date]['total_expense'];

            // Increment total trips for the day
            $dailyTotals[$date]['total_trips'] += 1; // Incrementing by 1 for each trip

            // Collect trip data for the day
            $tripData = [
                'id' => (string) $trip->start_trip_id,
                'trip_name' => (string) $trip->trip_name,
                'trip_total_tickets' => (int) ($trip->full_ticket + $trip->half_ticket + $trip->student_ticket + $trip->language_ticket + $trip->physical_ticket),
                'trip_total_price' => (int) $total,
            ];

            $existingTripKey = array_search($tripData['id'], array_column($dailyTotals[$date]['trips'], 'id'));

            if ($existingTripKey !== false) {
                // If it exists, update the total tickets and price
                $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_tickets'] += $tripData['trip_total_tickets'];
                $dailyTotals[$date]['trips'][$existingTripKey]['trip_total_price'] += $tripData['trip_total_price'];
            } else {
                // If it doesn't exist, add the trip data
                $dailyTotals[$date]['trips'][] = $tripData;
            }
        }

        // Format the final response
        $formattedData = [];
        foreach ($dailyTotals as $date => $data) {
            $formattedData[] = [
                'date' => $date,
                'end_date' => (string) $data['end_date'],
                'start_time' => (string) $data['start_time'],
                'end_time' => (string) $data['end_time'],
                'full_ticket_price' => (int) $data['full_ticket_price'],
                'cleaner_collection' => (int) $data['cleaner_collection'],
                'half_ticket_price' => (int) $data['half_ticket_price'],
                'student_ticket_price' => (int) $data['student_ticket_price'],
                'language_ticket_price' => (int) $data['language_ticket_price'],
                'physical_ticket_price' => (int) $data['physical_ticket_price'],
                'total_full_ticket_count' => (int) $data['total_full_ticket_count'],
                'total_half_ticket_count' => (int) $data['total_half_ticket_count'],
                'total_student_ticket_count' => (int) $data['total_student_ticket_count'],
                'total_language_ticket_count' => (int) $data['total_language_ticket_count'],
                'total_physical_ticket_count' => (int) $data['total_physical_ticket_count'],
                'total_tickets' => (int) $data['total_tickets'],
                'total_trips' => (int) $data['total_trips'],
                'total_price' => (int) $data['total_price'] + $data['cleaner_collection'],
                'total_expense' => (int) $data['total_expense'],
                'net_total' => (int) $data['net_total'],
                'trips' => $data['trips'],
            ];
        }
        $slicedData = array_slice($formattedData, 0, 10);
        return $slicedData;
    }

    // public function lastTenDaysCollectionReport($deviceId)
    // {
    //     $tripDetails = StartTrip::leftJoin('trips', 'start_trips.id', '=', 'trips.trip_id')
    //         ->select(
    //             'start_trips.id',
    //             'trips.trip_id',
    //             'start_trips.trip_name',
    //             'start_trips.created_at',
    //             'start_trips.updated_at',
    //             'start_trips.start_trip_date',
    //             'start_trips.end_trip_date',
    //             'start_trips.cleaner_collection',
    //             'start_trips.route_id',
    //             'trips.start_id',
    //             'trips.stop_id',
    //             'start_trips.route_id',
    //             DB::raw('SUM(trips.full_ticket) as total_full_tickets'),
    //             DB::raw('SUM(trips.half_ticket) as total_half_tickets'),
    //             DB::raw('SUM(trips.student_ticket) as total_student_tickets'),
    //             DB::raw('SUM(trips.language_ticket) as total_language_tickets'),
    //             DB::raw('SUM(trips.physical_ticket) as total_physical_tickets'),
    //             DB::raw('SUM(trips.full_ticket * COALESCE(trips.full_ticket_price, 0)) as total_full_ticket_price'),
    //             DB::raw('SUM(trips.half_ticket * COALESCE(trips.half_ticket_price, 0)) as total_half_ticket_price'),
    //             DB::raw('SUM(trips.student_ticket * COALESCE(trips.student_ticket_price, 0)) as total_student_ticket_price'),
    //             DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
    //             DB::raw('SUM(trips.physical_ticket * COALESCE(trips.physical_ticket_price, 0)) as total_physical_ticket_price'),
    //             DB::raw('SUM(trips.total_amount) as total_amount'),
    //             DB::raw('SUM(trips.total_expense) as total_expense'),
    //             DB::raw('SUM(trips.net_total) as net_total'),
    //             'routes.route_from',
    //             'routes.route_to'
    //         )
    //         ->join('routes', 'start_trips.route_id', '=', 'routes.id')
    //         ->where('start_trips.device_id', $deviceId)
    //         ->groupBy('start_trips.id', 'start_trips.trip_name', 'start_trips.created_at', 'start_trips.updated_at', 'trips.trip_id', 'start_trips.cleaner_collection', 'start_trips.start_trip_date', 'start_trips.end_trip_date','start_trips.route_id','trips.start_id','trips.stop_id','routes.route_from','routes.route_to')
    //         ->orderBy('start_trips.start_trip_date','desc')
    //         // ->limit(10)
    //         ->get();

    //     if ($tripDetails->isEmpty()) {
    //         return $this->sendError('Trips not found.');
    //     }

    //     $tripReports = [];

    //     foreach ($tripDetails as $trip) {
    //         // Assuming stage_data is stored in start_trips, you can parse it here
    //         $routeData = Stage::where('route_id',$trip->route_id)->first()->stage_data;
    //         $stageData = json_decode($routeData ?? '[]', true);
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //         // Calculate total ticket price using aggregated values
    //         $totalTicketPrice = $trip->total_full_ticket_price +
    //                             $trip->total_half_ticket_price +
    //                             $trip->total_student_ticket_price +
    //                             $trip->total_language_ticket_price +
    //                             $trip->total_physical_ticket_price;

    //         $totalTicketCount = $trip->total_full_tickets +
    //                             $trip->total_half_tickets +
    //                             $trip->total_student_tickets +
    //                             $trip->total_language_tickets +
    //                             $trip->total_physical_tickets;

    //         $netTotal = $totalTicketPrice + ($trip->cleaner_collection ?? 0);

    //         // Fetch trip expenses
    //         $totalExpense = TripExpense::where('trip_id', $trip->id)
    //             ->select('total_expense')
    //             ->first();

    //         $totalExpenseAmount = $totalExpense->total_expense ?? 0;

    //         // Calculate final total after expense deduction
    //         $total = $netTotal - $totalExpenseAmount;

    //         $tripReports[] = [
    //             'id' => $trip->id,
    //             'start_date' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('d M Y') : '',
    //             'end_date' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('d M Y') : '',
    //             'start_time' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('H:i') : '',
    //             'end_time' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('H:i') : '',
    //             'trip_name' => (string) $trip->trip_name,
    //             'start_stage_name' => (string) $trip->route_from,
    //             'end_stage_name' => (string) $trip->route_to,
    //             'full_ticket_count' => (int) $trip->total_full_tickets,
    //             'half_ticket_count' => (int) $trip->total_half_tickets,
    //             'student_ticket_count' => (int) $trip->total_student_tickets,
    //             'language_ticket_count' => (int) $trip->total_language_tickets,
    //             'physical_ticket_count' => (int) $trip->total_physical_tickets,
    //             'total_full_ticket_price' => (int) $trip->total_full_ticket_price,
    //             'total_half_ticket_price' => (int) $trip->total_half_ticket_price,
    //             'total_student_ticket_price' => (int) $trip->total_student_ticket_price,
    //             'total_language_ticket_price' => (int) $trip->total_language_ticket_price,
    //             'total_physical_ticket_price' => (int) $trip->total_physical_ticket_price,
    //             'total_ticket_price' => (int) $totalTicketPrice,
    //             'total_tickets' => (int) $totalTicketCount,
    //             'conductor_collection' => (int) $trip->conductor_collection ?? 0,
    //             'cleaner_collection' => (int) $trip->cleaner_collection ?? 0,
    //             'formatted_created_at' => (string) \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //             'total_amount' => (int) $netTotal,
    //             'total_expense' => (int) $totalExpenseAmount,
    //             'net_total' => (int) $total
    //         ];
    //     }

    //     $tripReports = array_slice($tripReports, 0, 10);
    //     return $tripReports;
    // }
    // public function lastTenDaysCollectionReport($deviceId)
    // {
    //     // Step 1: First, retrieve the unique start_trip ids for the last 10 trips
    //     $tripIds = StartTrip::where('device_id', $deviceId)
    //         ->orderBy('start_trip_date', 'desc')
    //         ->limit(10) // Limit to the last 10 trips
    //         ->pluck('id'); // Only get the trip IDs

    //     if ($tripIds->isEmpty()) {
    //         return $this->sendError('Trips not found.');
    //     }

    //     $tripReports = [];

    //     // Step 2: Loop through each trip ID and fetch details
    //     foreach ($tripIds as $tripId) {
    //         // Join and aggregate details for the specific trip
    //         $trip = StartTrip::leftJoin('trips', 'start_trips.id', '=', 'trips.trip_id')
    //             ->join('routes', 'start_trips.route_id', '=', 'routes.id')
    //             ->select(
    //                 'start_trips.id',
    //                 'start_trips.trip_name',
    //                 'start_trips.created_at',
    //                 'start_trips.updated_at',
    //                 'start_trips.start_trip_date',
    //                 'start_trips.end_trip_date',
    //                 'start_trips.cleaner_collection',
    //                 'start_trips.route_id',
    //                 'trips.start_id',
    //                 'trips.stop_id',
    //                 DB::raw('SUM(trips.full_ticket) as total_full_tickets'),
    //                 DB::raw('SUM(trips.half_ticket) as total_half_tickets'),
    //                 DB::raw('SUM(trips.student_ticket) as total_student_tickets'),
    //                 DB::raw('SUM(trips.language_ticket) as total_language_tickets'),
    //                 DB::raw('SUM(trips.physical_ticket) as total_physical_tickets'),
    //                 DB::raw('SUM(trips.full_ticket * COALESCE(trips.full_ticket_price, 0)) as total_full_ticket_price'),
    //                 DB::raw('SUM(trips.half_ticket * COALESCE(trips.half_ticket_price, 0)) as total_half_ticket_price'),
    //                 DB::raw('SUM(trips.student_ticket * COALESCE(trips.student_ticket_price, 0)) as total_student_ticket_price'),
    //                 DB::raw('SUM(trips.lagguage_ticket_price) as total_language_ticket_price'),
    //                 DB::raw('SUM(trips.physical_ticket * COALESCE(trips.physical_ticket_price, 0)) as total_physical_ticket_price'),
    //                 DB::raw('SUM(trips.total_amount) as total_amount'),
    //                 DB::raw('SUM(trips.total_expense) as total_expense'),
    //                 DB::raw('SUM(trips.net_total) as net_total'),
    //                 'routes.route_from',
    //                 'routes.route_to'
    //             )
    //             ->where('trips.trip_id', $tripId)
    //             ->groupBy(
    //                 'start_trips.id', 'start_trips.trip_name', 'start_trips.created_at',
    //                 'start_trips.updated_at', 'trips.trip_id', 'start_trips.cleaner_collection',
    //                 'start_trips.start_trip_date', 'start_trips.end_trip_date',
    //                 'start_trips.route_id', 'trips.start_id', 'trips.stop_id',
    //                 'routes.route_from', 'routes.route_to'
    //             )
    //             ->first(); // Retrieve the trip details
    //         if (!$trip) {
    //             continue; // Skip if trip details are not found
    //         }

    //         // Fetch the stage data and parse it
    //         $routeData = Stage::where('route_id', $trip->route_id)->first()->stage_data;
    //         $stageData = json_decode($routeData ?? '[]', true);
    //         $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
    //         $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;

    //         // Calculate total ticket price
    //         $totalTicketPrice = $trip->total_full_ticket_price +
    //                             $trip->total_half_ticket_price +
    //                             $trip->total_student_ticket_price +
    //                             $trip->total_language_ticket_price +
    //                             $trip->total_physical_ticket_price;

    //         $totalTicketCount = $trip->total_full_tickets +
    //                             $trip->total_half_tickets +
    //                             $trip->total_student_tickets +
    //                             $trip->total_language_tickets +
    //                             $trip->total_physical_tickets;

    //         $netTotal = $totalTicketPrice + ($trip->cleaner_collection ?? 0);

    //         // Fetch trip expenses
    //         $totalExpense = TripExpense::where('trip_id', $trip->id)
    //             ->select('total_expense')
    //             ->first();

    //         $totalExpenseAmount = $totalExpense->total_expense ?? 0;

    //         // Calculate final total after expense deduction
    //         $total = $netTotal - $totalExpenseAmount;

    //         // Add the trip report details to the array
    //         $tripReports[] = [
    //             'id' => $trip->id,
    //             'start_date' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('d M Y') : '',
    //             'end_date' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('d M Y') : '',
    //             'start_time' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('H:i') : '',
    //             'end_time' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('H:i') : '',
    //             'trip_name' => (string) $trip->trip_name,
    //             'start_stage_name' => (string) $trip->start_stage_name,
    //             'end_stage_name' => (string) $trip->end_stage_name,
    //             'full_ticket_count' => (int) $trip->total_full_tickets,
    //             'half_ticket_count' => (int) $trip->total_half_tickets,
    //             'student_ticket_count' => (int) $trip->total_student_tickets,
    //             'language_ticket_count' => (int) $trip->total_language_tickets,
    //             'physical_ticket_count' => (int) $trip->total_physical_tickets,
    //             'total_full_ticket_price' => (int) $trip->total_full_ticket_price,
    //             'total_half_ticket_price' => (int) $trip->total_half_ticket_price,
    //             'total_student_ticket_price' => (int) $trip->total_student_ticket_price,
    //             'total_language_ticket_price' => (int) $trip->total_language_ticket_price,
    //             'total_physical_ticket_price' => (int) $trip->total_physical_ticket_price,
    //             'total_ticket_price' => (int) $totalTicketPrice,
    //             'total_tickets' => (int) $totalTicketCount,
    //             'conductor_collection' => (int) $trip->conductor_collection ?? 0,
    //             'cleaner_collection' => (int) $trip->cleaner_collection ?? 0,
    //             'formatted_created_at' => (string) \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
    //             'total_amount' => (int) $netTotal,
    //             'total_expense' => (int) $totalExpenseAmount,
    //             'net_total' => (int) $total
    //         ];
    //     }

    //     $tripReports = array_slice($tripReports, 0, 10);
    //     return $tripReports;
    // }
    public function lastTenDaysCollectionReport($deviceId)
    {
        // Step 1: First, retrieve the unique start_trip ids for the last 10 trips
        $tripIds = StartTrip::where('device_id', $deviceId)
            ->orderBy('start_trip_date', 'desc')
            ->limit(10) // Limit to the last 10 trips
            ->pluck('id'); // Only get the trip IDs

        if ($tripIds->isEmpty()) {
            return $this->sendError('Trips not found.');
        }

        // Step 2: Fetch aggregated trip details for the retrieved trip IDs
        $tripReports = StartTrip::leftJoin('trips', 'start_trips.id', '=', 'trips.trip_id')
            ->join('routes', 'start_trips.route_id', '=', 'routes.id')
            ->select(
                'start_trips.id',
                'start_trips.trip_name',
                'start_trips.start_trip_date',
                'start_trips.end_trip_date',
                'start_trips.cleaner_collection',
                'trips.start_id',
                'trips.stop_id',
                DB::raw('SUM(trips.full_ticket) as total_full_tickets'),
                DB::raw('SUM(trips.half_ticket) as total_half_tickets'),
                DB::raw('SUM(trips.student_ticket) as total_student_tickets'),
                DB::raw('SUM(trips.language_ticket) as total_language_tickets'),
                DB::raw('SUM(trips.physical_ticket) as total_physical_tickets'),
                DB::raw('SUM(trips.full_ticket * COALESCE(trips.full_ticket_price, 0)) as total_full_ticket_price'),
                DB::raw('SUM(trips.half_ticket * COALESCE(trips.half_ticket_price, 0)) as total_half_ticket_price'),
                DB::raw('SUM(trips.student_ticket * COALESCE(trips.student_ticket_price, 0)) as total_student_ticket_price'),
                DB::raw('SUM(trips.language_ticket * COALESCE(trips.lagguage_ticket_price, 0)) as total_language_ticket_price'),
                DB::raw('SUM(trips.physical_ticket * COALESCE(trips.physical_ticket_price, 0)) as total_physical_ticket_price'),
                DB::raw('SUM(trips.total_amount) as total_amount'),
                DB::raw('SUM(trips.total_expense) as total_expense'),
                DB::raw('SUM(trips.net_total) as net_total'),
                'routes.route_from',
                'routes.route_to'
            )
            ->whereIn('start_trips.id', $tripIds)
            ->groupBy(
                'start_trips.id',
                'start_trips.trip_name',
                'start_trips.start_trip_date',
                'start_trips.end_trip_date',
                'start_trips.cleaner_collection',
                'trips.start_id',
                'trips.stop_id',
                'routes.route_from',
                'routes.route_to'
            )
            ->orderBy('start_trips.start_trip_date', 'desc')
            // ->where('start_trips.id',250)
            ->get(); // Retrieve all trip details

        $tripReportsArray = [];
        $cleanerCollectionAdded = [];

        foreach ($tripReports as $trip) {
            // Fetch the stage data and parse it
            $stage = Stage::where('route_id', $trip->route_id)->first();

            if ($stage) {
                $routeData = $stage->stage_data;
                $stageData = json_decode($routeData ?? '[]', true);
                $trip->start_stage_name = $stageData[$trip->start_id]['stage_name'] ?? null;
                $trip->end_stage_name = $stageData[$trip->stop_id]['stage_name'] ?? null;
            } else {
                // Handle the case where there is no stage data
                $trip->start_stage_name = null;
                $trip->end_stage_name = null;
            }


            // Calculate total ticket price and count
            $totalTicketPrice = $trip->total_full_ticket_price +
                $trip->total_half_ticket_price +
                $trip->total_student_ticket_price +
                $trip->total_language_ticket_price +
                $trip->total_physical_ticket_price;

            $totalTicketCount = $trip->total_full_tickets +
                $trip->total_half_tickets +
                $trip->total_student_tickets +
                $trip->total_language_tickets +
                $trip->total_physical_tickets;

            $totalExpense = TripExpense::where('trip_id', $trip->id)
                ->select('total_expense')
                ->first();

            $totalExpenseAmount = $totalExpense->total_expense ?? 0;

            // Initialize netTotalAmount variable
            $netTotalAmount = $totalTicketPrice - $totalExpenseAmount;

            if (!isset($cleanerCollectionAdded[$trip->id])) {
                $netTotalAmount += ($trip->cleaner_collection ?? 0); // Add cleaner collection only once
                $cleanerCollectionAdded[$trip->id] = true; // Mark as added
            }


            // Check if trip already exists in the array
            if (isset($tripReportsArray[$trip->id])) {
                // Existing trip, increment values
                $tripReportsArray[$trip->id]['full_ticket_count'] += (int) $trip->total_full_tickets;
                $tripReportsArray[$trip->id]['half_ticket_count'] += (int) $trip->total_half_tickets;
                $tripReportsArray[$trip->id]['student_ticket_count'] += (int) $trip->total_student_tickets;
                $tripReportsArray[$trip->id]['language_ticket_count'] += (int) $trip->total_language_tickets;
                $tripReportsArray[$trip->id]['physical_ticket_count'] += (int) $trip->total_physical_tickets;

                // Check if ticket price keys are set, initialize if not
                if (!isset($tripReportsArray[$trip->id]['total_full_ticket_price'])) {
                    $tripReportsArray[$trip->id]['total_full_ticket_price'] = 0;
                }
                if (!isset($tripReportsArray[$trip->id]['total_half_ticket_price'])) {
                    $tripReportsArray[$trip->id]['total_half_ticket_price'] = 0;
                }
                if (!isset($tripReportsArray[$trip->id]['total_student_ticket_price'])) {
                    $tripReportsArray[$trip->id]['total_student_ticket_price'] = 0;
                }
                if (!isset($tripReportsArray[$trip->id]['total_language_ticket_price'])) {
                    $tripReportsArray[$trip->id]['total_language_ticket_price'] = 0;
                }
                if (!isset($tripReportsArray[$trip->id]['total_physical_ticket_price'])) {
                    $tripReportsArray[$trip->id]['total_physical_ticket_price'] = 0;
                }

                // Increment ticket prices
                $tripReportsArray[$trip->id]['total_full_ticket_price'] += $trip->total_full_ticket_price;
                $tripReportsArray[$trip->id]['total_half_ticket_price'] += $trip->total_half_ticket_price;
                $tripReportsArray[$trip->id]['total_student_ticket_price'] += $trip->total_student_ticket_price;
                $tripReportsArray[$trip->id]['total_language_ticket_price'] += $trip->total_language_ticket_price;
                $tripReportsArray[$trip->id]['total_physical_ticket_price'] += $trip->total_physical_ticket_price;

                // Sum the total ticket price across all ticket types
                $totalTicketPrice =
                    ($trip->total_full_ticket_price) +
                    ($trip->total_half_ticket_price) +
                    ($trip->total_student_ticket_price) +
                    ($trip->total_language_ticket_price) +
                    ($trip->total_physical_ticket_price);

                // Update total ticket price and count
                $tripReportsArray[$trip->id]['total_ticket_price'] += (int) $totalTicketPrice;
                $tripReportsArray[$trip->id]['total_tickets'] += (int) $totalTicketCount;

                // Aggregate other totals
                $tripReportsArray[$trip->id]['total_amount'] += (int) $totalTicketPrice;
                $tripReportsArray[$trip->id]['total_expense'] = (int) $totalExpenseAmount;
                $tripReportsArray[$trip->id]['net_total'] += (int) $totalTicketPrice;
            } else {


                // Add new trip report details to the array
                $tripReportsArray[$trip->id] = [
                    'id' => $trip->id,
                    'start_date' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('d M Y') : '',
                    'end_date' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('d M Y') : '',
                    'start_time' => (string) $trip->start_trip_date ? \Carbon\Carbon::parse($trip->start_trip_date)->format('H:i') : '',
                    'end_time' => (string) $trip->end_trip_date ? \Carbon\Carbon::parse($trip->end_trip_date)->format('H:i') : '',
                    'trip_name' => (string) $trip->trip_name,
                    'start_stage_name' => (string) $trip->start_stage_name,
                    'end_stage_name' => (string) $trip->end_stage_name,
                    'full_ticket_count' => (int) $trip->total_full_tickets,
                    'half_ticket_count' => (int) $trip->total_half_tickets,
                    'student_ticket_count' => (int) $trip->total_student_tickets,
                    'language_ticket_count' => (int) $trip->total_language_tickets,
                    'physical_ticket_count' => (int) $trip->total_physical_tickets,
                    'total_full_ticket_price' => (int) $trip->total_full_ticket_price,
                    'total_half_ticket_price' => (int) $trip->total_half_ticket_price,
                    'total_student_ticket_price' => (int) $trip->total_student_ticket_price,
                    'total_language_ticket_price' => (int) $trip->total_language_ticket_price,
                    'total_physical_ticket_price' => (int) $trip->total_physical_ticket_price,
                    'total_ticket_price' => (int) $totalTicketPrice,
                    'total_tickets' => (int) $totalTicketCount,
                    'conductor_collection' => (int) $trip->conductor_collection ?? 0,
                    'cleaner_collection' => (int) $trip->cleaner_collection ?? 0,
                    'formatted_created_at' => (string) \Carbon\Carbon::parse($trip->created_at)->format('d M Y'),
                    'total_amount' => (int) $totalTicketPrice + $trip->cleaner_collection,
                    'total_expense' => (int) $totalExpenseAmount,
                    'net_total' => (int) ($totalTicketPrice + $trip->cleaner_collection) - $totalExpenseAmount
                ];
            }
        }

        // Return the reports as a list
        return array_values($tripReportsArray); // Reindex the array
    }

    public function getRouteForDashboard($deviceId)
    {
        $routes = DB::table('device_route_assignments as da')
            ->join('routes as r', 'da.route_id', '=', 'r.id')
            ->where('da.device_id', $deviceId)
            ->select('r.id', 'r.route_from', 'r.route_to', 'r.created_at', 'r.updated_at')
            ->get();
        return $routes;
    }

    public function getStagesForDashboard($deviceId)
    {
        // Get routes assigned to the device
        $routes = DB::table('device_route_assignments as da')
            ->join('routes as r', 'da.route_id', '=', 'r.id')
            ->where('da.device_id', $deviceId)
            ->select('r.id', 'r.route_from', 'r.route_to')
            ->get();

        if ($routes->isEmpty()) {
            return $this->sendError('No routes found for this device.');
        }

        // Determine the order direction based on status
        $orderDirection = 'asc';
        $combinedStages = [];
        $studentPriceStatus = false;

        foreach ($routes as $route) {
            // Retrieve stages for the main and corresponding student routes
            $mainStage = Stage::where('route_id', $route->id)
                ->orderBy('id', $orderDirection)
                ->first();

            $studentRoute = Route::where('route_from', $route->route_from)
                ->where('route_to', $route->route_to)
                ->where('type', 2)
                ->first();

            $studentStage = $studentRoute ? Stage::where('route_id', $studentRoute->id)
                ->orderBy('id', $orderDirection)
                ->first() : null;

            if (!$mainStage) {
                return $this->sendError('Main stage not found for route ID ' . $route->id);
            }

            // Prepare stages data
            $mainStageData = json_decode($mainStage->stage_data, true);
            $studentStageData = $studentStage ? json_decode($studentStage->stage_data, true) : [];

            $regularStages = $this->prepareStagesData($mainStageData);
            $studentStages = $this->prepareStagesData($studentStageData);

            // Reverse the stages if descending order is needed
            if ($orderDirection === 'desc') {
                $regularStages = array_reverse($regularStages, true);
                $studentStages = array_reverse($studentStages, true);
            }

            // Combine stages for the current route
            $currentCombinedStages = $this->combineStages($regularStages, $studentStages, $orderDirection);
            $combinedStages = array_merge($combinedStages, $currentCombinedStages);

            // Check for student stages
            if ($studentStage) {
                $studentPriceStatus = true;
            }
        }

        return $this->sendResponse([
            'route_id' => $route->id,
            'student_price_status' => $studentPriceStatus,
            'stages' => $combinedStages
        ], 'Stage List with Regular and Student Prices');
    }

    private function prepareStagesData(array $stageData)
    {
        $stages = [];
        foreach ($stageData as $key => $data) {
            $stages[(int)$key] = [
                'stage_name' => $data['stage_name'] ?? 'N/A',
                'price' => !empty($data['prices']) && is_array($data['prices']) ? (int)$data['prices'][0] : 0
            ];
        }
        ksort($stages);
        return $stages;
    }

    private function combineStages(array $regularStages, array $studentStages, string $orderDirection)
    {
        $combinedStages = [];
        foreach ($regularStages as $id => $regularStage) {
            $studentPrice = $studentStages[$id]['price'] ?? 0; // Default to 0 if student stage doesn't exist

            if ($orderDirection === 'asc') {
                $combinedStages[] = [
                    'id' => $id,
                    'stage_name' => $regularStage['stage_name'],
                    'regular_price' => $regularStage['price'],
                    'student_price' => $studentPrice
                ];
            } else {
                $maxRegularPrice = reset($regularStages)['price'] ?? 0;
                $maxStudentPrice = reset($studentStages)['price'] ?? 0;

                $combinedStages[] = [
                    'id' => $id,
                    'stage_name' => $regularStage['stage_name'],
                    'regular_price' => $maxRegularPrice - $regularStage['price'],
                    'student_price' => $maxStudentPrice - $studentPrice
                ];
            }
        }
        return $combinedStages;
    }

    public function offlineStartTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trips' => 'required|array',
            'trips.*.route_id' => 'required|exists:routes,id',
            'trips.*.device_id' => 'required|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $tripsData = $request->input('trips');
        $createdTrips = [];

        foreach ($tripsData as $tripData) {
            // Create a new StartTrip record for each trip
            $startTrip = new StartTrip([
                'route_id' => $tripData['route_id'],
                'device_id' => $tripData['device_id'],
                'start_trip_date' => Carbon::now(),
                'status' => AdminConstants::STATUS_ACTIVE,
            ]);

            $tripName = $this->generateUniqueTripName();
            $startTrip->trip_name = $tripName;
            $startTrip->save();

            $responseData = [
                'id' => (int) $startTrip->id,
                'trip_name' => (string) $startTrip->trip_name,
                'route_id' => (string) $startTrip->route_id,
                'device_id' => (string) $startTrip->device_id,
                'start_trip_date' => (string) $startTrip->start_trip_date->toDateTimeString(),
                'status' => (int) $startTrip->status,
                'created_at' => (string) $startTrip->created_at,
                'updated_at' => (string) $startTrip->updated_at,
            ];

            $createdTrips[] = $responseData;
        }

        return $this->sendResponse($createdTrips, 'Trips started successfully.');
    }


    public function offlineEndTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trips' => 'required|array', // Validate that trips is an array
            'trips.*.trip_id' => 'required|exists:start_trips,id', // Validate each trip_id
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $tripsData = $request->input('trips');
        $endedTrips = [];

        foreach ($tripsData as $tripData) {
            $startTrip = StartTrip::find($tripData['trip_id']);

            if (!$startTrip) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip not found.',
                ], 404);
            }

            if ($startTrip->status != AdminConstants::STATUS_ACTIVE) {
                return response()->json([
                    'status' => false,
                    'message' => 'This trip has already been ended.',
                ], 400);
            }

            $startTrip->status = AdminConstants::STATUS_INACTIVE;
            $startTrip->end_trip_date = Carbon::now();
            $startTrip->save();

            $response = [
                'id' => (int) $startTrip->id,
                'start_trip_date' => $startTrip->start_trip_date ? (string) Carbon::parse($startTrip->start_trip_date)->format('Y-m-d') : '',
                'end_trip_date' => $startTrip->end_trip_date ? (string) Carbon::parse($startTrip->end_trip_date)->format('Y-m-d') : '',
                'device_id' => (string) $startTrip->device_id,
                'route_id' => (string) $startTrip->route_id,
                'trip_name' => (string) $startTrip->trip_name,
                'status' => (int) $startTrip->status,
                'cleaner_collection' => $startTrip->cleaner_collection ?? null,
                'created_at' => (string) $startTrip->created_at,
                'updated_at' => (string) $startTrip->updated_at,
            ];

            $endedTrips[] = $response;
        }

        return $this->sendResponse($endedTrips, 'Trips ended successfully.');
    }

    public function getAllStages(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|string',
            'route_status' => 'required|string',
        ]);

        // Return validation error if it fails
        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        // Fetch all stages for the provided route_id
        $stages = Stage::where('route_id', $request->input('route_id'))->first();

        // Check if stages exist
        if (!$stages) {
            return $this->sendError('No stages found for the given route.');
        }

        // Decode fare data from the stages
        $fareData = json_decode($stages->stage_data, true); // Decode as associative array

        // Initialize studentFareData to null
        $studentFareData = null;

        // Fetch student route based on route_from and route_to
        $route = Route::find($request->route_id);
        $studentRoute = Route::where('route_from', $route->route_from)
            ->where('route_to', $route->route_to)
            ->where('type', 2)
            ->first();

        // Fetch student stage data if studentRoute exists
        if ($studentRoute) {
            $studentStage = Stage::where('route_id', $studentRoute->id)->first();

            if ($studentStage) {
                $studentFareData = json_decode($studentStage->stage_data, true);
            }
        }

        // Prepare fare data for response, ensuring consistent structure
        $preparedFareData = $this->prepareFareData($fareData, $studentFareData);

        // Logic for handling route status
        if ($request->input('route_status') == '1') {
            // If route_status is 1, return fareData in the prepared format
            $data = [
                'route_id' => $stages->route_id,
                'fare' => $preparedFareData,
            ];
        } else {
            // If route_status is 2, reverse the fare data
            $reversedData = $this->reverseFareData($fareData, $studentFareData);

            // Prepare data for response
            $data = [
                'route_id' => $stages->route_id,
                'fare' => $reversedData,
            ];
        }

        // Return the formatted stages
        return $this->sendResponse($data, 'Stages retrieved successfully.');
    }

    // Method to prepare fare data including student prices
    private function prepareFareData($fareData, $studentFareData = null)
    {
        $preparedData = [];
        $ids = array_keys($fareData);

        // Loop through the fare data to create the desired structure
        foreach ($ids as $id) {
            $preparedData[$id] = [
                'stage_name' => $fareData[$id]['stage_name'], // Keep the original stage name
                'prices' => $fareData[$id]['prices'], // Keep original prices
                'student_prices' => $studentFareData[$id]['prices'] ?? null, // Add student prices if available
            ];
        }

        return $preparedData;
    }

    // Method to reverse fare data and include student prices
    private function reverseFareData($fareData, $studentFareData = null)
    {
        $reversedData = [];
        $ids = array_keys($fareData);

        // Loop through the fare data to create the reversed structure
        for ($i = count($ids) - 1; $i >= 0; $i--) {
            $id = $ids[$i];
            // Get the corresponding original prices for the opposite index
            $oppositeId = $ids[count($ids) - 1 - $i]; // Calculate the opposite ID

            // Create the reversed data with swapped prices
            $reversedData[$id] = [
                'stage_name' => $fareData[$id]['stage_name'], // Keep the original stage name
                'prices' => $fareData[$oppositeId]['prices'], // Use prices from the opposite ID
                'student_prices' => $studentFareData[$oppositeId]['prices'] ?? null, // Add student prices if available
            ];
        }

        return $reversedData;
    }

    public function manageOfflineTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trips' => 'required|array',
            'trips.*.trip_name' => 'required|string',
            'trips.*.route_id' => 'required|exists:routes,id',
            'trips.*.device_id' => 'required|exists:devices,id',
            'trips.*.tickets' => 'required|array',
            'trips.*.tickets.*.trip_id' => 'required|string',
            'trips.*.tickets.*.start_day_id' => 'required|string',
            'trips.*.tickets.*.device_id' => 'required|string|exists:devices,id',
            'trips.*.tickets.*.route_id' => 'required|string|exists:routes,id',
            'trips.*.tickets.*.start_id' => 'required|string',
            'trips.*.tickets.*.stop_id' => 'required|string',
            'trips.*.tickets.*.route_status' => 'required|string',
            'trips.*.tickets.*.full_ticket' => 'nullable|integer|min:0',
            'trips.*.tickets.*.half_ticket' => 'nullable|integer|min:0',
            'trips.*.tickets.*.student_ticket' => 'nullable|integer|min:0',
            'trips.*.tickets.*.lagguage_ticket' => 'nullable|integer|min:0',
            'trips.*.tickets.*.physical_ticket' => 'nullable|integer|min:0',
            'trips.*.tickets.*.full_ticket_price' => 'nullable|numeric|min:0',
            'trips.*.tickets.*.half_ticket_price' => 'nullable|numeric|min:0',
            'trips.*.tickets.*.student_ticket_price' => 'nullable|numeric|min:0',
            'trips.*.tickets.*.lagguage_ticket_price' => 'nullable|numeric|min:0',
            'trips.*.tickets.*.physical_ticket_price' => 'nullable|numeric|min:0',
            'trips.*.tickets.*.date' => 'nullable|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $createdTrips = [];

        foreach ($request->trips as $tripData) {
            $existingTrip = StartTrip::where('trip_name', $tripData['trip_name'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$existingTrip) {
                $startTrip = new StartTrip([
                    'route_id' => $tripData['route_id'],
                    'device_id' => $tripData['device_id'],
                    'start_trip_date' => Carbon::now(),
                    'status' => AdminConstants::STATUS_ACTIVE,
                    'trip_name' => $tripData['trip_name']
                ]);
                $previousTrips = StartTrip::where('device_id', $tripData['device_id'])
                    ->where('trip_name', '!=', $tripData['trip_name'])
                    ->where('status', AdminConstants::STATUS_ACTIVE)
                    ->update(['status' => 2]);
                $startTrip->save();
            } else {
                $startTrip = $existingTrip;

                if ($startTrip->status != AdminConstants::STATUS_ACTIVE) {
                    return $this->sendError('This trip is not active for trip name: ' . $tripData['trip_name']);
                }
            }

            foreach ($tripData['tickets'] as $ticketData) {
                $existingTrips = Trip::all();

                $highestNumber = 0;

                foreach ($existingTrips as $existingTrip) {
                    preg_match('/\d+/', $existingTrip->trip_name, $matches);
                    if (!empty($matches)) {
                        $ticketNumber = (int)$matches[0];
                        if ($ticketNumber > $highestNumber) {
                            $highestNumber = $ticketNumber;
                        }
                    }
                }

                $nextNumber = $highestNumber + 1;

                $tripName = sprintf('TICKET-%03d', $nextNumber);

                $stage = Stage::where('route_id', $ticketData['route_id'])->first();
                if (!$stage) {
                    return $this->sendError('No stage data found for the provided route for trip: ' . $tripData['trip_name']);
                }

                $fullTickets = $ticketData['full_ticket'] ?? 0;
                $halfTickets = $ticketData['half_ticket'] ?? 0;
                $studentTickets = $ticketData['student_ticket'] ?? 0;
                $luggageTickets = $ticketData['lagguage_ticket'] ?? 0;
                $physicalTickets = $ticketData['physical_ticket'] ?? 0;

                $totalFullTicketPrice = $fullTickets * $ticketData['full_ticket_price'];
                $totalHalfTicketPrice = $halfTickets * $ticketData['half_ticket_price'];
                $totalStudentTicketPrice = $studentTickets * $ticketData['student_ticket_price'];
                $totalLuggageTicketPrice = $luggageTickets * $ticketData['lagguage_ticket_price'];
                $totalPhysicalTicketPrice = $physicalTickets * $ticketData['physical_ticket_price'];

                $totalAmount = $totalFullTicketPrice + $totalHalfTicketPrice + $totalStudentTicketPrice + $totalLuggageTicketPrice + $totalPhysicalTicketPrice;
                $totalExpense = 0;
                $netTotal = $totalAmount - $totalExpense;

                $trip = Trip::create([
                    'trip_id' => $startTrip['id'],
                    'start_day_id' => $ticketData['start_day_id'],
                    'device_id' => $ticketData['device_id'],
                    'stage_id' => $stage->id,
                    'trip_name' => $tripName,
                    'start_id' => $ticketData['start_id'],
                    'stop_id' => $ticketData['stop_id'],
                    'full_ticket' => $fullTickets,
                    'half_ticket' => $halfTickets,
                    'student_ticket' => $studentTickets,
                    'luggage_ticket' => $luggageTickets,
                    'physical_ticket' => $physicalTickets,
                    'total_amount' => $totalAmount,
                    'total_expense' => $totalExpense,
                    'net_total' => $netTotal,
                    'route_status' => $ticketData['route_status'],
                    'full_ticket_price' => $ticketData['full_ticket_price'],
                    'half_ticket_price' => $ticketData['half_ticket_price'],
                    'student_ticket_price' => $ticketData['student_ticket_price'],
                    'lagguage_ticket_price' => $ticketData['lagguage_ticket_price'],
                    'physical_ticket_price' => $ticketData['physical_ticket_price'],
                    'created_at' => $ticketData['date'] ?? Carbon::now()
                ]);

                $createdTrips[] = $trip;
            }
        }

        return $this->sendResponse($createdTrips, 'Trips managed successfully.');
    }
}
