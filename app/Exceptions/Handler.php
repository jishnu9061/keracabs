<?php

namespace App\Exceptions;

use Closure;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Check if the request has an Authorization header
        if ($request->hasHeader('Authorization')) {
            $authHeader = $request->header('Authorization');

            // Match the Bearer token
            if (preg_match('/^Bearer\s(\S+)$/', $authHeader, $matches)) {
                $token = $matches[1];

                try {
                    // Set the token for the current request
                    Auth::guard('api')->setToken($token);

                    // Attempt to retrieve the user associated with the token
                    if (Auth::guard('api')->check()) {
                        // If the user is found, return a successful response
                        return response()->json(['status' => 200, 'message' => 'Authenticated'], 200);
                    }
                } catch (OAuthServerException $e) {
                    // Catch token-related exceptions
                    return response()->json(['status' => 401, 'message' => 'Invalid Token'], 401);
                } catch (\Exception $e) {
                    // Catch other exceptions
                    return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
                }
            }
        }
        // If no Authorization header or token is found, return an error
        return response()->json(['status' => 401, 'message' => 'Token is invalid'], 401);
    }
}
