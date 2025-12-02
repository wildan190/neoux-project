<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use App\Modules\Auth\Domain\Actions\UpdateUserProfileInformation;
use App\Modules\User\Domain\Models\User;

test('update user profile information action updates user data', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $action = new UpdateUserProfileInformation;

    $action->update($user, [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com');
});

test('update user profile information does not change password', function () {
    $originalPassword = Hash::make('password');
    $user = User::factory()->create([
        'password' => $originalPassword,
    ]);

    $action = new UpdateUserProfileInformation;

    $action->update($user, [
        'name' => 'Updated Name',
        'email' => $user->email,
    ]);

    $user->refresh();

    expect($user->password)->toBe($originalPassword);
});
