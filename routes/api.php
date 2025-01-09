<?php

namespace App\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TripNotification;
use Illuminate\Support\Facades\Route;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::group([ 'middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    
    // Authentication routes
    Route::post('/driver/register', [DriverController::class, 'register']);
    Route::post('/driver/login', [DriverController::class, 'login']); // For driver phone number and password
    Route::post('/login', [AuthController::class, 'login']); // Normal user login route
    Route::post('/register', [AuthController::class, 'register'])->name('login');
    Route::post('/register-step-one', [RegistrationController::class, 'registerStepOne']);
    Route::post('/verify-phone', [RegistrationController::class, 'verifyPhone']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // OTP Routes
    Route::post('/otp/verify', function (Request $request) {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'code' => ['required', 'string']
        ]);
        $otp = Otp::identifier($request->email)->attempt($request->code);
        if ($otp['status'] != Otp::OTP_PROCESSED) {
            abort(403, __($otp['status']));
        }
        return $otp['result'];
    });

    // OTP Resend Route
    Route::post('/otp/resend', function (Request $request) {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255']
        ]);
        $otp = Otp::identifier($request->email)->update();
        if ($otp['status'] != Otp::OTP_SENT) {
            abort(403, __($otp['status']));
        }
        return __($otp['status']);
    });

    // Protected Routes for Authenticated Users
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::put('/editUserProfile/{email}', [UserController::class, 'updateProfile']);
        Route::post('update/profile-picture', [UserController::class, 'updatePicture']);
        Route::delete('/deleteUserProfile/{email}', [UserController::class, 'deleteProfile']);
        Route::post('/trips/store', [TripController::class, 'store']);
        Route::get('/trips/user/{userId}', [TripController::class, 'getTripsByUser']);
        Route::get('trips', [TripController::class, 'index']);
        Route::patch('/trips/{id}/accept', [TripController::class, 'acceptTrip']);
        Route::post('/riders', [RiderController::class, 'index']);
        
        Route::group(['prefix' => 'rider'], function () {
            Route::post('/store', [RiderController::class, 'store']);
            Route::get('/{driver_id}', [RiderController::class, 'getRideById']);
        });

        Route::get('/driver/profile', [DriverController::class, 'profile']);
        Route::get('driver-list', [DriverController::class, 'list']);
    });

    // Testing Routes (Consider removing these after testing)
    Route::post('/testing/register', [AuthenticationController::class, 'register']);
    Route::post('/testing/login', [AuthenticationController::class, 'login']);
    Route::middleware('auth:api')->post('/testing/logout', [AuthenticationController::class, 'logout']);
    Route::get('/testing/login/google/callback', [AuthenticationController::class, 'handleGoogleCallback']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Schedule Command for Deleting Unaccepted Trips (Every Minute)
Artisan::command('schedule:run', function ($schedule) {
    $schedule->command('trips:delete-unaccepted')->everyMinute();
});
