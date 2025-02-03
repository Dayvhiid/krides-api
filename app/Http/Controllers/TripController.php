<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\User;
use App\Events\TripUpdated;
use Illuminate\Http\Request;
use App\Events\TripRequested;
use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    //This is returns all trips that still have pending as thier status
    public function index(){
        // $trips = Trip::where('status', 'Pending')->paginate(20);
        // return response()->json($trips, 200);
        $user = auth()->user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Fetch all trips belonging to the authenticated user
        $trips = Trip::where('user_id', $user->id)->get();
    
        // Return the trips using the resource
        return TripResource::collection($trips);
    }


//     public function store(Request $request)
// {
//     // Validate the data
//     $validator = Validator::make($request->all(), [
//         'location' => 'string|max:255',
//         'destination' => 'string|max:255',
//         'amount' => 'string|max:225',
//         'number_of_passengers' => 'required|integer|max:3',
//         'rider_name' => 'required|string|max:225'
//         // 'distance' => 'required|string',
//         // 'DriverId' => 'required|string|max:255',
//         // 'paymentStatus' => 'required|string|max:255',
//         // 'vehicleId' => 'required|string|max:255',
//         // 'status' => 'required|string'    
//     ]);
        
//         //this block and the next prevents the user from requesting from too many rides in the space of a minute
//         // $lastTrip = Trip::where('user_id', Auth::id())
//         // ->orderBy('created_at', 'desc')
//         // ->first();

//         // if ($lastTrip && $lastTrip->created_at->gt(Carbon::now()->subMinute())) {
//         // return response()->json([
//         // 'message' => 'You can only book one ride per minute.'
//         // ], 429);
//         // }
//         $user = $request->user();

//     // Check if validation fails
//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     try {
//         // Create a new trip using the validated data
//         $validatedData = $validator->validated();
//         $validatedData['user_id'] = Auth::id();

//         // Create a new trip using the validated data
//         $trip = Trip::create($validatedData);
//         event(new TripRequested($trip));
//         $trips = new Trip();
//         $trips->name = $user->firstName . ' ' . $user->lastName;
//         $trips->phone_number = $user->phone;
//         $trips->save();

//         // Return a resource response with the newly created trip
//         return response()->json([
//             'success' => true,
//             'data' => new TripResource($trip)
//         ], 201);


      
//     } catch (\Exception $e) {
//         // Return error response if something goes wrong during storage
//         return response()->json([
//             'success' => false,
//             'message' => 'There was an error storing the trip.',
//             'error' => $e->getMessage()
//         ], 500);
//     }

    
// }



public function store(Request $request)
{
    // Validate the data
    $validator = Validator::make($request->all(), [
        'location' => 'string|max:255',
        'destination' => 'string|max:255',
        'amount' => 'string|max:225',
        'number_of_passengers' => 'required|integer|max:3',
        'rider_name' => 'required|string|max:225',
    ]);

    $user = $request->user();

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
        $validatedData['user_id'] = Auth::id();

        $trip = Trip::create($validatedData); // Create the trip
        
        // Add the user's name and phone number to the created trip
        $trip->name = $user->firstName . ' ' . $user->lastName;
        $trip->phone_number = $user->phone;
        $trip->save(); // Save the updated trip details

        // Trigger the event
        event(new TripRequested($trip));

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


// public function getTripsByUser($userId)
// {
//     $user = User::find($userId);
//     $trips = $user->trips()->paginate(20);
//     // Query trips based on userId
//     // $trips = Trip::where('user_id', $userId)->get();

//     // Check if trips exist for the user
//     if ($trips->isEmpty()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No trips found for this user.'
//         ], 404);
//     }

//     // Return the trips in a JSON response
//     return response()->json([
//         'success' => true,
//         'data' => TripResource::collection($trips)
//     ], 200);
// }

    public function acceptTrip($id){
        $trip = Trip::find($id);

        if (!$trip) {
            return response()->json(['message' => 'Trip not found'], 404);
        }

        if ($trip->status !== 'Accepted') {
            return response()->json(['message' => 'Trip status cannot be updated'], 400);
        }

        $trip->status = 'Accepted';
        $trip->save();

        return response()->json(['message' => 'Trip accepted successfully'], 200);
    }

public function getTripsByUser(Request $request)
{
    try {
        // Get the authenticated user's ID
        $userId = $request->user()->id;

        // Fetch trips for the authenticated user
        $trips = Trip::where('user_id', $userId)->get();

        // Check if any trips were found
        if ($trips->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No trips found for the authenticated user.'
            ], 404);
        }

        // Return the trips
        return response()->json([
            'success' => true,
            'data' => TripResource::collection($trips)
        ], 200);

    } catch (\Exception $e) {
        // Handle unexpected errors
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching trips.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}