<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;
use App\Services\ResultService;

test('can find result by exam number', function () {
    $result = Result::factory()->create([
        'exam_number' => 'EX444444444',
    ]);

    $service = new ResultService;
    $found = $service->findResultByExamNumber('EX444444444');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($result->id);
});

test('returns null for non-existent exam number', function () {
    $service = new ResultService;
    $found = $service->findResultByExamNumber('INVALID');

    expect($found)->toBeNull();
});

test('checks if result exists', function () {
    Result::factory()->create(['exam_number' => 'EX666666666']);

    $service = new ResultService;

    expect($service->resultExists('EX666666666'))->toBeTrue()
        ->and($service->resultExists('INVALID'))->toBeFalse();
});

test('can get result with related pins', function () {
    $result = Result::factory()->create();
    $pin = Pin::factory()->used($result)->create();

    $service = new ResultService;
    $found = $service->getResultWithPins($result->exam_number);

    expect($found)->not->toBeNull()
        ->and($found->pins)->toHaveCount(1)
        ->and($found->pins->first()->id)->toBe($pin->id);
});
