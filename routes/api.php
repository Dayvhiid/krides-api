<?php
namespace App\Api\V1; 
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\RiderController;
use Dotenv\Exception\ValidationException;
use App\Http\Controllers\GoogleController;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthenticationController;


Artisan::command('schedule:run', function (Schedule $schedule) {
    // Register the DeleteUnacceptedTrips command
    $schedule->command('trips:delete-unaccepted')->everyMinute();
});

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
    Route::post('/register-step-one', [RegistrationController::class, 'registerStepOne']);
    Route::post('/verify-phone', [RegistrationController::class, 'verifyPhone']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);



    Route::post('/otp/verify', function (Request $request) {

        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'code'     => ['required', 'string']
        ]);
    
        $otp = Otp::identifier($request->email)->attempt($request->code);
    
        if($otp['status'] != Otp::OTP_PROCESSED)
        {
            abort(403, __($otp['status']));
        }
    
        return $otp['result'];
    });
    
    
    
   
    /** OTP Resend Route */
    Route::post('/otp/resend', function (Request $request) {
    
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255']
        ]);
    
        $otp = Otp::identifier($request->email)->update();
    
        if($otp['status'] != Otp::OTP_SENT)
        {
            abort(403, __($otp['status']));
        }
        return __($otp['status']);
    });
    
    // Protect these routes using 'auth:api' middleware
    Route::middleware('auth:api')->group(function () {
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::put('/editUserProfile/{email}', [UserController::class, 'updateProfile']);
        Route::delete('/deleteUserProfile/{email}', [UserController::class, 'deleteProfile']);
        Route::post('/trips/store', [TripController::class, 'store']);
        Route::get('/trips/user/{userId}', [TripController::class, 'getTripsByUser']);
        Route::get('trips', [TripController::class, 'index']);
        Route::patch('/trips/{id}/accept', [TripController::class, 'acceptTrip']);


        Route::group(['prefix' => 'rider'], function () {
            Route::post('/store', [RiderController::class, 'store']);
            Route::get('/{driver_id}', [RiderController::class, 'getRideById']);
        });


    });


    Route::post('/testing/register', [AuthenticationController::class, 'register']);
    Route::post('/testing/login', [AuthenticationController::class, 'login']);
    Route::middleware('auth:api')->post('/testing/logout', [AuthenticationController::class, 'logout']);

    // Route::get('/testing/login/google', [AuthenticationController::class, 'redirectToGoogle']);
    Route::get('/testing/login/google/callback', [AuthenticationController::class, 'handleGoogleCallback']);

   // Chnages hrere    
});


