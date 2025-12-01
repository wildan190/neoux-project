<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Domain\Models\User;
use App\Modules\Auth\Application\Jobs\SendWelcomeEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function handle(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        SendWelcomeEmail::dispatch($user);

        Auth::login($user);

        return $user;
    }
}
