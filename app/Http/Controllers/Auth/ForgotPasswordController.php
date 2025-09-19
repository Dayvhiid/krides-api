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



    public function resetPassword(Request $request)
    {
        // Step 1: Validate the incoming request data
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // Added min length
        ]);

        // Step 2: Find all password reset records for this email
        $passwordResets = DB::table('password_reset_tokens')
                            ->where('email', $request->email)
                            ->get();

        // Step 3: If no tokens exist, return an error
        if ($passwordResets->isEmpty()) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        // Step 4: Check if any token matches and is not expired
        $validToken = null;
        foreach ($passwordResets as $passwordReset) {
            // Check if token matches (Laravel hashes tokens)
            if (Hash::check($request->token, $passwordReset->token)) {
                // Check if token has not expired
                $tokenExpirationTime = Carbon::parse($passwordReset->created_at)->addMinutes(60);
                if (Carbon::now()->lessThanOrEqualTo($tokenExpirationTime)) {
                    $validToken = $passwordReset;
                    break;
                }
            }
        }

        // Step 5: If no valid token found, return error
        if (!$validToken) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        // Step 6: Find the user by email
        $user = User::where('email', $request->email)->first();

        // Step 7: If the user doesn't exist, return an error
        if (!$user) {
            return response()->json(['error' => 'No user found with this email address.'], 404);
        }

        // Step 8: Update the user's password
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now(); // Mark email as verified if needed
        $user->save();

        // Step 9: Delete all password reset tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Step 10: Fire the password reset event
        event(new PasswordReset($user));

        // Step 11: Return a success response
        return response()->json(['message' => 'Password has been reset successfully.']);
    }



}
