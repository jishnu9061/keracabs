<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/13
 * Time: 13:43:58
 * Description: WebBookingController.php
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Booking;

use App\Mail\BookingMail;

use App\Http\Requests\SendBookingStoreRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class WebBookingController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('web.booking');
        $para = [];
        $title = 'Booking';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param SendBookingStoreRequest $request
     *
     * @return [type]
     */
    public function sendBooking(SendBookingStoreRequest $request)
    {
        $booking = Booking::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->number,
            'vehicle' => $request->vehicle,
            'message' => $request->message,
        ]);
        Mail::to('jishnuganesh27@gmail.com')->send(new BookingMail($booking));
        return Response::json(['success' => true]);
    }
}
