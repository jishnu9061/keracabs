<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\QrCodeModel;
use Illuminate\Http\Request;
use BaconQrCode\Encoder\QrCode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Utilities\ToastrHelper;

class QrCodeController extends Controller
{
    public function index()
    {
        $path = $this->getView('admin.qr.index');
        $devices = Device::select('id','user_name')->get();
        $qrCode = QrCodeModel::first();
        $para = ['qrCode' => $qrCode];
        $title = 'Edit Qr';
        return $this->renderView($path, $para, $title);
    }

    public function updateQrCode(Request $request, QrCodeModel $qrCodeModel)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image) {
                $filePath = Storage::disk('public')->put('qr', $image);
                $fileUrl = Storage::disk('public')->url($filePath);
                $qrCodeModel->image = basename($filePath);
                $qrCodeModel->save();
            }
        }
        ToastrHelper::success('Qr code updated successfully');
        return redirect()->route('qr.index');
    }
}
