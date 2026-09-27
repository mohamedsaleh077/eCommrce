<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Signup extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|max:255|min:3|string",
            "email" => "required|email|max:255|string",
            "password" => "required|max:255|min:8|string",
            "address" => "required|max:512|min:8|string"
        ]);

        $user = User::create([
            "role_id" =>  DB::table('roles')->where('title', 'user')->value('id'),
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "address" => $validated["address"]
        ]);

        Auth::login($user);

        return response()->json(['message' => "user " . $validated['name'] . " have registered successfully!"], 200);
    }
}
