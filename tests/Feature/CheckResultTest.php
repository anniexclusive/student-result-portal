<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;
use App\Models\User;

test('home page loads successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

test('can check result with valid credentials', function () {
    $user = User::factory()->create();
    $result = Result::factory()->create(['exam_number' => 'EX123456789']);
    $pin = Pin::factory()->unused()->create([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
    ]);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX123456789',
    ]);

    $response->assertStatus(200)
        ->assertViewIs('pages.result')
        ->assertViewHas('student');

    // Verify PIN was marked as used
    $pin->refresh();
    expect($pin->use_status)->toBe('used')
        ->and($pin->result_id)->toBe($result->id)
        ->and($pin->count)->toBe(1);
});

test('cannot check result with invalid pin', function () {
    $user = User::factory()->create();
    Result::factory()->create(['exam_number' => 'EX987654321']);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => 'invalid',
        'serial_number' => 'invalid',
        'reg_number' => 'EX987654321',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors();
});

test('cannot check result with invalid exam number', function () {
    $user = User::factory()->create();
    Pin::factory()->unused()->create([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
    ]);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'INVALID',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('msg');
});

test('cannot check result with expired pin', function () {
    $user = User::factory()->create();
    $result = Result::factory()->create(['exam_number' => 'EX555555555']);
    Pin::factory()->expired()->create([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
    ]);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX555555555',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('msg');
});

test('cannot use pin used by different result', function () {
    $user = User::factory()->create();
    $result1 = Result::factory()->create(['exam_number' => 'EX111111111']);
    $result2 = Result::factory()->create(['exam_number' => 'EX222222222']);

    Pin::factory()->used($result1)->create([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
    ]);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX222222222',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('msg');
});

test('can reuse pin for same result', function () {
    $user = User::factory()->create();
    $result = Result::factory()->create(['exam_number' => 'EX333333333']);
    $pin = Pin::factory()->used($result)->create([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'count' => 2,
    ]);

    $response = $this->actingAs($user)->post('/check', [
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX333333333',
    ]);

    $response->assertStatus(200)
        ->assertViewIs('pages.result');

    // Verify PIN count was incremented
    $pin->refresh();
    expect($pin->count)->toBe(3);
});

test('validation fails without required fields', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/check', []);

    $response->assertSessionHasErrors(['pin', 'reg_number', 'serial_number']);
});
