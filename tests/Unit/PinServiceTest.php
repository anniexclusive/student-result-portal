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
