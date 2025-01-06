<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    

    public function registerStepOne(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:100|unique:users',
        'password' => 'required|string|min:6',
        'phone' => 'required|numeric',
        'firstName' => 'string',
        'lastName' => 'string'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $verificationCode = rand(1000, 9999); // Generate a random 4-digit code

    $user = User::create([
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => bcrypt($request->password),
        'verification_code' => $verificationCode,
        'status' => 'unverified',
    ]);

    // Send the verification code via SMS
    $this->sendVerificationCode($user->phone, $verificationCode);

    return response()->json([
        'message' => 'User partially registered. Verification code sent to your phone.',
        'user_id' => $user->id, // Temporary identifier for the next step
    ], 201);
}

private function sendVerificationCode($phone, $verificationCode)
{
    $username = "daviddada360@gmail.com";
    $password = "David_4141";
    $message = $verificationCode;
    $sender = "krides";
    $mobiles = $phone;

    // Build the URL with variables
    // $url = "https://portal.nigeriabulksms.com/api/?username=" . urlencode($username) 
    // . "&password=" . urlencode($password) 
    // . "&message=" . urlencode("Your Verification Code for krides is $message") 
    // . "&sender=" . urlencode($sender) 
    // . "&mobiles=" . urlencode($mobiles);

    $url = "https://portal.nigeriabulksms.com/api/?username=daviddada360@gmail.com&password=David_4141&message=%22Your%20message%20is%22.$verificationCode&sender=welcome&mobiles=2349152632374&type=%22call%22";
    
 
    
   


    // Fetch the content from the URL
    $response = file_get_contents($url);

    // Check if request was successful
    if ($response === false) {
        echo "Error fetching URL";
    } else {
        // return redirect(route('doctors.status'))->with('msg','Message Sent to User Succefully'); 
        echo "Response: " . $response;
    }
}

public function verifyPhone(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'verification_code' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $user = User::find($request->user_id);

    if ($user->verification_code === $request->verification_code) { // Use verificationCode here
        $user->update([
            'verification_code' => null, // Clear the code
            'status' => 'verified',    // Mark as verified
        ]);

        return response()->json(['message' => 'Phone number verified successfully'], 200);
    }

    return response()->json(['message' => 'Invalid verification code'], 400);
}



}
