<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Device;
use App\Models\Manager;
use Illuminate\Support\Facades\DB;

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

    public function getTicketCount()
    {
        return Trip::select(
            DB::raw('SUM(full_ticket) as total_full_ticket'),
            DB::raw('SUM(half_ticket) as total_half_ticket'),
            DB::raw('SUM(student_ticket) as total_student_ticket'),
            DB::raw('SUM(language_ticket) as total_language_ticket'),
            DB::raw('SUM(physical_ticket) as total_physical_ticket'),
            DB::raw('SUM(total_amount) as total_amount_sum'),
            DB::raw('SUM(total_expense) as total_expense_sum'),
            DB::raw('SUM(net_total) as net_total_sum'),
            DB::raw('SUM(full_ticket + half_ticket + student_ticket + language_ticket + physical_ticket) as total_tickets')
        )->first();
    }
}
