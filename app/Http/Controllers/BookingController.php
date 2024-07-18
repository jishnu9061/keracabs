<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/12
 * Time: 14:45:27
 * Description: BookingController.php
 */

namespace App\Http\Controllers;

use App\Http\Constants\FileDestinations;

use App\Models\Booking;

use Illuminate\Support\Facades\Response;
use App\Http\Helpers\Utilities\ToastrHelper;

class BookingController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.booking.index');
        $bookings = Booking::select('id', 'name', 'phone', 'email', 'vehicle', 'message', 'created_at')->get();
        $para = ['bookings' => $bookings];
        $title = 'Bookings';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param Booking $booking
     *
     * @return [type]
     */
    public function delete(Booking $booking)
    {
        $booking->delete();
        ToastrHelper::success('Booking deleted successfully');
        return Response::json(['success' => 'Booking Deleted Successfully']);
    }
}
