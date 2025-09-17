<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Trip;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

   
public function handleCallback(Request $request) {

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





// public function payForTrip(Request $request) {
     
//     $request->validate([
//         'trip_id' => 'required|exists:trips,id',
//         'method' => 'required|in:wallet,direct',
//     ]);

//     $user = auth()->user();
//     $trip = Trip::findOrFail($request->trip_id);

//     if ($trip->status === 'paid') {
//         return response()->json(['message' => 'Trip already paid'], 400);
//     }

//     // Fetch the driver
//     $driver = User::find($trip->driver_id);// subaccount_id
//     if (!$driver) {
//         return response()->json(['message' => 'Driver not found'], 404);
//     }

//     if ($request->method === 'wallet') {
//     if ($user->wallet_balance < $trip->amount) {
//         return response()->json(['message' => 'Insufficient wallet balance'], 400);
//     }

//     // Deduct from rider's wallet
//     $user->wallet_balance -= $trip->amount;
//     $user->save();

//     $platformShare = $trip->amount * 0.20;
//     $driverShare = $trip->amount * 0.80;

//     // Send 80% to driver's Flutterwave subaccount via Transfers API
//     $transfer = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/transfers', [
//         'account_bank' => 'flutterwave',  // pseudo bank code for subaccount transfer
//         'account_number' => $trip->subaccount_id, // YES: this is used here as ID
//         'amount' => $driverShare,
//         'currency' => 'NGN',
//         'reference' => Str::uuid(),
//         'narration' => 'Trip payment to driver',
//         // 'callback_url' => route('transfer.callback'), // optional
//         'debit_currency' => 'NGN',
//     ]);

//     if (!$transfer->successful()) {
//     $errorMessage = $transfer->json('message') ?? 'Unknown error from Flutterwave';
//     return response()->json([
//         'message' => 'Trip payment failed: Unable to transfer driver’s share.',
//         'details' => $errorMessage,
//     ], 500);
// }


//     // Mark trip as paid
//     $trip->status = 'paid';
//     $trip->save();

//     // Record transactions
//     Transaction::create([
//         'user_id' => $user->id,
//         'amount' => $trip->amount,
//         'type' => 'debit',
//         'reference' => Str::uuid(),
//         'description' => 'Trip payment from wallet',
//     ]);

//     Transaction::create([
//         'user_id' => $driver->id,
//         'amount' => $driverShare,
//         'type' => 'credit',
//         'reference' => Str::uuid(),
//         'description' => 'Trip earning paid via Flutterwave from wallet',
//     ]);

//     return response()->json(['message' => 'Trip paid via wallet (Flutterwave split)']);
// }


//     // Direct payment via Flutterwave with split
//     $reference = Str::uuid();
//     $flutterwaveResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
//         'tx_ref' => $reference,
//         'amount' => $trip->amount,
//         'currency' => 'NGN',
//         'redirect_url' => route('payment.callback'), // make sure this route exists
//         'customer' => [
//             'email' => $user->email,
//             'name' => $user->name,
//         ],
//         'subaccounts' => [
//             [
//                 'id' => $trip->subaccount_id,               // driver's subaccount
//                 'transaction_charge_type' => 'percentage',
//                 'transaction_charge' => 80                  // driver gets 80%
//             ]
//             // The platform (main account) gets 20% by default
//         ],
//         'customizations' => [
//             'title' => 'Trip Payment',
//             'description' => 'Pay for your trip',
//         ],
//     ]);

//     $data = $flutterwaveResponse->json();

//     if (!$flutterwaveResponse->successful() || !isset($data['data']['link'])) {
//         return response()->json(['message' => 'Payment initiation failed'], 500);
//     }

//     // Save temporary transaction for later callback validation if needed
//     // You can optionally create a PaymentIntent record here

//     return response()->json(['link' => $data['data']['link']]);
// }


public function payForTrip(Request $request)
{
    $request->validate([
        'trip_id' => 'required|exists:trips,id',
        'method'  => 'required|in:wallet,direct',
    ]);

    $user = auth()->user();
    $trip = Trip::findOrFail($request->trip_id);

    if ($trip->status === 'paid') {
        return response()->json(['message' => 'Trip already paid'], 400);
    }

    // Fetch the driver
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

        $platformShare = $trip->amount * 0.20;
        $driverShare   = $trip->amount * 0.80;

        // Send 80% directly to driver's bank account via Transfers API
        $transfer = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/transfers', [
            'account_bank'   => $driver->bank_code,        // e.g. "044" for Access Bank
            'account_number' => $driver->account_number,   // driver’s NUBAN account
            'amount'         => $driverShare,
            'currency'       => 'NGN',
            'reference'      => (string) Str::uuid(),
            'narration'      => 'Trip payment to driver',
            'debit_currency' => 'NGN',
        ]);

        if (!$transfer->successful()) {
            $errorMessage = $transfer->json('message') ?? 'Unknown error from Flutterwave';
            return response()->json([
                'message' => 'Trip payment failed: Unable to transfer driver’s share.',
                'details' => $errorMessage,
            ], 500);
        }

        // ? Update driver's wallet balance (for internal tracking)
        $driver->wallet_balance += $driverShare;
        $driver->save();

        // Mark trip as paid
        $trip->status = 'paid';
        $trip->save();

        // Record transactions
        Transaction::create([
            'user_id'    => $user->id,
            'amount'     => $trip->amount,
            'type'       => 'debit',
            'reference'  => (string) Str::uuid(),
            'description'=> 'Trip payment from wallet',
        ]);

        Transaction::create([
            'user_id'    => $driver->id,
            'amount'     => $driverShare,
            'type'       => 'credit',
            'reference'  => (string) Str::uuid(),
            'description'=> 'Trip earning paid directly to bank via Flutterwave',
        ]);

        return response()->json(['message' => 'Trip paid via wallet (direct bank transfer + wallet updated)']);
    }

    // If method is 'direct' (initiate Flutterwave hosted payment)
    $reference = (string) Str::uuid();
    $flutterwaveResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
        'tx_ref'       => $reference,
        'amount'       => $trip->amount,
        'currency'     => 'NGN',
        'redirect_url' => route('payment.callback'), // make sure this exists
        'customer'     => [
            'email' => $user->email,
            'name'  => $user->name,
        ],
        'customizations' => [
            'title'       => 'Trip Payment',
            'description' => 'Pay for your trip',
        ],
    ]);

    $data = $flutterwaveResponse->json();

    if (!$flutterwaveResponse->successful() || !isset($data['data']['link'])) {
        return response()->json(['message' => 'Payment initiation failed'], 500);
    }

    return response()->json(['link' => $data['data']['link']]);
}



