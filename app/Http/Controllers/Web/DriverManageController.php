<?php

namespace App\Http\Controllers\Web;

use App\Models\Registration;
use Illuminate\Http\Request;
use App\Mail\RegistrationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Helpers\Core\FileManager;
use App\Http\Constants\FileDestinations;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\SendRegistartionStoreRequest;
use App\Mail\DriverMail;
use App\Mail\DriverRegistration;

class DriverManageController extends Controller
{
    public function index()
    {
        $path = $this->getView('web.driver');
        $para = [];
        $title = 'Driver';
        return $this->renderView($path, $para, $title);
    }

    public function sendRegistration(SendRegistartionStoreRequest $request)
    {
        $registration = Registration::create([
            'name' => $request->name,
            'number' => $request->number,
            'vehicle_type' => $request->vehicle_type,
            'seating_capacity' => $request->seating_capacity,
            'vehicle_number' => $request->vehicle_number,
            'parking_location' => $request->parking_location,
            'district' => $request->district,
            'whatsapp_number'=>$request->whatsapp_number
        ]);
        if ($request->hasFile('vehicle_photo')) {
            $res = FileManager::upload(FileDestinations::VEHICLE_PHOTO, 'vehicle_photo', FileManager::FILE_TYPE_IMAGE);
            if ($res['status']) {
                $registration->vehicle_photo = $res['data']['fileName'];
                $registration->save();
            }
        }
        if ($request->hasFile('driver_image')) {
            $res = FileManager::upload(FileDestinations::DRIVER_IMAGE, 'driver_image', FileManager::FILE_TYPE_IMAGE);
            if ($res['status']) {
                $registration->driver_image = $res['data']['fileName'];
                $registration->save();
            }
        }
        $registrationData = $registration->toArray();

        Mail::to('keracabs7@gmail.com')->send(new DriverMail($registrationData));
        return Response::json(['success' => true]);
    }
}
