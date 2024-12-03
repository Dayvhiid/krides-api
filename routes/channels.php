<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});




Broadcast::channel('trips.{tripId}', function ($user, $tripId) {
    // Authorize users based on their trip access
    return $user->trips->contains($tripId);
});
