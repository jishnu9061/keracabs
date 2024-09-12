<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/08/24
 * Time: 12:32:19
 * Description: AdminDeviceController.php
 */

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use App\Models\Device;

use App\Models\Manager;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\DeviceStoreRequest;
use App\Http\Requests\DeviceUpdateRequest;
use App\Http\Helpers\Utilities\ToastrHelper;

class AdminDeviceController extends Controller
{
    public function index(Manager $manager)
    {
        $devices = Device::where('manager_id', $manager->id)
            ->select('id', 'manager_id', 'route_id', 'user_name', 'password', 'logo', 'header_one', 'header_two', 'footer')
            ->get();

        $routes = Route::select('id', 'route_from', 'route_to')->get();

        return $this->renderView(
            $this->getView('admin.manager-device.index'),
            ['devices' => $devices, 'manager' => $manager, 'routes' => $routes],
            'Devices'
        );
    }

    public function store(DeviceStoreRequest $request)
    {
        $device = Device::create([
            'manager_id' => $request->manager_id,
            'user_name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
            'footer' => $request->input('footer'),
            'header_one' => $request->input('header_one'),
            'header_two' => $request->input('header_two')
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            if ($image) {
                $filePath = Storage::disk('public')->put('device', $image);
                $fileUrl = Storage::disk('public')->url($filePath);
                $device->logo = basename($filePath);
                $device->save();
            }
        }

        return Response::json(['success' => true]);
    }

    public function edit(Device $device)
    {
        $path = $this->getView('admin.manager-device.edit');
        $para = ['device' => $device];
        $title = 'Edit Manager';
        return $this->renderView($path, $para, $title);
    }

    public function update(Device $device, DeviceUpdateRequest $request)
    {
        $device->update([
            'user_name' => $request->input('user_name'),
            'password' => bcrypt($request->input('password')),
            'header_one' => $request->input('header_one'),
            'header_two' => $request->input('header_two'),
            'footer' => $request->input('footer')
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            if ($image) {
                $filePath = Storage::disk('public')->put('device', $image);
                $fileUrl = Storage::disk('public')->url($filePath);
                $device->logo = basename($filePath);
                $device->save();
            }
        }

        ToastrHelper::success('Device updated successfully');
        return redirect()->route('manager-device.index', $device->manager_id);
    }

    public function delete(Device $device)
    {
        $device->delete();
        ToastrHelper::success('Device deleted successfully');
        return Response::json(['success' => true]);
    }

    public function managerAssign(Request $request)
    {
        $device = Device::find($request->entity_id);
        foreach ($request->route_id as $routeId) {
            DB::table('device_route_assignments')->updateOrInsert(
                ['device_id' => $device->id, 'route_id' => $routeId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
        return redirect()->route('manager-device.index', $device->manager_id)
            ->with('success', 'Routes assigned successfully.');
    }

    public function resetDevice(Device $device)
    {
        $device->route_id = null;
        $device->save();
        return redirect()->back();
    }
}
