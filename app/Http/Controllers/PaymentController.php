<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Trip;

class PaymentController extends Controller
{
    public function fundWallet(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:100']);
        $user = auth()->user();
        $reference = Str::uuid();

        $flutterwaveResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $reference,
            'amount' => $request->amount,
            'currency' => 'NGN',
            'redirect_url' => route('payment.callback'),
            'customer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'customizations' => [
                'title' => 'Wallet Top-up',
                'description' => 'Add funds to your wallet',
            ],
        ]);

        $data = $flutterwaveResponse->json();
        return response()->json(['link' => $data['data']['link']]);
    }

    // public function handleCallback(Request $request)
    // {
    //     $status = $request->status;
    //     $txRef = $request->tx_ref;

    //     if ($status !== 'successful') {
    //         return response()->json(['message' => 'Payment failed'], 400);
    //     }

    //     $verify = Http::withToken(env('FLW_SECRET_KEY'))
    //         ->get("https://api.flutterwave.com/v3/transactions/{$request->transaction_id}/verify");

            

    //     $response = $verify->json();
    //     $email = $response['data']['customer']['email'];
    //     $amount = $response['data']['amount'];

    //     $user = User::where('email', $email)->first();
    //     if (!$user) return response()->json(['message' => 'User not found'], 404);

    //     // Update wallet
    //     $user->wallet_balance += $amount;
    //     $user->save();

    //     // Record transaction
    //     Transaction::create([
    //         'user_id' => $user->id,
    //         'amount' => $amount,
    //         'type' => 'credit',
    //         'reference' => $txRef,
    //         'description' => 'Wallet Funding',
    //     ]);

    //     return response()->json(['message' => 'Wallet funded successfully']);
    // }

public function handleCallback(Request $request)
{
    $status = strtolower($request->status);
    $txRef = $request->tx_ref;

    $validSuccessStatuses = ['successful', 'completed', 'success', 'approved'];

    if (!in_array($status, $validSuccessStatuses)) {
        return response()->json([
            'message' => 'Payment not successful from callback',
            'callback_status' => $status,
            'tx_ref' => $txRef,
        ], 400);
    }

    $verify = Http::withToken(env('FLW_SECRET_KEY'))
        ->get("https://api.flutterwave.com/v3/transactions/{$request->transaction_id}/verify");

    if ($verify->failed()) {
        return response()->json([
            'message' => 'Verification failed',
            'details' => $verify->body()
        ], 400);
    }

    $response = $verify->json();

    if (!isset($response['data']['customer']['email'], $response['data']['amount'])) {
        return response()->json([
            'message' => 'Invalid verification response structure',
            'response' => $response
        ], 400);
    }

    $rawEmail = $response['data']['customer']['email'];
    $amount = $response['data']['amount'];

    // Remove any Flutterwave email prefix like 'ravesb_f2c266144f78e4750640_'
    $email = preg_replace('/^ravesb_[^_]+_/', '', $rawEmail);

    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found',
            'email' => $email
        ], 404);
    }

    $user->wallet_balance += $amount;
    $user->save();

    Transaction::create([
        'user_id' => $user->id,
        'amount' => $amount,
        'type' => 'credit',
        'reference' => $txRef ?? Str::uuid(),
        'description' => 'Wallet Funding',
    ]);

    return response()->json(['message' => 'Wallet funded successfully']);
}





    // public function payForTrip(Request $request)
    // {
    //     $request->validate([
    //         'trip_id' => 'required|exists:trips,id',
    //         'method' => 'required|in:wallet,direct',
    //     ]);

    //     $user = auth()->user();
    //     $trip = Trip::findOrFail($request->trip_id);

    //     if ($trip->status === 'paid') {
    //         return response()->json(['message' => 'Trip already paid'], 400);
    //     }

    //     if ($request->method === 'wallet') {
    //         if ($user->wallet_balance < $trip->amount) {
    //             return response()->json(['message' => 'Insufficient wallet balance'], 400);
    //         }

    //         // Deduct from wallet
    //         $user->wallet_balance -= $trip->amount;
    //         $user->save();

    //         $trip->status = 'paid';
    //         $trip->save();

    //         Transaction::create([
    //             'user_id' => $user->id,
    //             'amount' => $trip->amount,
    //             'type' => 'debit', //Fix This
    //             'reference' => Str::uuid(),
    //             'description' => 'Trip payment from wallet',
    //         ]);

    //         // You can also update the driver’s wallet here or send money via Flutterwave transfer

    //         return response()->json(['message' => 'Trip paid using wallet']);
    //     }

    //     // Direct payment with split
    //     $reference = Str::uuid();
    //     $flutterwaveResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
    //         'tx_ref' => $reference,
    //         'amount' => $trip->amount,
    //         'currency' => 'NGN',
    //         'redirect_url' => route('payment.callback'),
    //         'customer' => [
    //             'email' => $user->email,
    //             'name' => $user->name,
    //         ],
    //         'subaccounts' => [
    //             [
    //                 'id' => $trip->subaccount_id,// Hold
    //                 'transaction_charge_type' => 'flat',
    //                 'transaction_charge' => 50, // your fee
    //             ]
    //         ],
    //         'customizations' => [
    //             'title' => 'Trip Payment',
    //             'description' => 'Pay for your trip',
    //         ],
    //     ]);

    //     $data = $flutterwaveResponse->json();
    //     return response()->json(['link' => $data['data']['link']]);
    // }


    public function payForTrip(Request $request)
{
    $request->validate([
        'trip_id' => 'required|exists:trips,id',
        'method' => 'required|in:wallet,direct',
    ]);

    $user = auth()->user();
    $trip = Trip::findOrFail($request->trip_id);

    if ($trip->status === 'paid') {
        return response()->json(['message' => 'Trip already paid'], 400);
    }

    // Get the driver from users table
    $driver = User::find($trip->driver_id);
    if (!$driver) {
        return response()->json(['message' => 'Driver not found'], 404);
    }

    if ($request->method === 'wallet') {
        if ($user->wallet_balance < $trip->amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        // Deduct from rider's wallet
        $user->wallet_balance -= $trip->amount;
        $user->save();

        // Credit driver's wallet
        $driver->wallet_balance += $trip->amount;
        $driver->save();

        $trip->status = 'paid';
        $trip->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $trip->amount,
            'type' => 'debit',
            'reference' => Str::uuid(),
            'description' => 'Trip payment from wallet',
        ]);

        Transaction::create([
            'user_id' => $driver->id,
            'amount' => $trip->amount,
            'type' => 'credit',
            'reference' => Str::uuid(),
            'description' => 'Trip earning to driver wallet',
        ]);

        return response()->json(['message' => 'Trip paid using wallet']);
    }

    // Direct payment (Flutterwave)
    $reference = Str::uuid();
    $flutterwaveResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
        'tx_ref' => $reference,
        'amount' => $trip->amount,
        'currency' => 'NGN',
        'redirect_url' => route('payment.callback'),
        'customer' => [
            'email' => $user->email,
            'name' => $user->name,
        ],
        'subaccounts' => [
            [
                'id' => $trip->subaccount_id,
                'transaction_charge_type' => 'flat',
                'transaction_charge' => 50,
            ]
        ],
        'customizations' => [
            'title' => 'Trip Payment',
            'description' => 'Pay for your trip',
        ],
    ]);

    $data = $flutterwaveResponse->json();

    // NOTE: Since payment is external, you should handle driver wallet update in the payment callback
    // Do NOT credit the driver's wallet here yet — wait for confirmation in callback

    return response()->json(['link' => $data['data']['link']]);
}





