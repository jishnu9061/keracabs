<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Helpers\Utilities\ToastrHelper;

class AdminRegistrationController extends Controller
{
    public function index()
    {
        $path = $this->getView('admin.register.index');
        $registers = Registration::select('id', 'name', 'number', 'vehicle_type', 'seating_capacity','vehicle_number','parking_location','district','vehicle_photo','driver_image')->get();
        $para = ['registers' => $registers];
        $title = 'Registers';
        return $this->renderView($path, $para, $title);
    }

    public function delete(Registration $registration)
    {
        $registration->delete();
        ToastrHelper::success('Driver deleted successfully');
        return Response::json(['success' => 'Driver Deleted Successfully']);
    }
}
