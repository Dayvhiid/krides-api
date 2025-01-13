<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// class GoogleController extends Controller
// {
//     public function redirectToGoogle()
//     {
//         return Socialite::driver('google')->redirect();
//     }

//     public function handleGoogleCallback()
//     {
//         try {
//             $googleUser = Socialite::driver('google')->user();

//             // Check if user exists
//             $user = User::where('email', $googleUser->getEmail())->first();

//             if (!$user) {
//                 // Create a new user
//                 $user = User::create([
//                     'name' => $googleUser->getName(),
//                     'email' => $googleUser->getEmail(),
//                     'google_id' => $googleUser->getId(),
//                     'password' => bcrypt('default-password'), // Not used for Google login
//                 ]);
//             }

//             // Login the user
//             Auth::login($user);

//             return redirect()->intended('/dashboard');
//         } catch (\Exception $e) {
//             return redirect('/login')->with('error', 'Something went wrong. Please try again.');
//         }
//     }
// }
class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // public function handleGoogleCallback()
    // {
    //     try {
    //         $googleUser = Socialite::driver('google')->user();

    //         // Check if user exists
    //         $user = User::where('email', $googleUser->getEmail())->first();

    //         if (!$user) {
    //             // Create a new user
    //             $user = User::create([
    //                 'name' => $googleUser->getName(),
    //                 'email' => $googleUser->getEmail(),
    //                 'google_id' => $googleUser->getId(),
    //                 'password' => bcrypt('default-password'), // Not used for Google login
    //             ]);

    //             // Flash a success message for registration
    //             session()->flash('success', 'You have been successfully registered and logged in!');
    //         }

    //         // Login the user
    //         Auth::login($user);

    //         // Redirect to the dashboard or the intended page
    //         return redirect()->intended('/dashboard');
    //     } catch (\Exception $e) {
    //         return redirect('/login')->with('error', 'Something went wrong. Please try again.');
    //     }
    // }


    public function handleGoogleCallback()
{
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
