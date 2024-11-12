<?php

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Constants\AdminConstants;

use App\Http\Constants\FileDestinations;

use App\Http\Requests\RouteStoreRequest;
use Illuminate\Support\Facades\Response;

use App\Http\Requests\RouteUpdateRequest;
use App\Http\Helpers\Utilities\ToastrHelper;
use App\Models\FareFee;
use App\Models\StudentFee;

class AdminRouteController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.route.index');
        $routes = Route::select('id', 'device_id', 'route_from', 'route_to', 'type')->get();
        $para = ['routes' => $routes];
        $title = 'Routes';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param RouteStoreRequest $request
     *
     * @return [type]
     */
    public function store(RouteStoreRequest $request)
    {
        $route = Route::create([
            'route_from' => $request->input('route_from'),
            'route_to' => $request->input('route_to'),
            'minimum_charge' => $request->input('min_charge'),
            'type' => $request->input('charge_type'),
        ]);

        $chargeType = $request->input('charge_type');
        $feeData = ($chargeType == AdminConstants::ROUTE_TYPE_FAIR) ? FareFee::first() : StudentFee::first();

        $priceData = json_decode($feeData->price_data, true);
        if (is_array($priceData)) {
            foreach ($priceData as &$entry) {
                $entry['stage_name'] = null;
            }
        } else {
            $priceData = [];
        }

        $priceDataJson = json_encode($priceData);

        $existingStage = DB::table('stages')->where('route_id', $route->id)->first();
        if ($existingStage) {
            DB::table('stages')
                ->where('route_id', $route->id)
                ->update([
                    'stage_data' => $priceDataJson,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('stages')->insert([
                'route_id' => $route->id,
                'stage_data' => $priceDataJson,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        ToastrHelper::success('Route created successfully');
        return Response::json(['success' => true]);
    }

    /**
     * @param Route $route
     *
     * @return [type]
     */
    public function edit(Route $route)
    {
        $path = $this->getView('admin.route.edit');
        $para = ['route' => $route];
        $title = 'Edit Route';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param Manager $manager
     * @param ManagerUpdateRequest $request
     *
     * @return [type]
     */
    public function update(Route $route, RouteUpdateRequest $request)
    {
        $route->update([
            'route_from' => $request->input('route_from'),
            'route_to' => $request->input('route_to'),
            'minimum_charge' => $request->input('min_charge'),
            'type' => $request->input('charge_type'),
        ]);

        $chargeType = $request->input('charge_type');
        $feeData = ($chargeType == AdminConstants::ROUTE_TYPE_FAIR) ? FareFee::first() : StudentFee::first();

        $priceData = json_decode($feeData->price_data, true);
        if (is_array($priceData)) {
            foreach ($priceData as &$entry) {
                $entry['stage_name'] = null;
            }
        } else {
            $priceData = [];
        }

        $priceDataJson = json_encode($priceData);

        $existingStage = DB::table('stages')->where('route_id', $route->id)->first();
        if ($existingStage) {
            DB::table('stages')
                ->where('route_id', $route->id)
                ->update([
                    'stage_data' => $priceDataJson,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('stages')->insert([
                'route_id' => $route->id,
                'stage_data' => $priceDataJson,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        ToastrHelper::success('Route updated successfully');
        return redirect()->route('route.index');
    }

    /**
     * @param Route $route
     *
     * @return [type]
     */
    public function delete(Route $route)
    {
        $route->delete();
        ToastrHelper::success('Route deleted successfully');
        return Response::json(['success' => true]);
    }
}
