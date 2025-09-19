<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Generate a temporary signed verification URL (valid for 60 minutes)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Build and return the custom email
        return (new MailMessage)
            ->subject('Verify Your Email Address - Kampus Rides')
            ->greeting('Hello ' . $notifiable->name . ' 👋,')
            ->line('Thank you for registering on Krides.')
            ->line('Please verify your email address by clicking the button below:')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Cheers, Krides API Team 🚀'); // removes default "Regards, Laravel"
    }



    /**
     * Get the array representation of the notification (optional).
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
