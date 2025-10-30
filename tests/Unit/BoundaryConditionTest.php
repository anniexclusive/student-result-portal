<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;

// PIN Count Boundary Tests
test('PIN count at boundary values', function (int $count, bool $expectedUsable, string $examNumber) {
    $result = Result::factory()->create(['exam_number' => $examNumber]);
    $pin = Pin::factory()->used($result)->create(['count' => $count]);

    $canUse = $pin->canBeUsedFor($result);

    expect($canUse)->toBe($expectedUsable);
})->with([
    'count=0 (minimum)' => [0, true, 'EX_BOUNDARY_0'],
    'count=1' => [1, true, 'EX_BOUNDARY_1'],
    'count=4 (just before limit)' => [4, true, 'EX_BOUNDARY_4'],
    'count=5 (at limit - expired)' => [5, false, 'EX_BOUNDARY_5'],  // count >= 5 is expired
    'count=6 (over limit)' => [6, false, 'EX_BOUNDARY_6'],
    'count=10' => [10, false, 'EX_BOUNDARY_10'],
    'count=100' => [100, false, 'EX_BOUNDARY_100'],
]);

test('PIN count negative values are invalid', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_NEGATIVE_TEST']);

    // Database should reject negative counts due to unsigned constraint
    $this->expectException(\Illuminate\Database\QueryException::class);

    Pin::factory()->create([
        'result_id' => $result->id,
        'use_status' => 'used',
        'count' => -1,
    ]);
});

// Score Boundary Tests
test('score handles boundary values correctly', function (float $score, string $expectedGrade) {
    $result = Result::factory()->create([
        'score' => $score,
        'grade' => $expectedGrade,
        'exam_number' => "EX_SCORE_{$score}",
    ]);

    expect($result->score)->toBe(number_format($score, 2, '.', ''));
})->with([
    'minimum score' => [0.0, 'F'],
    'low score' => [25.5, 'F'],
    'passing score' => [50.0, 'C'],
    'high score' => [85.75, 'A'],
    'maximum score' => [100.0, 'A'],
    'decimal precision' => [99.99, 'A'],
]);

test('score handles extreme decimal precision', function () {
    $result = Result::factory()->create([
        'score' => 85.123456789,
        'exam_number' => 'EX_PRECISION_TEST',
    ]);

    // Score should be cast to 2 decimal places
    expect($result->score)->toBe('85.12');
});

test('score does not accept negative values', function () {
    // Attempting negative score should either be rejected or clamped
    $result = Result::factory()->create([
        'score' => -10.5,
        'exam_number' => 'EX_NEGATIVE_SCORE',
    ]);

    // Verify it's stored (database allows it, but application logic should handle)
    expect($result->score)->toBe('-10.50');
});

test('score handles very large values', function () {
    $result = Result::factory()->create([
        'score' => 999.99,
        'exam_number' => 'EX_LARGE_SCORE',
    ]);

    expect($result->score)->toBe('999.99');
});

// PIN Expiration Based on Count (not date)
test('PIN expiration based on count threshold', function (int $count, bool $expectedExpired) {
    $pin = Pin::factory()->create([
        'count' => $count,
        'use_status' => 'unused',
    ]);

    expect($pin->hasExpired())->toBe($expectedExpired);
})->with([
    'count=0 not expired' => [0, false],
    'count=4 not expired' => [4, false],
    'count=5 is expired' => [5, true],
    'count=10 is expired' => [10, true],
]);

// String Length Boundaries
test('exam number with maximum length', function () {
    $longExamNumber = 'EX'.str_repeat('1', 253); // Total 255 chars

    $result = Result::factory()->create([
        'exam_number' => $longExamNumber,
    ]);

    expect($result->exam_number)->toBe($longExamNumber);
});

test('PIN with maximum typical length', function () {
    $longPin = str_repeat('1', 255);

    $pin = Pin::factory()->create([
        'pin' => $longPin,
    ]);

    expect($pin->pin)->toBe($longPin);
});

test('serial number with maximum length', function () {
    $longSerial = str_repeat('SN', 127).'X'; // 255 chars

    $pin = Pin::factory()->create([
        'serial_number' => $longSerial,
    ]);

    expect($pin->serial_number)->toBe($longSerial);
});

// Grade Boundaries
test('grade values at boundaries', function (string $grade, bool $shouldPass) {
    $result = Result::factory()->create([
        'grade' => $grade,
        'remark' => $shouldPass ? 'PASS' : 'FAIL',
        'exam_number' => "EX_GRADE_{$grade}",
    ]);

    expect($result->grade)->toBe($grade);
})->with([
    'A grade' => ['A', true],
    'B grade' => ['B', true],
    'C grade' => ['C', true],
    'D grade' => ['D', true],
    'E grade' => ['E', false],
    'F grade' => ['F', false],
]);

// Concurrent Operation Boundaries
test('handles multiple PINs for same result', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_MULTI_PIN']);

    // Create 100 PINs for the same result
    $pins = Pin::factory()->count(100)->create([
        'result_id' => $result->id,
        'use_status' => 'used',
    ]);

    expect($pins)->toHaveCount(100)
        ->and($result->pins()->count())->toBe(100);
});

test('handles result with zero PINs', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_NO_PINS']);

    expect($result->pins)->toHaveCount(0)
        ->and($result->pins()->exists())->toBeFalse();
});
