<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request['user_id'];

        $adminRoleId = Role::where('title', 'admin')->value('id');

        $userRoleId = User::with('role')->find($userId);

        if($userRoleId->role_id !== $adminRoleId){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->merge(['isAdmin' => true]);
        return $next($request);
    }
}
