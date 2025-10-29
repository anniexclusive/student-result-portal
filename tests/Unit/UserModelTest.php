<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can be created', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->exists)->toBeTrue();
});

test('user password is hidden', function () {
    $user = User::factory()->create();

    expect($user->toArray())->not->toHaveKey('password');
});

test('user remember token is hidden', function () {
    $user = User::factory()->create();

    expect($user->toArray())->not->toHaveKey('remember_token');
});

test('user email is verified', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->not->toBeNull();
});
