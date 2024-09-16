<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    //
    // public function index(){

    // }


    public function store(Request $request)
{
    // Validate the data
    $validator = Validator::make($request->all(), [
        'location' => 'required|string|max:255',
        'destination' => 'required|string|max:255',
        'distance' => 'required|string',
        'userId' => 'required|string|max:255',
        'DriverId' => 'required|string|max:255',
        'paymentStatus' => 'required|string|max:255',
        'vehicleId' => 'required|string|max:255'
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Create a new trip using the validated data
        $trip = Trip::create($validator->validated());

        // Return a resource response with the newly created trip
        return response()->json([
            'success' => true,
            'data' => new TripResource($trip)
        ], 201);
        
    } catch (\Exception $e) {
        // Return error response if something goes wrong during storage
        return response()->json([
            'success' => false,
            'message' => 'There was an error storing the trip.',
            'error' => $e->getMessage()
        ], 500);
    }

    
}

public function getTripsByUser($userId)
{
    // Query trips based on userId
    $trips = Trip::where('userId', $userId)->get();

    // Check if trips exist for the user
    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this user.'
        ], 404);
    }

    // Return the trips in a JSON response
    return response()->json([
        'success' => true,
        'data' => TripResource::collection($trips)
    ], 200);
}


}