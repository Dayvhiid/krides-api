<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function index(){

    }
    public function store(StoreUserRequest $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        // Create a new product using the validated data
        $user = User::create($validatedData);

        // Return a resource response with the newly created product
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        //
        $user->update($request->all());
    }

    public function updateProfile(Request $request, $email)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'outlet' => 'required|string',
            'vehicle_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the user profile
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        if ($request->has('outlet')) {
            $user->outlet = $request->outlet;
        }
        if ($request->has('vehicle')) {
            $user->vehicle = $request->vehicle;
        }
        

        $user->save();

        return response()->json(['message' => 'Profile updated successfully.'], 200);
    }


    public function deleteProfile($email)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Delete the user profile
        $user->delete();

        return response()->json(['message' => 'Profile deleted successfully.'], 200);
    }
}
