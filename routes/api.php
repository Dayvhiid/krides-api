<?php

namespace App\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TripNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\{
    AuthController,
    TripController,
    UserController,
    RiderController,
    DriverController,
    RegistrationController,
    AuthenticationController,
    GoogleController,
    Auth\PasswordResetController,
    Auth\ForgotPasswordController
};

// Authentication Routes
// Route::get('/reset-password', function () {
//     return view('auth.reset-password');
// })->name('password.reset');

// Email Verification
Route::middleware('auth:sanctum')->post('/email/verify', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 200);
    }

    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent.'], 200);
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    // Driver Authentication
    Route::post('/driver/register', [DriverController::class, 'register']);
    Route::post('/driver/login', [DriverController::class, 'login']);

    // Normal User Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register-step-one', [RegistrationController::class, 'registerStepOne']);
    Route::post('/verify-phone', [RegistrationController::class, 'verifyPhone']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Password Reset
    Route::middleware('auth:api')->post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    // Route::get('/reset-password', function () {
    //     return view('auth.reset-password');
    // })->name('password.reset');

    // Protected Routes for Authenticated Users
    Route::middleware('auth:sanctum')->group(function () {
        // User Profile
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::put('/editUserProfile/{email}', [UserController::class, 'updateProfile']);
        Route::post('update/profile-picture', [UserController::class, 'updatePicture']);
        Route::delete('/deleteUserProfile/{email}', [UserController::class, 'deleteProfile']);

        // Trip Management
        Route::post('/trips/create', [TripController::class, 'store']);
        Route::get('/trips/user_history', [TripController::class, 'getTripsByUser']);//get user ride history
        Route::get('/trips', [TripController::class, 'index']);
        Route::patch('/trips/{id}/accept', [TripController::class, 'acceptTrip']);
        Route::get('ride/{driver_name}', [DriverController::class, 'fetchRide']);//fetch rider history by driver name

        // Rider Management
        Route::post('/riders', [RiderController::class, 'index']); // me sef i dont know what this one is doing
        Route::group(['prefix' => 'rider'], function () {
            Route::post('/store', [RiderController::class, 'store']);
            Route::get('/{driver_id}', [RiderController::class, 'getRideById']); // Get a history of the riders trips by user id
        });

        // Driver Management
        Route::get('/driver/profile', [DriverController::class, 'profile']); //This is the driver profile
        Route::get('/driver-list', [DriverController::class, 'list']);  //gets a list of all drivers
    });

    // Testing Routes
    Route::post('/testing/register', [AuthenticationController::class, 'register']);
    Route::post('/testing/login', [AuthenticationController::class, 'login']);
    Route::middleware('auth:api')->post('/testing/logout', [AuthenticationController::class, 'logout']);



});

// User Route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::middleware(['web'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});


Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);
    Auth::login($user);

    request()->merge(['hash' => $hash]);
    app(EmailVerificationRequest::class)->fulfill();

    return response()->json(['message' => 'Email successfully verified.'], 200);
})->middleware(['signed'])->name('verification.verify');

// Schedule Command for Deleting Unaccepted Trips
// Artisan::command('schedule:run', function ($schedule) {
//     $schedule->command('trips:delete-unaccepted')->everyMinute();
// });
