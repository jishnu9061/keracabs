<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Encryption\DecryptException;

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
        }

        $credentials = $request->only('user_name', 'password');

        $device = Device::where('user_name', $request->user_name)->first();

        if ($device && $device->is_logged_in) {
            return response()->json([
                'status' => false,
                'message' => 'Device is already logged in from another session',
            ], 403);
        }

        if (Auth::guard('device')->attempt($credentials)) {
            $device = Auth::guard('device')->user();

            $device->update(['is_logged_in' => true]);

            $token = $device->createToken('Device API Token')->accessToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'device' => $device
            ], 200);
        } else {
            return $this->sendError('Invalid username or password', $validator->errors()->toArray());
        }
    }

    public function qrLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'qrcode' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $decryptString = Crypt::decryptString($request->qrcode);
            $result = unserialize($decryptString);

            $user = Device::where('user_name', $result['user_name'])->first();

            if (!$user || !Hash::check($result['password'], $user->password)) {
                return $this->sendError('Username & Password do not match our records.');
            }
            $user->is_logged_in = true;
            $user->save();
            return $this->sendResponse($user, 'User Logged In Successfully');
        } catch (DecryptException $e) {
            return $this->sendError('Invalid QR code format.');
        } catch (Exception $e) {
            return $this->sendError('An error occurred.', $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $user->update(['is_logged_in' => false]);
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function ownerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid data', $validator->errors()->toArray());
        }

        $credentials = $request->only('user_name', 'password');

        // Check if the owner exists
        $owner = Manager::where('user_name', $request->user_name)->first();

        if (!$owner) {
            return response()->json([
                'status' => false,
                'message' => 'Owner not found',
            ], 403);
        }

        // Verify the password
        if (Hash::check($request->password, $owner->password)) {
            Auth::login($owner);
            // Generate a token for the owner
            $token = $owner->createToken('Manager API Token')->accessToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'manager' => $owner
            ], 200);
        } else {
            return $this->sendError('Invalid username or password', []);
        }
    }

    public function ownerLogout(Request $request)
    {
        $owner = Auth::guard('manager')->user();

        if ($owner) {
            $owner->token()->revoke();

            return response()->json([
                'status' => true,
                'message' => 'Logout successful',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }
}
