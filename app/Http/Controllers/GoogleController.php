<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{


public function redirectToProvider()
{
    return Socialite::driver('google')->redirect();
}

public function handleProviderCallback()
{
    $user = Socialite::driver('google')->stateless()->user();
    // Generate an access token for the authenticated user
    $token = $user->token;
    // Return the access token to the mobile device
    return response()->json(['token' => $token]);
}
}
