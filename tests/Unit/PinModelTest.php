<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;

test('pin has result relationship', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->used($result)->create();

    expect($pin->result)->not->toBeNull()
        ->and($pin->result->id)->toBe($result->id);
});

test('checks if pin is used', function () {
    $usedPin = Pin::factory()->used()->create();
    $unusedPin = Pin::factory()->unused()->create();

    expect($usedPin->isUsed())->toBeTrue()
        ->and($unusedPin->isUsed())->toBeFalse();
});

test('checks if pin has expired', function () {
    $expiredPin = Pin::factory()->expired()->create();
    $validPin = Pin::factory()->create(['count' => 3]);

    expect($expiredPin->hasExpired())->toBeTrue()
        ->and($validPin->hasExpired())->toBeFalse();
});

test('pin can be used for unused status', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->unused()->create();

    expect($pin->canBeUsedFor($result))->toBeTrue();
});

test('pin can be used for same result', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->used($result)->create(['count' => 2]);

    expect($pin->canBeUsedFor($result))->toBeTrue();
});

test('pin cannot be used for different result', function () {
    $result1 = Result::factory()->create();
    $result2 = Result::factory()->create();
    $pin = Pin::factory()->used($result1)->create();

    expect($pin->canBeUsedFor($result2))->toBeFalse();
});

test('expired pin cannot be used', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->expired()->create();

    expect($pin->canBeUsedFor($result))->toBeFalse();
});
