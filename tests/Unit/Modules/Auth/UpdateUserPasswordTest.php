<?php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);


use App\Modules\Auth\Domain\Actions\UpdateUserPassword;
use App\Modules\User\Domain\Models\User;
use Illuminate\Support\Facades\Hash;

test('update user password action updates password successfully', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $action = new UpdateUserPassword();

    $action->update($user, [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue()
        ->and(Hash::check('old-password', $user->password))->toBeFalse();
});
