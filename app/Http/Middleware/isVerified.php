<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class isVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
                $validation = $request->validate(['user_id' => 'required|integer|exists:users,id']);

        $requestToken = $request->bearerToken();
        
        if (!$requestToken) {
            return response()->json([
                'message' => 'Unauthorized access, missing Bearer token'
            ], 401);
        }

        $tokenInstance = PersonalAccessToken::findToken($requestToken);

        if (!$tokenInstance || $tokenInstance->tokenable_id != $validation['user_id']) {
            return response()->json([
                'message' => 'Unauthorized access, token verification failed'
            ], 403);
        }

        if ($tokenInstance->tokenable->hasVerifiedEmail()) {
            return response()->json(['message' => 'your email is verified'], 403);
        }

        return $next($request);
    }
}
