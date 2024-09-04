<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/09
 * Time: 15:50:20
 * Description: DashboardController.php
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Manager;

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
        $managers = Manager::select('id','name','user_name','password','contact')->get();
        $para = ['counts'=>$counts,'managers' => $managers];
        $title = 'Dashboard';
        return $this->renderView($path, $para, $title);
    }
}
