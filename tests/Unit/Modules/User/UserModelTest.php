<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;

test('user can be created with valid data', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com')
        ->and($user->password)->not->toBeNull();
});

test('user email must be unique', function () {
    User::factory()->create(['email' => 'test@example.com']);

    expect(fn () => User::factory()->create(['email' => 'test@example.com']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('user password is hashed', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    expect($user->password)
        ->not->toBe('password123')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

test('user has many companies', function () {
    $user = User::factory()->create();

    $company1 = Company::factory()->create(['user_id' => $user->id]);
    $company2 = Company::factory()->create(['user_id' => $user->id]);

    expect($user->companies)
        ->toHaveCount(2)
        ->and($user->companies->pluck('id')->toArray())
        ->toContain($company1->id, $company2->id);
});

test('user can send email verification notification', function () {
    $user = new User();

    // The sendEmailVerificationNotification method comes from the MustVerifyEmail contract/trait.
    // We check if the User model implements the contract.
    expect($user)->toBeInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class);

    // Also, let's check if it can actually send the notification.
    \Illuminate\Support\Facades\Notification::fake();
    $user = User::factory()->create();
    $user->sendEmailVerificationNotification();
    \Illuminate\Support\Facades\Notification::assertSentTo($user, VerifyEmail::class);
});

test('user can send password reset notification', function () {
    \Illuminate\Support\Facades\Notification::fake();
    $user = User::factory()->create();
    $user->sendPasswordResetNotification('fake-token');
    \Illuminate\Support\Facades\Notification::assertSentTo($user, ResetPassword::class);
});

test('user implements must verify email contract', function () {
    expect(new User())->toBeInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class);
});

test('user has two factor authentication trait', function () {
    $uses = class_uses_recursive(User::class);

    expect($uses)->toContain(\Laravel\Fortify\TwoFactorAuthenticatable::class);
});
