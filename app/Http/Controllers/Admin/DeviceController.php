<?php

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Helpers\Utilities\ToastrHelper;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::select('devices.id', 'devices.manager_id', 'devices.route_id', 'devices.user_name', 'devices.password', 'devices.logo', 'devices.header_one', 'devices.header_two', 'devices.footer', 'managers.name','managers.id as manager_id')
            ->join('managers', 'devices.manager_id', '=', 'managers.id')
            ->get();

        $routes = Route::select('id', 'route_from', 'route_to')->get();

        return $this->renderView(
            $this->getView('admin.device.index'),
            ['devices' => $devices, 'routes' => $routes],
            'Devices'
        );
    }

    public function delete(Device $device)
    {
        $device->delete();
        ToastrHelper::success('Device deleted successfully');
        return Response::json(['success' => true]);
    }
}
