<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyMail extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function verify(Request $request)
    {
        $validation = $request->validate([
            'code' => 'required|max:999999|min:100000|integer'
        ]);

        $verify = MailSender::checkVerifyCode($validation['code'], $request['user_id']);

        if(!$verify){
            return response()->json([
                'message' => 'Invalid Verification Code'
            ], 422);
        }

        $user = User::findOrFail($request['user_id']);
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'mail verified successfully!'
        ]);
    }

    public function getNewCode(Request $request)
    {

        MailSender::sendVerificationCode($request['user_id']);
        return response()->json([
            'message' => 'new code have been sent!'
        ]);
    }
}
