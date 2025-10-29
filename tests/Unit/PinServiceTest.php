<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;
use App\Services\PinService;

test('can find pin by credentials', function () {
    $pinCode = fake()->numerify('########');
    $serialNumber = fake()->numerify('SN########');

    $pin = Pin::factory()->create([
        'pin' => $pinCode,
        'serial_number' => $serialNumber,
    ]);

    $service = new PinService;
    $found = $service->findPin($pinCode, $serialNumber);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($pin->id);
});

test('returns null for non-existent pin', function () {
    $service = new PinService;
    $found = $service->findPin('invalid', 'invalid');

    expect($found)->toBeNull();
});

test('validates pin usage correctly', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->unused()->create();

    $service = new PinService;
    $validation = $service->validatePinUsage($pin, $result);

    expect($validation['valid'])->toBeTrue();
});

test('rejects expired pin', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->expired()->create();

    $service = new PinService;
    $validation = $service->validatePinUsage($pin, $result);

    expect($validation['valid'])->toBeFalse()
        ->and($validation['message'])->toBe('PIN has expired!!');
});

test('rejects pin used by different result', function () {
    $result1 = Result::factory()->create();
    $result2 = Result::factory()->create();
    $pin = Pin::factory()->used($result1)->create(['count' => 2]);

    $service = new PinService;
    $validation = $service->validatePinUsage($pin, $result2);

    expect($validation['valid'])->toBeFalse()
        ->and($validation['message'])->toBe('PIN used by another user!');
});

test('marks pin as used', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->unused()->create();

    $service = new PinService;
    $updatedPin = $service->markPinAsUsed($pin, $result);

    expect($updatedPin->use_status)->toBe('used')
        ->and($updatedPin->result_id)->toBe($result->id)
        ->and($updatedPin->count)->toBe(1);
});

test('increments pin count on each use', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->used($result)->create(['count' => 2]);

    $service = new PinService;
    $updatedPin = $service->markPinAsUsed($pin, $result);

    expect($updatedPin->count)->toBe(3);
});

test('validateAndUsePinForResult returns success for valid unused pin', function () {
    $pinCode = fake()->numerify('########');
    $serialNumber = fake()->numerify('SN########');

    $result = Result::factory()->create();
    $pin = Pin::factory()->unused()->create([
        'pin' => $pinCode,
        'serial_number' => $serialNumber,
    ]);

    $service = new PinService;
    $response = $service->validateAndUsePinForResult($pinCode, $serialNumber, $result);

    expect($response['success'])->toBeTrue()
        ->and($response)->toHaveKey('pin');

    $pin->refresh();
    expect($pin->use_status)->toBe('used')
        ->and($pin->result_id)->toBe($result->id)
        ->and($pin->count)->toBe(1);
});

test('validateAndUsePinForResult returns error for non-existent pin', function () {
    $result = Result::factory()->create();
    $service = new PinService;
    $response = $service->validateAndUsePinForResult('invalid', 'invalid', $result);

    expect($response['success'])->toBeFalse()
        ->and($response['message'])->toBe('Invalid PIN and Serial Number');
});

test('validateAndUsePinForResult returns error for expired pin', function () {
    $pinCode = fake()->numerify('########');
    $serialNumber = fake()->numerify('SN########');

    $result = Result::factory()->create();
    $pin = Pin::factory()->expired()->create([
        'pin' => $pinCode,
        'serial_number' => $serialNumber,
    ]);

    $service = new PinService;
    $response = $service->validateAndUsePinForResult($pinCode, $serialNumber, $result);

    expect($response['success'])->toBeFalse()
        ->and($response['message'])->toBe('PIN has expired!!');
});

test('validateAndUsePinForResult allows reuse of pin for same result', function () {
    $pinCode = fake()->numerify('########');
    $serialNumber = fake()->numerify('SN########');

    $result = Result::factory()->create();
    $pin = Pin::factory()->used($result)->create([
        'pin' => $pinCode,
        'serial_number' => $serialNumber,
        'count' => 2,
    ]);

    $service = new PinService;
    $response = $service->validateAndUsePinForResult($pinCode, $serialNumber, $result);

    expect($response['success'])->toBeTrue();

    $pin->refresh();
    expect($pin->count)->toBe(3);
});
