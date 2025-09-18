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

    public function handleGoogleCallback() {
    $user = Socialite::driver('google')->stateless()->with(['access_type' => 'offline'])->user();

    // Log the user in or create the user in your database
    $authUser = User::firstOrCreate(
        ['email' => $user->getEmail()],
        ['name' => $user->getName(), 'google_id' => $user->getId()]
    );

    Auth::login($authUser);

    // Redirect to a desired location
    return redirect('/dashboard'); // Change to your desired route
}

}
