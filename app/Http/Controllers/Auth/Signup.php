<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Auth\MailSender;
use Mail;

class Signup extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|max:255|min:3|string",
            "email" => "required|email|max:255|string|unique:users,email",
            "password" => "required|max:255|min:8|string",
            "address" => "required|max:512|min:8|string"
        ]);

        $roleId = Role::where('title', 'user')->value('id');

        $user = User::create([
            "role_id" =>  $roleId,
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "address" => $validated["address"]
        ]);

        $deviceName = $request->input('device_name', 'Unknown Device');
        $token = $user->createToken($deviceName)->plainTextToken;

        MailSender::sendVerificationCode($user->id);

        return response()->json([
            'message' => "user {$user->name} have registered successfully! Verify your email.",
            'access_token' => $token,
            'token_type'   => 'bearer'
            ], 200);
    }
}
