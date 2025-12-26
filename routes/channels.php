<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Modules.User.Domain.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});
