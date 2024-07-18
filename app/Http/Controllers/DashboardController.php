<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/09
 * Time: 15:50:20
 * Description: DashboardController.php
 */

namespace App\Http\Controllers;

use App\Models\Booking;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    /**
     * @param DashboardService $dashboardService
     *
     * @return [type]
     */
    public function index(DashboardService $dashboardService)
    {
        $path = $this->getView('admin.dashboard');
        $counts = $dashboardService->getDashboardCounts();
        $bookings = Booking::select('id', 'name', 'phone', 'email', 'vehicle', 'message', 'created_at')->get();
        $para = ['bookings' => $bookings, 'counts' => $counts];
        $title = 'Dashboard';
        return $this->renderView($path, $para, $title);
    }
}
