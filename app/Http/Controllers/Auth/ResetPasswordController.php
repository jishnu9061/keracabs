<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    protected function validationErrorMessages()
    {
        return [];
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return redirect()->route('login')->with('status', trans($response));
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Attempt to reset the user's password
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // Handle the response
        if ($response == Password::PASSWORD_RESET) {
            // Password successfully reset
            return redirect()->route('login')->with('status', trans($response));
        } else {
            // Password reset failed
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }
    }

    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->save();
    }

    public function broker()
    {
        return Password::broker('admins'); // Use 'admins' instead of the default 'users'
    }
}
