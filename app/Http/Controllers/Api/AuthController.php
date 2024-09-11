<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiBaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:255',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        } else {
            $credentials = $request->only('user_name', 'password');
            if (Auth::guard('manager')->attempt($credentials)) {
                $manager = Auth::guard('manager')->user();

                $token = $manager->createToken('Manager API Token')->accessToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'manager' => $manager
                ], 200);
            } else {
                return $this->sendError('Invalid username or password', $validator->errors()->toArray());
            }
        }
    }

    public function homePage()
    {
        dd('hi');
    }
}

