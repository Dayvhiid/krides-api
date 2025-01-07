<?php

namespace App\Otp;
use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class UserRegistrationOtp implements Otp
{
    /**
     * Initiates the OTP with user detail
     *
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        protected string $lastName,
        protected string $firstName,
        protected string $email,
        protected string $password
    ) {
    }

    /**
     * Creates the user
     */
    public function process()
    {
        /** @var User */
        $user = User::unguarded(function () {
            return User::create([
                'firstName'                  => $this->firstName,
                'lastName'                  => $this->lastName,
                'email'                 => $this->email,
                'password'              => Hash::make($this->password),
                'email_verified_at'     => now(),
            ]);
        });

        event(new Registered($user));
        
        Auth::login($user);

        return [
            'user' => $user
        ];
    }
}


