<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use Illuminate\Support\Facades\Hash;
use Modules\Auth\Domain\Actions\UpdateUserPassword;
use Modules\User\Domain\Models\User;

test('update user password action updates password successfully', function () {
    $user = User::factory()->create([
        'password' => 'old-password',
    ]);

    $this->actingAs($user);

    $action = new UpdateUserPassword;

    $action->update($user, [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue()
        ->and(Hash::check('old-password', $user->password))->toBeFalse();
});
