<?php

use Illuminate\Support\Facades\Route;
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