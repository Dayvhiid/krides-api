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
    PaymentController,
    EmailVerificationController,
    Auth\PasswordResetController,
    Auth\ForgotPasswordController
};



// Email Verification
Route::post('/email/verify', [EmailVerificationController::class, 'send']);
Route::post('/email/verification-status', [EmailVerificationController::class, 'status'])->name('verification.status');
//Password Reset
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
// Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);

// Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    // Driver Authentication
    Route::post('/driver/register', [DriverController::class, 'register']);
    Route::post('/driver/login', [DriverController::class, 'login']);

    // Normal User Authentication
    Route::any('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register-step-one', [RegistrationController::class, 'registerStepOne']);
    Route::post('/verify-phone', [RegistrationController::class, 'verifyPhone']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);


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
        // Route::patch('/trips/{id}/accept', [TripController::class, 'acceptTrip']);
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
        Route::get('/driver/trips', [DriverController::class, 'fetchRidesGlobal']); //get a list of all pending trips.   //dcmt
        Route::post('/trips/{trip}/accept', [TripController::class, 'accept']);// accepts the ride while using the trips  id //dcmt
        Route::get('trips/{trip}', [TripController::class, 'show']);//get a specific trip by id //dcmt

        Route::get('/trip/accept/{id}', [DriverController::class, 'acceptTrip']); //new route to accept trip


        //Endpoint for drivers to update their subaccount id
        Route::put('/driver/subaccount', [DriverController::class, 'updateSubaccountId']); //dcmt
        Route::post('/driver/bank-details', [DriverController::class, 'updateBankDetails']);



        //Paymment Management
        Route::post('/wallet/fund', [PaymentController::class, 'fundWallet']);// dcmt
        // Route::any('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
        Route::post('/trip/pay', [PaymentController::class, 'payForTrip']); 

        Route::post('/initiate-payment', [PaymentController::class, 'initiatePayment']); //dcmt

        Route::post('/payment/callback', [PaymentController::class, 'handleFlutterwaveCallback'])->name('payment.callback');//dcmt
        Route::post('/driver/withdraw', [DriverController::class, 'requestWithdrawal']);//dcmt


        Route::post('/paystack/wallet/fund', [PaymentController::class, 'fundWalletWithPaystack']);
        Route::get('/payment/verify/{reference}', [PaymentController::class, 'verifyPaystackPayment'])->name('payment.verify');
        Route::post('/paystack/trip/pay', [PaymentController::class, 'payForTripWithPaystack']);



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



// Schedule Command for Deleting Unaccepted Trips
// Artisan::command('schedule:run', function ($schedule) {
//     $schedule->command('trips:delete-unaccepted')->everyMinute();
// });
