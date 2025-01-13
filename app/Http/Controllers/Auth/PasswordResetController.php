<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
  
    public function sendResetLink(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        // Send reset link and get status
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        Log::info('Reset link status: ' . $status);
    
        // If the reset link was sent successfully, return the token
        if ($status === Password::RESET_LINK_SENT) {
            // Retrieve the reset token from the password_resets table
            $resetRecord = DB::table('password_resets_token')
                ->where('email', $request->email)
                ->first();
    
            if ($resetRecord) {
                // Return the token along with the success message
                return response()->json([
                    'message' => __($status),
                    'reset_token' => $resetRecord->token,  // Add the reset token to the response
                ], 200);
            }
        }
    
        // If there was an issue, return failure message
        return response()->json(['message' => __($status)], 400);
    }
    

    // public function resetPassword(Request $request)
    // {
    //     // Validate the input
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //         'token' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     // Attempt to reset the password
    //     $status = Password::reset(
    //         $request->only('email', 'password',  'token'),
    //         function ($user, $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );

    //     // Return success or failure in JSON format
    //     if ($status === Password::PASSWORD_RESET) {
    //         return response()->json(['message' => __($status)], 200);
    //     }

    //     return response()->json(['message' => __($status)], 400);
    // }

    public function resetPassword(Request $request)
    {
        // Log the incoming request data
        Log::info('Reset password request received', $request->all());
    
        // Validate the input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
    
        // Retrieve the reset token record from the database
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();
    
        if (!$resetRecord) {
            Log::warning('No reset record found for email: ' . $request->email);
            return response()->json(['message' => 'Invalid reset token'], 400);
        }
    
        // Verify the provided token against the hashed token in the database
        if (!Hash::check($request->token, $resetRecord->token)) {
            Log::warning('Invalid token for email: ' . $request->email);
            return response()->json(['message' => 'Invalid reset token'], 400);
        }
    
        // Token is valid; proceed with the password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update the user's password
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
    
                // Log the password reset success
                Log::info('Password reset successful for user: ' . $user->email);
            }
        );
    
        // Return success or failure response
        if ($status === Password::PASSWORD_RESET) {
            Log::info('Password reset completed successfully for email: ' . $request->email);
            return response()->json(['message' => __($status)], 200);
        }
    
        Log::warning('Password reset failed for email: ' . $request->email);
        return response()->json(['message' => __($status)], 400);
    }
    
}
