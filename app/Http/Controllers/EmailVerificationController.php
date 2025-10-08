<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{


    public function send(Request $request) {
    // Validate the email parameter
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    // if ($user->hasVerifiedEmail()) {
    //     return response()->json(['message' => 'Email already verified.'], 200);
    // }

    // Send email verification notification
    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent.'], 200);
}


 

    public function verify(Request $request, $id, $hash)
{
    $user = User::findOrFail($id);

    // Remove manual hash validation - signed middleware handles this
    // The signed middleware will automatically validate the URL signature

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 200);
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }
  
    return view('email-success', [
        'message' => 'Your email was successfully verified! 🎉'
    ]);
   
}

 public function status (Request $request) {
        // Validate incoming email
        $data = $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        // Retrieve the user (we validated existence)
        $user = User::where('email', $data['email'])->first();

        // Null check on the email_verified_at field
        if (is_null($user->email_verified_at)) {
            return response()->json([
                'status'  => 'unverified',
                'message' => 'This email address has not been verified.',
            ], 200);
        }

        return response()->json([
            'status'      => 'verified',
            'message'     => 'This email address has been verified.',
            // safe formatting: optional() handles Carbon instance or null (but branch ensures not null)
            'verified_at' => optional($user->email_verified_at)->toDateTimeString(),
        ], 200);
    }
}

