<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Route;
use App\Models\Device;
use App\Models\Manager;

class DashboardService
{
    public function getDashboardCounts()
    {
        $now = Carbon::now();
        $oneWeekAgo = $now->subWeek();

        return [
            'manager' => [
                'total' => Manager::count(),
                'last_week' => Manager::where('created_at', '>=', $oneWeekAgo)->count()
            ],
            'device' => [
                'total' => Device::count(),
                'last_week' => Device::where('created_at', '>=', $oneWeekAgo)->count()
            ],
            'route' => [
                'total' => Route::count(),
                'last_week' => Route::where('created_at', '>=', $oneWeekAgo)->count()
            ],
        ];
    }
}