public function initiatePayment(Request $request) {
    $request->validate([
        'trip_id' => 'required|exists:trips,id',
    ]);

    $user = auth()->user();
    $trip = Trip::findOrFail($request->trip_id);

    if ($trip->status === 'paid') {
        return response()->json(['message' => 'Trip already paid'], 400);
    }

    // Generate a unique tx_ref for this payment
    $txRef = Str::uuid();

    // Save tx_ref on trip to track payment later
    $trip->payment_reference = $txRef;
    $trip->save();

    // Flutterwave payment initialization
    $response = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/payments', [
        'tx_ref' => $txRef,
        'amount' => $trip->amount,
        'currency' => 'NGN',
        'redirect_url' => route('payment.callback'),
        'customer' => [
            'email' => $user->email,
            'name' => $user->name,
        ],
        'customizations' => [
            'title' => 'Trip Payment',
            'description' => 'Pay for your trip',
        ],
    ]);

    $data = $response->json();

    if (!$response->successful() || $data['status'] !== 'success') {
        return response()->json(['message' => 'Failed to initiate payment'], 500);
    }

    // Return payment link to frontend for redirect
    return response()->json([
        'payment_link' => $data['data']['link'],
        'message' => 'Payment initiated. Redirect user to this link.',
    ]);
}



public function handleFlutterwaveCallback(Request $request)
{
    $payload = $request->all();

    // You can log the payload to debug
    Log::info('Flutterwave webhook payload:', $payload);

    // Validate the payment status and transaction ref
    if ($payload['status'] !== 'successful') {
        return response()->json(['message' => 'Payment not successful'], 400);
    }

    $txRef = $payload['data']['tx_ref']; // This should be your trip payment reference
    $amountPaid = $payload['data']['amount'];

    // Find the trip by reference (assumes you saved the tx_ref on trip record)
    $trip = Trip::where('payment_reference', $txRef)->first();

    if (!$trip) {
        return response()->json(['message' => 'Trip not found'], 404);
    }

    // Check if already paid
    if ($trip->status === 'paid') {
        return response()->json(['message' => 'Trip already marked as paid'], 200);
    }

    // Mark trip as paid
    $trip->status = 'paid';
    $trip->save();

    // Calculate driver share (20%)
    $driverShare = $amountPaid * 0.20;

    // Initiate transfer to driver subaccount via Flutterwave API
    $transferResponse = Http::withToken(env('FLW_SECRET_KEY'))->post('https://api.flutterwave.com/v3/transfers', [
        'account_bank' => $trip->driver_bank_code,  // Driver's bank code, make sure this data is saved
        'account_number' => $trip->driver_account_number, // Driver's bank account number, also saved previously
        'amount' => $driverShare,
        'currency' => 'NGN',
        'reference' => 'driver_share_' . $trip->id . '_' . now()->timestamp,
        'narration' => 'Driver payment for trip ID ' . $trip->id,
        'callback_url' => route('transfer.callback'),  // Optional: if you want to handle transfer confirmation
        'debit_currency' => 'NGN',
    ]);

    $transferData = $transferResponse->json();

    if (!$transferResponse->successful() || $transferData['status'] !== 'success') {
        \Log::error('Driver transfer failed', $transferData);
        return response()->json(['message' => 'Failed to transfer driver share'], 500);
    }

    // Optionally store transfer details in DB here...

    return response()->json(['message' => 'Payment confirmed and driver paid']);
}







}