public function fundWalletWithPaystack(Request $request)
{
    $request->validate(['amount' => 'required|numeric|min:100']);
    $user = auth()->user();
    $reference = Str::uuid();

    $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->post('https://api.paystack.co/transaction/initialize', [
        'email' => $user->email,
        'amount' => $request->amount * 100,
        'reference' => $reference,
        'callback_url' => route('payment.verify', ['reference' => $reference]),
    ]);

    return response()->json(['link' => $response['data']['authorization_url']]);
}

public function verifyPaystackPayment($reference)
{
    $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
        ->get("https://api.paystack.co/transaction/verify/{$reference}");

    if ($response['data']['status'] !== 'success') {
        return response()->json(['message' => 'Verification failed'], 400);
    }

    $email = $response['data']['customer']['email'];
    $amount = $response['data']['amount'] / 100;

    $user = User::where('email', $email)->first();
    if (!$user) return response()->json(['message' => 'User not found'], 404);

    $user->wallet_balance += $amount;
    $user->save();

    Transaction::create([
        'user_id' => $user->id,
        'amount' => $amount,
        'type' => 'credit',
        'reference' => $reference,
        'description' => 'Wallet Funding via Paystack',
    ]);

    return response()->json(['message' => 'Wallet funded successfully']);
}

public function payForTripWithPaystack(Request $request)
{
    $request->validate([
        'trip_id' => 'required|exists:trips,id',
        'method' => 'required|in:wallet,direct',
    ]);

    $user = auth()->user();
    $trip = Trip::findOrFail($request->trip_id);

    if ($trip->status === 'paid') {
        return response()->json(['message' => 'Trip already paid'], 400);
    }

    if ($request->method === 'wallet') {
        if ($user->wallet_balance < $trip->amount) {
            return response()->json(['message' => 'Insufficient wallet balance'], 400);
        }

        $user->wallet_balance -= $trip->amount;
        $user->save();

        $trip->status = 'paid';
        $trip->save();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $trip->amount,
            'type' => 'debit',
            'reference' => Str::uuid(),
            'description' => 'Trip payment from wallet',
        ]);

        return response()->json(['message' => 'Trip paid using wallet']);
    }

    // Direct Paystack payment
    $reference = Str::uuid();
    $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->post('https://api.paystack.co/transaction/initialize', [
        'email' => $user->email,
        'amount' => $trip->amount * 100,
        'reference' => $reference,
        'callback_url' => route('payment.verify', ['reference' => $reference]),
        // Optional: Use split payment if you’ve created subaccounts
    ]);

    return response()->json(['link' => $response['data']['authorization_url']]);
}

}

