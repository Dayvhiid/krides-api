<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\RideResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RiderController extends Controller
{
    public function store(Request $request)
{
    // Validate the data
    $validator = Validator::make($request->all(), [
        'location' => 'required|string|max:255',
        'destination' => 'required|string|max:255',
        'distance' => 'required|string',
        'DriverId' => 'required|string|max:255',
        'paymentStatus' => 'required|string|max:255',
        'vehicleId' => 'required|string|max:255', 
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
        $validatedData = $validator->validated();
        $validatedData['driver_id'] = Auth::id();

        // Create a new trip using the validated data
        $ride = Ride::create($validatedData);

        // Return a resource response with the newly created trip
        return response()->json([
            'success' => true,
            'data' => new RideResource($ride)
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

public function getRideById($driver_id){
    // $user = User::find($driver_id);
    // $rides = $user->rides()->paginate(10);
    // Query trips based on userId
    $rides = Ride::where('driver_id', Auth::id())->paginate(10);

    // Check if trips exist for the user
    if ($rides->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this user.'
        ], 404);
    }

    // Return the trips in a JSON response
    return response()->json([
        'success' => true,
        'data' => RideResource::collection($rides)
    ], 200); 
}
}
