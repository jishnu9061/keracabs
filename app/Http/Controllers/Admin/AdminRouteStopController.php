<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;

class AdminRouteStopController extends Controller
{
    // public function index(Route $route)
    // {
    //     $path = $this->getView('admin.stop.index');
    //     $stagesData = Stage::where('route_id', $route->id)->first();

    //     $stages = [];
    //     if ($stagesData && !empty($stagesData->stage_data)) {
    //         $decodedData = json_decode($stagesData->stage_data, true);
    //         if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
    //             foreach ($decodedData as $key => $value) {
    //                 if (is_array($value) && isset($value['stage_name']) && isset($value['prices'])) {
    //                     $stages[$key] = $value;
    //                 }
    //             }
    //         }
    //     }

    //     $para = ['route' => $route, 'existingStages' => $stages];
    //     $title = 'Route Stops';
    //     return $this->renderView($path, $para, $title);
    // }
    // public function index(Route $route)
    // {
    //     $path = $this->getView('admin.stop.index');
    //     $stagesData = Stage::where('route_id', $route->id)->first();

    //     $stages = [];
    //     if ($stagesData && !empty($stagesData->stage_data)) {
    //         $decodedData = json_decode($stagesData->stage_data, true);
    //         if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
    //             foreach ($decodedData as $key => $value) {
    //                 if (is_array($value) && isset($value['prices'])) {
    //                     $stages[$key] = [
    //                         'stage_name' => $value['stage_name'] ?? null,
    //                         'prices' => $value['prices']
    //                     ];
    //                 }
    //             }
    //         }
    //     }

    //     $para = ['route' => $route, 'existingStages' => $stages];
    //     $title = 'Route Stops';
    //     return $this->renderView($path, $para, $title);
    // }
    public function index(Route $route)
    {
        $path = $this->getView('admin.stop.index');
        $stagesData = Stage::where('route_id', $route->id)->first();
        if ($stagesData && !empty($stagesData->stage_data)) {
            $decodedData = json_decode($stagesData->stage_data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                $stages = collect(); // Initialize as a collection
                foreach ($decodedData as $key => $value) {
                    if (is_array($value) && isset($value['prices'])) {
                        $stages->push([
                            'stage_name' => $value['stage_name'] ?? null,
                            'prices' => $value['prices'],
                        ]);
                    }
                }
            }
        }
        $para = ['route' => $route, 'existingStages' => $stages];
        $title = 'Route Stops';
        return $this->renderView($path, $para, $title);
    }



    public function create(Route $route)
    {
        $path = $this->getView('admin.stop.create');
        $para = ['route' => $route];
        $title = 'Route Stops';
        return $this->renderView($path, $para, $title);
    }

    public function store(Request $request, Route $route)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sequence' => 'required|integer|unique:route_stops,stop_sequence',
            'price' => 'required|integer|max:255',
        ]);

        RouteStop::create([
            'route_id' => $route->id,
            'stop_name' => $request->name,
            'stop_sequence' => $request->sequence,
            'price' => $request->price
        ]);

        return redirect()->route('stop.index', $route->id);
    }

    public function edit(RouteStop $routeStop)
    {
        $path = $this->getView('admin.stop.edit');
        $para = ['routeStop' => $routeStop];
        $title = 'Route Stops Edit';
        return $this->renderView($path, $para, $title);
    }

    public function update(Request $request, RouteStop $routeStop)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|max:255',
            'sequence' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('route_stops', 'stop_sequence')->ignore($routeStop->id),
            ],
        ]);
        $routeStop->stop_name = $validatedData['name'];
        $routeStop->stop_sequence = $validatedData['sequence'];
        $routeStop->price = $validatedData['price'];
        $routeStop->save();
        return redirect()->route('stop.index', $routeStop->route_id);
    }

    public function delete(RouteStop $routeStop)
    {
        $routeStop->delete();
        return Response::json(['success' => true]);
    }
}
