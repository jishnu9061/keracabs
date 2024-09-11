<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminStageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'stages' => 'required|array',
            'stages.*.stage_name' => 'required|string',
            'stages.*.prices' => 'required|array',
            'stages.*.prices.*' => 'required|numeric'
        ]);

        $routeId = $request->input('route_id');
        $stages = $request->input('stages');

        // Convert stages data to JSON
        $newStageData = json_encode($stages);

        // Check if existing stage data for the route
        $existingStage = DB::table('stages')
            ->where('route_id', $routeId)
            ->first();

        if ($existingStage) {
            // Update existing stage data
            DB::table('stages')
                ->where('route_id', $routeId)
                ->update([
                    'stage_data' => $newStageData,
                    'updated_at' => now()
                ]);
        } else {
            // Create new entry
            DB::table('stages')->insert([
                'route_id' => $routeId,
                'stage_data' => $newStageData,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('stop.index', $routeId);
    }
}
