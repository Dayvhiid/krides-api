<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DriverResource;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [ 
    //         'password' => 'required|string|min:6',
    //         'phone' => 'required|string',
    //         'fullname' => 'string',
    //         'picture' => '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'vehicle_id' => 'required|string',  
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }

    //     $verificationCode = rand(1000, 9999);
    //     $uniqueEmail = strtolower(str_replace(' ', '_', $request->fullname)) . '_' . time() . '@example.com';


    //     $user = User::create(array_merge(
    //         $validator->validated(),
    //         [
    //             'password' => bcrypt($request->password),
    //             'verification_code' => $verificationCode,
    //             'email' => $uniqueEmail,
    //             'role' => 'driver'
    //         ]
    //     ));


    //     if ($request->hasFile('picture')) {
    //         // Store the picture in the 'public' directory and get the file path
    //         $path = $request->file('picture')->store('drivers_pictures', 'public');

    //         // Save the file path to the database (assuming you're saving to the 'drivers' table)
    //         $driver = new Driver();
    //         $driver->picture = $path; // Save the file path to the 'picture' column
    //         $driver->save();
    //     }
    //     $token = $user->createToken('auth_token')->plainTextToken;
    //     return response()->json([
    //         'access token' => $token,
    //        'message' => 'Driver successfully registered',
    //         'verification_code' => $verificationCode,
    //        'user' => $user
    //    ], 201);

    // }


    public function register(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'password' => 'required|string|min:6',
        'phone' => 'required|string',
        'fullname' => 'string|required',
        'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'vehicle_id' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    // Generate a unique email and verification code
    $verificationCode = rand(1000, 9999);
    $uniqueEmail = strtolower(str_replace(' ', '_', $request->fullname)) . '_' . time() . '@example.com';

    // Create the user with the default role as 'driver'
    $user = User::create(array_merge(
        $validator->validated(),
        [
            'password' => bcrypt($request->password),
            'verification_code' => $verificationCode,
            'email' => $uniqueEmail,
            'role' => 'driver', // Set role as 'driver' by default
        ]
    ));

    // Handle the picture file if it exists
    if ($request->hasFile('picture')) {
        // Store the picture in the 'public/drivers_pictures' directory and get the path
        $path = $request->file('picture')->store('drivers_pictures', 'public');

        // Save the file path to the user's profile
        $user->picture = $path; // Assuming 'picture' column exists in the 'users' table
        $user->save();
    }

    // Generate an access token for the user
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return a success response
    return response()->json([
        'access_token' => $token,
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

    // public function list(){ //should pick the vehicle ID and driver name
    //     $drivers = User::whereNotNull('vehicle_id')->pluck('fullname');

    //     // Return the results as a JSON response
    //     return response()->json($drivers);
    // }

    public function list(){
        // Fetch drivers where the role is 'driver' and include their name and vehicle_id
        $drivers = User::where('role', 'driver')
                       ->whereNotNull('vehicle_id')
                       ->select('fullname', 'vehicle_id')
                       ->get();
    
        // Transform the data into the desired format
        $formattedDrivers = $drivers->map(function ($driver) {
            return [
                'name' => $driver->fullname,
                'vehicle_id' => $driver->vehicle_id
            ];
        });
    
        // Return the results as a JSON response
        return response()->json($formattedDrivers);
    }

    public function fetchRide($driver_name)
{
    try {
        // Search for trips with an exact match for the driver_name
        $trips = Trip::where('rider_name', $driver_name)->get();

        // Check if any trips were found
        if ($trips->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No trips found for the given rider name.'
            ], 404);
        }

        // Return the filtered trips
        return response()->json([
            'success' => true,
            'data' => $trips
        ], 200);

    } catch (\Exception $e) {
        // Handle any unexpected errors
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching rides.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
