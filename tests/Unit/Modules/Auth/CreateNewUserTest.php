<?php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);


use App\Modules\Auth\Domain\Actions\CreateNewUser;
use App\Modules\User\Domain\Models\User;

test('create new user action creates user successfully', function () {
    $action = new CreateNewUser();

    $userData = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
    ];

    $user = $action->create($userData);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->email)->toBe('jane@example.com')
        ->and(\Hash::check('SecurePassword123!', $user->password))->toBeTrue();
});

test('create new user action hashes password', function () {
    $action = new CreateNewUser();

    $user = $action->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    expect($user->password)->not->toBe('password');
});
