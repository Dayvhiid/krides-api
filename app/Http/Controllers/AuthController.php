<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use App\Otp\UserRegistrationOtp;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTFactory;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;


class AuthController extends Controller
{
    //
      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6',
    //     ]);
        
    //    $user = User::where('email', $request->email)->first();
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     if (!$token = auth()->attempt($validator->validated())) {
    //         return response()->json(['error' => 'Unauthorized'], 401);

    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //    return response()->json([
    //     'access_token' => $token,
    //     'token_type' => 'bearer',
    //     'expires_in' => auth('api')->factory()->getTTL() * 60
    // ]);

        
    // }

    public function login(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Attempt to authenticate the user
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get the authenticated user
    $user = Auth::user();

    // Generate a Sanctum token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return the response with the token
    return response()->json([
         'message' => 'User Logged in succesfully',
        'access_token' => $token,
        'token_type' => 'bearer',
    ]);
}


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'firstName' => 'required|string',
            'lastName' => 'required|string'
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


        

  


        // try {
        //     $otp = Otp::identifier($request->email)
        //         ->send(new UserRegistrationOtp(
        //             firstName: $request->firstName,
        //             lastName: $request->lastName,
        //             email: $request->email,
        //             password: $request->password
        //         ), Notification::route('mail', $request->email));
        
        //     return response()->json(['status' => $otp['status']]);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()]);
        // }
        

        //the block of code that handles sending the verification code.
        // $username = "daviddada360@gmail.com";
        // $password = "David_4141";
        // $message = $verificationCode;
        // $sender = "krides";
        // $mobiles = $request->input('phone');

        // // Build the URL with variables
        // $url = "https://portal.nigeriabulksms.com/api/?username=" . urlencode($username) . "&password=" . urlencode($password) . "&message=" . urlencode($message) . "&sender=" . urlencode($sender) . "&mobiles=" . urlencode($mobiles);

        // // Fetch the content from the URL
        // $response = file_get_contents($url);

        // // Check if request was successful
        // if ($response === false) {
        //     echo "Error fetching URL";
        // } else {
        //     // return redirect(route('doctors.status'))->with('msg','Message Sent to User Succefully'); 
        //     echo "Response: " . $response;
        // }
                

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
             'access token' => $token,
            'message' => 'User successfully registered',
             'verification_code' => $verificationCode,
            'user' => $user
        ], 201);


    }

    
  


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'User successfully signed out']);
    // }
    public function logout()
{
    // Revoke the current user's token
    auth()->user()->tokens()->delete();

    return response()->json(['message' => 'User successfully signed out']);
}
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        // return response()->json(auth()->user());
        return new UserResource(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
