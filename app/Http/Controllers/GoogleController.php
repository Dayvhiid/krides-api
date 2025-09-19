<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')
        ->stateless()
        ->with(['access_type' => 'offline'])
        ->user();

    // Find or create the user
    $authUser = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]
    );

    // ✅ Generate API token (instead of session login)
    $token = $authUser->createToken('authToken')->plainTextToken;

    // Option A: return JSON if frontend is calling API directly
    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $authUser
    ]);

//     public function handleGoogleCallback() {
//     $user = Socialite::driver('google')->stateless()->with(['access_type' => 'offline'])->user();

//     // Log the user in or create the user in your database
//     $authUser = User::firstOrCreate(
//         ['email' => $user->getEmail()],
//         ['name' => $user->getName(), 'google_id' => $user->getId()]
//     );

//     Auth::login($authUser);

//     // Redirect to a desired location
//     // return redirect('/dashboard'); 
// }

}
}
