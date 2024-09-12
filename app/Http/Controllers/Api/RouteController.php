<?php

namespace App\Http\Controllers\Api;

use App\Models\Route;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\DeviceRouteAssignment;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiBaseController;

class RouteController extends ApiBaseController
{
    public function getDeviceRoute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:routes,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        } else {
            $routes = DB::table(DeviceRouteAssignment::getTableName() . ' as dra')
                ->join(Route::getTableName() . ' as r', 'dra.route_id', '=', 'r.id')
                ->where('dra.device_id', $request->device_id)
                ->select('r.id', 'r.route_from', 'r.route_to', 'r.created_at', 'r.updated_at')
                ->get();
            return $this->sendResponse($routes, 'Route List');
        }
    }

    public function getStages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|string|exists:routes,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $stage = Stage::where('route_id', $request->route_id)->first();

        if (!$stage) {
            return $this->sendError('Stage not found');
        }

        $stageData = json_decode($stage->stage_data, true);

        $formattedStages = [];

        foreach ($stageData as $key => $data) {
            $formattedStages[] = [
                'id' => (int) $key,
                'stage_name' => $data['stage_name'],
            ];
        }

        return $this->sendResponse($formattedStages, 'Stage List');
    }
}
