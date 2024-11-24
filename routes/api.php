<?php
namespace App\Api\V1; 
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Laravel\Socialite\Facades\Socialite;
use Dotenv\Exception\ValidationException;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::post("/login", function(Request $request){
//     $request->validate([
//         "email" => ['required', 'email'],
//         "password" => ['required'],
//         "device_name" => ['required']
//     ]);

//     // Correct the typo: `$request->eamil` to `$request->email`
//     $user = User::where('email', $request->email)->first();

//     // Fix the condition for Hash::check
//     if (! $user || ! Hash::check($request->password, $user->password)) {
//         throw ValidationException::withMessages([
//             'email' => ['The provided credentials are incorrect']
//         ]);
//     }

//     // Correct the response syntax
//     return response()->json([
//         'token' => $user->createToken($request->device_name)->plainTextToken
//     ]);
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Protect these routes using 'auth:api' middleware
    Route::middleware('auth:api')->group(function () {
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::put('/editUserProfile/{email}', [UserController::class, 'updateProfile']);
        Route::delete('/deleteUserProfile/{email}', [UserController::class, 'deleteProfile']);
        Route::post('/trips/store', [TripController::class, 'store']);
        Route::get('/trips/user/{userId}', [TripController::class, 'getTripsByUser']);
    });

    //use this in prod
    Route::post('/testing/register', [AuthenticationController::class, 'register']);
    Route::post('/testing/login', [AuthenticationController::class, 'login']);
    Route::middleware('auth:api')->post('/testing/logout', [AuthenticationController::class, 'logout']);

    // Route::get('/testing/login/google', [AuthenticationController::class, 'redirectToGoogle']);
    Route::get('/testing/login/google/callback', [AuthenticationController::class, 'handleGoogleCallback']);

   // Chnages hrere    
});


