<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Helpers\Utilities\ToastrHelper;

use App\Models\Route;

use App\Http\Requests\RouteStoreRequest;
use Illuminate\Support\Facades\Response;

use App\Http\Requests\RouteUpdateRequest;

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
        Route::create([
            'route_from' => $request->input('route_from'),
            'route_to' => $request->input('route_to'),
            'minimum_charge' => $request->input('min_charge'),
            'type' => $request->input('charge_type'),
        ]);
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
