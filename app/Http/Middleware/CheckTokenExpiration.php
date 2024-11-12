<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Carbon\Carbon;

class CheckTokenExpiration
{
    public function handle($request, Closure $next)
    {
        // Use the 'api' guard explicitly
        $user = Auth::guard('api')->user();
        if (!$user) {
            // If no user is authenticated, return a JSON error instead of redirecting
            return response()->json(['status'=>401,'message' => 'Unauthenticated'], 401);
        }

        // Get the access token from the request
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['status'=>401,'message' => 'Token not provided'], 401);
        }

        // Validate the token
        $token = $this->validateToken($user, $accessToken);

        if (!$token) {
            return response()->json(['status'=>401,'message' => 'Token is invalid'], 401);
        }

        // Check if the token has expired
        if ($token->expires_at->lt(Carbon::now())) {
            return response()->json(['status'=>401,'message' => 'Token has expired'], 401);
        }

        return $next($request);
    }

    private function validateToken($user, $accessToken)
    {
        $parts = explode('.', $accessToken);
        if (count($parts) != 3) {
            return null;
        }

        $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $parts[1]))), true);
        return Token::where('id', $payload['jti'])
            ->where('user_id', $user->getAuthIdentifier())
            ->where('revoked', 0)
            ->first();
    }
}
