<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/reset-password/{token}', function ($token, Illuminate\Http\Request $request) {
    return view('auth.reset-password',
    [ 'request' => $request,
'token' => $token]);
})->name('password.reset');


Route::post('/store', [ForgotPasswordController::class, 'store'])->name('password.store');


Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
//  Route::get('/login', [AuthController::class, 'login'])->name('login');