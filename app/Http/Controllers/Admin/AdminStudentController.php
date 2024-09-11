<?php

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Constants\AdminConstants;

class AdminStudentController extends Controller
{
    public function index()
    {
        $path = $this->getView('admin.student.index');
        $studentFee = StudentFee::first();
        $para = ['studentFee' => $studentFee];
        $title = 'Student';
        return $this->renderView($path, $para, $title);
    }

    public function store(Request $request)
    {
        $request->validate([
            'stages' => 'required|array',
            'stages.*.stage_name' => 'required|string',
            'stages.*.prices' => 'required|array',
            'stages.*.prices.*' => 'required|numeric'
        ]);

        $stages = $request->input('stages');

        $combinedData = array_map(function ($stage) {
            return [
                'stage_name' => $stage['stage_name'],
                'prices' => $stage['prices']
            ];
        }, $stages);

        $formattedData = json_encode($combinedData);

        $existingRecord = DB::table('student_fees')->first();

        if ($existingRecord) {
            DB::table('student_fees')
                ->update([
                    'price_data' => $formattedData,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('student_fees')->insert([
                'price_data' => $formattedData,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return redirect()->route('student.index');
    }
}
