<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validation = $request->validate([
            'email' => 'required|max:255|email|string',
            'password' => 'required|max:255|min:8|string'
        ]);

        if (! Auth::attempt($validation)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();
        // $token = $user->tokens()->latest()->first();
        
        // if(!$token){
            // $user->tokens()->delete();
            
        $deviceName = $request->input('device_name', 'Unknown Device')
                        ?? $request->userAgent() 
                        ?? 'Unknown Device';
        $token = $user->createToken($deviceName)->plainTextToken;
        // }

        $message = "user {$user->name} have logged in successfully!";
        
        if(!$user->email_verified_at){
            MailSender::sendVerificationCode($user->id);
            $message = "user {$user->name} have logged in successfully! but you must verify your email!";
        }

        return response()->json([
            'message' => $message,
            'user_id' => $user->id,
            'access_token' => $token,
            'token_type'   => 'bearer'
            ], 200);
    }
}
