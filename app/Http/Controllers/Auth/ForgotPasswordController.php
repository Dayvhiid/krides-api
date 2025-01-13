<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;


class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
                    ? response()->json(['message' => 'Password reset link sent.'])
                    : response()->json(['error' => 'Failed to send reset link.'], 400);
    }







    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|confirmed',
    //     ]);

    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user) use ($request) {
    //             $user->forceFill([
    //                 'password' => Hash::make($request->password),
    //             ])->save();

    //             event(new PasswordReset($user));
    //         }
    //     );

    //     return $status == Password::PASSWORD_RESET
    //                 ? response()->json(['message' => 'Password has been reset successfully.'])
    //                 : response()->json(['error' => 'Failed to reset password.'], 400);
    // }


    public function store(Request $request)
    {
        // Step 1: Validate the incoming request data
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed', // Ensure the password confirmation matches
        ]);
    
        // Step 2: Find the password reset token in the database
        $passwordReset = DB::table('password_reset_tokens')
                            ->where('email', $request->email)
                            // ->where('token', $request->token)
                            ->first();
    
        // Step 3: If the token doesn't exist or has expired, return an error
        if (!$passwordReset) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }
    
        // Step 4: Check if the token has expired (if expiration time was set in the password reset table)
        $tokenExpirationTime = Carbon::parse($passwordReset->created_at)->addMinutes(60); // Assume 60 mins expiration
        if (Carbon::now()->greaterThan($tokenExpirationTime)) {
            return response()->json(['error' => 'Token has expired.'], 400);
        }
    
        // Step 5: Find the user by email
        $user = User::where('email', $request->email)->first();
    
        // Step 6: If the user doesn't exist, return an error
        if (!$user) {
            return response()->json(['error' => 'No user found with this email address.'], 404);
        }
    
        // Step 7: Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();
    
        // Step 8: Fire the password reset event (optional, for logging, email notifications, etc.)
        event(new PasswordReset($user));
    
        // Step 9: Return a success response
        return response()->json(['message' => 'Password has been reset successfully.']);
    }


}
