<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverResource;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'fullname' => 'string',
            'picture' => '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vehicle_id' => 'required|string',  
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $verificationCode = rand(1000, 9999);
        

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'verification_code' => $verificationCode
            ]
        ));


        if ($request->hasFile('picture')) {
            // Store the picture in the 'public' directory and get the file path
            $path = $request->file('picture')->store('drivers_pictures', 'public');

            // Save the file path to the database (assuming you're saving to the 'drivers' table)
            $driver = new Driver();
            $driver->picture = $path; // Save the file path to the 'picture' column
            $driver->save();
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access token' => $token,
           'message' => 'Driver successfully registered',
            'verification_code' => $verificationCode,
           'user' => $user
       ], 201);

    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Attempt to authenticate the user
        if (!Auth::attempt($request->only('phone', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Generate a Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return the response with the token
        return response()->json([
             'message' => 'Driver Logged in succesfully',
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function profile(){
        return new DriverResource(auth()->user());
    }

    public function list(){
        $drivers = User::whereNotNull('vehicle_id')->pluck('fullname');

        // Return the results as a JSON response
        return response()->json($drivers);
    }
}
