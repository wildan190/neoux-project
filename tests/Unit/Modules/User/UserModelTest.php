<?php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);


use App\Modules\User\Domain\Models\User;
use App\Modules\Company\Domain\Models\Company;

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

    expect(fn() => User::factory()->create(['email' => 'test@example.com']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('user password is hashed', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    expect($user->password)
        ->not->toBe('password123')
        ->and(\Hash::check('password123', $user->password))->toBeTrue();
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
    $user = User::factory()->create();

    expect($user)->toHaveMethod('sendEmailVerificationNotification');
});

test('user can send password reset notification', function () {
    $user = User::factory()->create();

    expect($user)->toHaveMethod('sendPasswordResetNotification');
});

test('user implements must verify email contract', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class);
});

test('user has two factor authentication trait', function () {
    $user = User::factory()->create();

    expect($user)->toHaveMethod('twoFactorQrCodeSvg');
});
